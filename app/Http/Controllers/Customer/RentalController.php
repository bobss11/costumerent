<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Costume;
use App\Models\Rental;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RentalController extends Controller
{
    /**
     * List the authenticated customer's rentals, newest first.
     */
    public function index(): View
    {
        $rentals = Rental::query()
            ->with(['costume', 'returnInspection', 'invoice'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('customer.rentals', [
            'rentals' => $rentals,
        ]);
    }

    /**
     * Create a reservation (rental) for the authenticated customer.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'costume_id' => ['required', 'integer', 'exists:costumes,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        $costume = Costume::where('is_active', true)->findOrFail($validated['costume_id']);

        $qty = (int) $validated['qty'];

        // Stock availability check (stock is NOT decremented here; that happens at pickup).
        if ($qty > $costume->stock_available) {
            throw ValidationException::withMessages([
                'qty' => "Stok tersedia hanya {$costume->stock_available} unit untuk kostum ini.",
            ]);
        }

        $start = Carbon::parse($validated['start_date'])->startOfDay();
        $end = Carbon::parse($validated['end_date'])->startOfDay();

        // Inclusive-day duration (same start and end date counts as 1 day).
        $totalDays = $start->diffInDays($end) + 1;

        $rentalSubtotal = (float) $costume->rental_price_per_day * $totalDays * $qty;
        $depositTotal = (float) $costume->deposit_amount * $qty;
        $grandTotal = $rentalSubtotal + $depositTotal;

        $rental = Rental::create([
            'rental_code' => $this->generateRentalCode(),
            'user_id' => Auth::id(),
            'costume_id' => $costume->id,
            'qty' => $qty,
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'total_days' => $totalDays,
            'rental_subtotal' => $rentalSubtotal,
            'deposit_total' => $depositTotal,
            'grand_total' => $grandTotal,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        return redirect()
            ->route('customer.rentals.index')
            ->with('status', "Reservasi berhasil dibuat dengan kode {$rental->rental_code}. Silakan lakukan pembayaran saat pengambilan.");
    }

    /**
     * Generate a unique rental code in the form RNT-YYYYMM-### for the current month.
     */
    protected function generateRentalCode(): string
    {
        $period = now()->format('Ym');
        $prefix = 'RNT-'.$period.'-';

        $count = Rental::where('rental_code', 'like', $prefix.'%')->count();

        do {
            $count++;
            $code = $prefix.str_pad((string) $count, 3, '0', STR_PAD_LEFT);
        } while (Rental::where('rental_code', $code)->exists());

        return $code;
    }
}
