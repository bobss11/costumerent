{{--
    Reusable status pill.

    Usage:
        @include('partials.status-badge', ['status' => $rental->status])
        @include('partials.status-badge', ['status' => $rental->payment_status, 'type' => 'payment'])

    Params:
        - status (string) required: the rental status, payment status, or inspection condition.
        - type   (string) optional: 'rental' (default), 'payment', or 'condition'.
--}}
@php
    $type = $type ?? 'rental';

    $labels = [
        // Rental statuses.
        'pending'                => 'Pending',
        'confirmed'              => 'Confirmed',
        'picked_up'              => 'Picked Up',
        'returned'               => 'Returned',
        'completed'              => 'Completed',
        'overdue'                => 'Overdue',
        'cancelled'              => 'Cancelled',
        // Payment statuses.
        'unpaid'                 => 'Unpaid',
        'paid'                   => 'Paid',
        // Inspection conditions.
        'good'                   => 'Good',
        'minor_stain'            => 'Minor Stain',
        'damaged'                => 'Damaged',
        'incomplete_accessories' => 'Incomplete',
    ];

    // Pastel pill palettes straight from the spec.
    $amber   = 'bg-amber-50 text-amber-700 border-amber-200';
    $sky     = 'bg-sky-50 text-sky-700 border-sky-200';
    $indigo  = 'bg-indigo-50 text-indigo-700 border-indigo-200';
    $emerald = 'bg-emerald-50 text-emerald-700 border-emerald-200';
    $rose    = 'bg-rose-50 text-rose-700 border-rose-200';
    $slate   = 'bg-slate-100 text-slate-600 border-slate-200';

    $classes = [
        // Rental statuses.
        'pending'                => $amber,
        'confirmed'              => $sky,
        'picked_up'              => $indigo,
        'returned'               => $emerald,
        'completed'              => $emerald,
        'overdue'                => $rose,
        'cancelled'              => $slate,
        // Payment statuses.
        'unpaid'                 => $amber,
        'paid'                   => $emerald,
        // Inspection conditions.
        'good'                   => $emerald,
        'minor_stain'            => $amber,
        'damaged'                => $rose,
        'incomplete_accessories' => $rose,
    ];

    $key = (string) $status;
    $pill = $classes[$key] ?? $slate;
    $label = $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
@endphp
<span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $pill }}">
    {{ $label }}
</span>
