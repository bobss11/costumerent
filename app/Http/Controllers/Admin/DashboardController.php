<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Costume;
use App\Models\Invoice;
use App\Models\Rental;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Executive KPI dashboard + pickup/return operational queues.
     */
    public function index(): View
    {
        $today = Carbon::today();

        // KPI 1: Total rental revenue — money actually collected via invoices.
        $totalRevenue = (float) Invoice::sum('amount_paid');

        // KPI 2: Total deposit currently held — deposits of rentals still out
        // (picked_up / overdue) that have NOT yet been settled by a return inspection.
        $heldDeposits = (float) Rental::whereIn('status', ['picked_up', 'overdue'])
            ->whereDoesntHave('returnInspection')
            ->sum('deposit_total');

        // KPI 3: Costumes currently rented out (unit count across active rentals).
        $costumesRentedOut = (int) Rental::whereIn('status', ['picked_up', 'overdue'])
            ->whereDoesntHave('returnInspection')
            ->sum('qty');

        // KPI 4: Items due or overdue today — still out and due on/before today.
        $dueTodayAlert = (int) Rental::whereIn('status', ['picked_up', 'overdue'])
            ->whereDoesntHave('returnInspection')
            ->whereDate('end_date', '<=', $today)
            ->count();

        // Pickup queue: reservations waiting to be handed over.
        $pickupQueue = Rental::with(['user', 'costume'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('start_date')
            ->get();

        // Return queue: costumes currently out and awaiting return inspection.
        $returnQueue = Rental::with(['user', 'costume'])
            ->whereIn('status', ['picked_up', 'overdue'])
            ->whereDoesntHave('returnInspection')
            ->orderBy('end_date')
            ->get();

        return view('admin.dashboard', [
            'totalRevenue' => $totalRevenue,
            'heldDeposits' => $heldDeposits,
            'costumesRentedOut' => $costumesRentedOut,
            'dueTodayAlert' => $dueTodayAlert,
            'pickupQueue' => $pickupQueue,
            'returnQueue' => $returnQueue,
            'today' => $today,
            'totalCostumes' => Costume::count(),
        ]);
    }
}
