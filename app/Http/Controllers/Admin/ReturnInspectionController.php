<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\ReturnInspection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class ReturnInspectionController extends Controller
{
    /**
     * Show the return inspection / deposit settlement form.
     * Only valid for rentals that have been picked up (still out).
     */
    public function create(Rental $rental): View|RedirectResponse
    {
        if (! in_array($rental->status, ['picked_up', 'overdue'], true)) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', "Rental {$rental->rental_code} tidak dapat diinspeksi (status: {$rental->status}).");
        }

        $rental->load(['user', 'costume']);

        return view('admin.return_inspection', [
            'rental' => $rental,
        ]);
    }

    /**
     * Compute the deposit settlement and persist the inspection.
     * Restores stock and marks the rental as returned.
     */
    public function store(Request $request, Rental $rental): RedirectResponse
    {
        if (! in_array($rental->status, ['picked_up', 'overdue'], true)) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', "Rental {$rental->rental_code} tidak dapat diinspeksi (status: {$rental->status}).");
        }

        $validated = $request->validate([
            'actual_return_date' => ['required', 'date'],
            'condition_status' => ['required', 'in:good,minor_stain,damaged,incomplete_accessories'],
            'damage_penalty_fee' => ['required', 'numeric', 'min:0'],
            'inspection_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $rental->loadMissing('costume');
        $costume = $rental->costume;

        $endDate = $rental->end_date->copy()->startOfDay();
        $actualReturn = Carbon::parse($validated['actual_return_date'])->startOfDay();

        // late_days = whole days the return is past the agreed end date (never negative).
        $lateDays = max(0, $endDate->diffInDays($actualReturn, false));

        $lateFeeAmount = $lateDays * (float) $costume->late_fee_per_day * (int) $rental->qty;
        $damagePenalty = (float) $validated['damage_penalty_fee'];
        $totalDeduction = $lateFeeAmount + $damagePenalty;

        $depositTotal = (float) $rental->deposit_total;
        $depositRefunded = max(0, $depositTotal - $totalDeduction);
        $extraCharge = max(0, $totalDeduction - $depositTotal);

        DB::transaction(function () use (
            $rental,
            $costume,
            $validated,
            $actualReturn,
            $lateDays,
            $lateFeeAmount,
            $damagePenalty,
            $totalDeduction,
            $depositRefunded,
            $extraCharge
        ) {
            ReturnInspection::create([
                'rental_id' => $rental->id,
                'checked_by_user_id' => Auth::id(),
                'actual_return_date' => $actualReturn->toDateString(),
                'late_days' => $lateDays,
                'late_fee_amount' => $lateFeeAmount,
                'condition_status' => $validated['condition_status'],
                'inspection_notes' => $validated['inspection_notes'] ?? null,
                'damage_penalty_fee' => $damagePenalty,
                'total_deduction' => $totalDeduction,
                'deposit_refunded' => $depositRefunded,
                'extra_charge' => $extraCharge,
            ]);

            $rental->update(['status' => 'returned']);

            // Stock restored here at return — the single source of truth.
            $costume->increment('stock_available', $rental->qty);
        });

        $message = "Inspeksi {$rental->rental_code} selesai. Deposit dikembalikan ".rupiah($depositRefunded)
            .($extraCharge > 0 ? ', tambahan tagihan '.rupiah($extraCharge).'.' : '.');

        if (Route::has('invoice.show')) {
            return redirect()
                ->route('invoice.show', $rental->rental_code)
                ->with('status', $message);
        }

        return redirect()
            ->route('admin.dashboard')
            ->with('status', $message);
    }
}
