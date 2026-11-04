<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Rental;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PickupController extends Controller
{
    /**
     * Show the pickup / POS verification form for a rental.
     * Only valid for rentals waiting in the pickup queue.
     */
    public function edit(Rental $rental): View|RedirectResponse
    {
        if (! in_array($rental->status, ['pending', 'confirmed'], true)) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', "Rental {$rental->rental_code} tidak dapat diproses untuk pengambilan (status: {$rental->status}).");
        }

        $rental->load(['user', 'costume']);

        return view('admin.pickup', [
            'rental' => $rental,
        ]);
    }

    /**
     * Confirm payment, record the held guarantee card + initial condition,
     * decrement stock, set the rental to picked_up, and issue an invoice.
     */
    public function update(Request $request, Rental $rental): RedirectResponse
    {
        if (! in_array($rental->status, ['pending', 'confirmed'], true)) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', "Rental {$rental->rental_code} tidak dapat diproses untuk pengambilan (status: {$rental->status}).");
        }

        $validated = $request->validate([
            'guarantee_card_held' => ['required', 'string', 'max:255'],
            'pickup_condition_notes' => ['nullable', 'string', 'max:2000'],
            'payment_method' => ['required', 'in:cash,qris,transfer'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
        ]);

        $rental->loadMissing('costume');
        $costume = $rental->costume;

        // Stock guard: cannot hand over more units than are available.
        if ($costume->stock_available < $rental->qty) {
            throw ValidationException::withMessages([
                'amount_paid' => "Stok kostum tidak mencukupi (tersedia {$costume->stock_available}, dibutuhkan {$rental->qty}).",
            ]);
        }

        // Must at least cover the grand total (rental + deposit).
        if ((float) $validated['amount_paid'] < (float) $rental->grand_total) {
            throw ValidationException::withMessages([
                'amount_paid' => 'Jumlah pembayaran kurang dari total tagihan '.rupiah($rental->grand_total).'.',
            ]);
        }

        DB::transaction(function () use ($rental, $costume, $validated) {
            $rental->update([
                'guarantee_card_held' => $validated['guarantee_card_held'],
                'pickup_condition_notes' => $validated['pickup_condition_notes'] ?? null,
                'status' => 'picked_up',
                'payment_status' => 'paid',
            ]);

            // Stock is decremented here at pickup — the single source of truth.
            $costume->decrement('stock_available', $rental->qty);

            $amountPaid = (float) $validated['amount_paid'];
            $changeAmount = max(0, $amountPaid - (float) $rental->grand_total);

            Invoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'rental_id' => $rental->id,
                'payment_method' => $validated['payment_method'],
                'amount_paid' => $amountPaid,
                'change_amount' => $changeAmount,
                'issued_at' => Carbon::now(),
            ]);
        });

        $message = "Pengambilan {$rental->rental_code} berhasil diproses. Pembayaran lunas & stok diperbarui.";

        if (Route::has('invoice.show')) {
            return redirect()
                ->route('invoice.show', $rental->rental_code)
                ->with('status', $message);
        }

        return redirect()
            ->route('admin.dashboard')
            ->with('status', $message);
    }

    /**
     * Generate a unique invoice number: INV-YYYYMM-### (sequential per month).
     */
    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV-'.Carbon::now()->format('Ym').'-';

        do {
            $count = Invoice::where('invoice_number', 'like', $prefix.'%')->count();
            $candidate = $prefix.str_pad((string) ($count + 1), 3, '0', STR_PAD_LEFT);
            $count++;
        } while (Invoice::where('invoice_number', $candidate)->exists());

        return $candidate;
    }
}
