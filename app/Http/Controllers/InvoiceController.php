<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    /**
     * Render the print-ready invoice / handover proof for a rental.
     *
     * Resolved by rental_code (not id). Viewable only by the owning
     * customer or an admin/staff user; everyone else is forbidden.
     */
    public function show(string $rental_code): View
    {
        $rental = Rental::with(['user', 'costume', 'returnInspection', 'invoice'])
            ->where('rental_code', $rental_code)
            ->firstOrFail();

        $user = Auth::user();

        $isOwner = (int) $rental->user_id === (int) $user->id;
        $isStaff = $user->isAdmin() || $user->isStaff();

        abort_unless($isOwner || $isStaff, 403);

        return view('invoice', [
            'rental' => $rental,
            'invoice' => $rental->invoice,
            'inspection' => $rental->returnInspection,
        ]);
    }
}
