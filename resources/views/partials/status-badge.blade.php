{{--
    Reusable status pill (editorial boutique design system).

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

    // Warm-minimalist boutique pill palettes (pill + matching dot colour).
    $amber   = ['pill' => 'bg-amber-50 text-amber-800 border-amber-200/80',   'dot' => 'bg-amber-500'];
    $sky     = ['pill' => 'bg-sky-50 text-sky-800 border-sky-200/80',         'dot' => 'bg-sky-500'];
    $obsidian= ['pill' => 'bg-zinc-900 text-white border-zinc-900',           'dot' => 'bg-white/80'];
    $emerald = ['pill' => 'bg-emerald-50 text-emerald-800 border-emerald-200/80', 'dot' => 'bg-emerald-500'];
    $rose    = ['pill' => 'bg-rose-50 text-rose-800 border-rose-200/80',      'dot' => 'bg-rose-500'];
    $stone   = ['pill' => 'bg-stone-100 text-stone-600 border-stone-200',     'dot' => 'bg-stone-400'];

    $styles = [
        // Rental statuses.
        'pending'                => $amber,
        'confirmed'              => $sky,
        'picked_up'              => $obsidian,
        'returned'               => $emerald,
        'completed'              => $emerald,
        'overdue'                => $rose,
        'cancelled'              => $rose,
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
    $style = $styles[$key] ?? $stone;
    $label = $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
@endphp
<span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-medium tracking-tight {{ $style['pill'] }}">
    <span class="h-1.5 w-1.5 rounded-full {{ $style['dot'] }}"></span>
    {{ $label }}
</span>
