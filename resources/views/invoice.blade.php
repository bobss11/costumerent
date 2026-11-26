<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nota {{ $rental->rental_code }} — CostumeRent</title>

    {{-- Tailwind CSS (CDN) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { canvas: '#FAFAF9', accent: '#D97706' },
                    fontFamily: {
                        display: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        sans: ['"Inter"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                },
            },
        };
    </script>

    {{-- Google Fonts: Plus Jakarta Sans (display) + Inter (body) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        h1, .font-display { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; letter-spacing: -0.02em; }
        .tabular-nums { font-variant-numeric: tabular-nums; }

        @media print {
            .no-print { display: none !important; }
            body { background: #ffffff !important; }
            .invoice-sheet {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                max-width: none !important;
                width: 100% !important;
            }
            @page { margin: 14mm; }
        }
    </style>
</head>
<body class="bg-stone-100 text-stone-700">
    @php
        $costume = $rental->costume;
        $accessories = array_filter(array_map('trim', explode(',', $costume->accessories_included ?? '')));
    @endphp

    {{-- Toolbar (hidden when printing) --}}
    <div class="no-print sticky top-0 z-10 border-b border-stone-200 bg-white/90 backdrop-blur">
        <div class="mx-auto flex max-w-3xl items-center justify-between px-4 py-3">
            <a href="{{ url()->previous() }}"
               class="inline-flex items-center gap-1.5 rounded-lg border border-stone-200 px-3.5 py-2 text-sm font-semibold text-stone-700 transition hover:bg-stone-50">
                &larr; Kembali
            </a>
            <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-2 rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-zinc-800">
                🖨 Cetak Nota
            </button>
        </div>
    </div>

    <main class="mx-auto my-8 max-w-3xl px-4">
        <div class="invoice-sheet overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-[0_2px_12px_-4px_rgba(0,0,0,0.04)]">
            {{-- Store header / kop --}}
            <header class="flex flex-col gap-4 border-b border-stone-200 p-8 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex items-start gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-zinc-900 text-xl font-extrabold text-white">C</div>
                    <div>
                        <h1 class="font-display text-2xl font-extrabold tracking-tight text-zinc-900">CostumeRent</h1>
                        <p class="text-sm text-stone-500">Jasa Persewaan Kostum &amp; Busana Adat</p>
                        <p class="mt-0.5 text-xs text-stone-400">Jl. Malioboro No. 1, Yogyakarta &middot; 0812-3456-7890</p>
                    </div>
                </div>
                <div class="text-left sm:text-right">
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-400">Nota Sewa</p>
                    <p class="font-mono text-lg font-bold tabular-nums text-accent">{{ $rental->rental_code }}</p>
                    @if($invoice)
                        <p class="mt-1 text-xs text-stone-500">No. Invoice: <span class="font-mono font-semibold text-stone-700">{{ $invoice->invoice_number }}</span></p>
                        <p class="text-xs text-stone-500">{{ optional($invoice->issued_at)->translatedFormat('d M Y, H:i') }}</p>
                    @endif
                </div>
            </header>

            {{-- Customer + rental meta --}}
            <section class="grid gap-6 border-b border-stone-200 p-8 sm:grid-cols-2">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-400">Detail Pelanggan</p>
                    <p class="mt-2 text-base font-bold text-zinc-900">{{ $rental->user->name }}</p>
                    @if($rental->user->phone_number)
                        <p class="text-sm text-stone-600">{{ $rental->user->phone_number }}</p>
                    @endif
                    @if($rental->user->identity_type || $rental->user->identity_number)
                        <p class="text-sm text-stone-600">{{ $rental->user->identity_type }} {{ $rental->user->identity_number }}</p>
                    @endif
                    @if($rental->user->address)
                        <p class="mt-1 text-sm text-stone-500">{{ $rental->user->address }}</p>
                    @endif
                </div>
                <div class="sm:text-right">
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-400">Detail Sewa</p>
                    <p class="mt-2 text-sm text-stone-600">Mulai: <span class="font-medium text-zinc-900">{{ $rental->start_date->translatedFormat('d M Y') }}</span></p>
                    <p class="text-sm text-stone-600">Selesai: <span class="font-medium text-zinc-900">{{ $rental->end_date->translatedFormat('d M Y') }}</span></p>
                    <p class="text-sm text-stone-600">Durasi: <span class="font-medium text-zinc-900">{{ $rental->total_days }} hari</span></p>
                    <p class="text-sm text-stone-600">Status: <span class="font-medium capitalize text-zinc-900">{{ str_replace('_', ' ', $rental->status) }}</span></p>
                </div>
            </section>

            {{-- Item + accessories --}}
            <section class="border-b border-stone-200 p-8">
                <p class="text-xs font-semibold uppercase tracking-wide text-stone-400">Item Kostum</p>
                <table class="mt-3 w-full text-sm">
                    <thead>
                        <tr class="border-b border-stone-200 text-left text-xs uppercase tracking-wide text-stone-400">
                            <th class="py-2">Kostum</th>
                            <th class="py-2 text-center">Qty</th>
                            <th class="py-2 text-center">Hari</th>
                            <th class="py-2 text-right">Harga/Hari</th>
                            <th class="py-2 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-stone-100 align-top">
                            <td class="py-3">
                                <p class="font-semibold text-zinc-900">{{ $costume->name }}</p>
                                <p class="text-xs text-stone-500">{{ $costume->code }} &middot; Ukuran {{ $costume->size }}</p>
                                @if(count($accessories))
                                    <div class="mt-1.5 flex flex-wrap gap-1.5">
                                        @foreach($accessories as $item)
                                            <span class="inline-flex items-center rounded-md bg-stone-100 px-2 py-0.5 text-xs font-medium text-stone-600">{{ $item }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 text-center text-stone-700">{{ $rental->qty }}</td>
                            <td class="py-3 text-center text-stone-700">{{ $rental->total_days }}</td>
                            <td class="py-3 text-right text-stone-700">{{ rupiah($costume->rental_price_per_day) }}</td>
                            <td class="py-3 text-right font-semibold text-zinc-900">{{ rupiah($rental->rental_subtotal) }}</td>
                        </tr>
                    </tbody>
                </table>
            </section>

            {{-- Cost breakdown --}}
            <section class="border-b border-stone-200 p-8">
                <div class="ml-auto w-full space-y-2 text-sm sm:max-w-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-stone-500">Subtotal Sewa</span>
                        <span class="font-medium text-zinc-900">{{ rupiah($rental->rental_subtotal) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-stone-500">Deposit Jaminan</span>
                        <span class="font-medium text-amber-600">{{ rupiah($rental->deposit_total) }}</span>
                    </div>
                    <div class="flex items-center justify-between border-t border-stone-200 pt-2">
                        <span class="font-bold text-zinc-900">Grand Total</span>
                        <span class="text-base font-extrabold tabular-nums text-zinc-900">{{ rupiah($rental->grand_total) }}</span>
                    </div>
                </div>
            </section>

            {{-- Payment info (when an invoice exists) --}}
            @if($invoice)
                <section class="border-b border-stone-200 bg-stone-50 p-8">
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-400">Informasi Pembayaran</p>
                    <div class="mt-3 grid gap-3 text-sm sm:grid-cols-4">
                        <div>
                            <p class="text-stone-400">Metode</p>
                            <p class="mt-0.5 font-semibold capitalize text-zinc-900">{{ $invoice->payment_method }}</p>
                        </div>
                        <div>
                            <p class="text-stone-400">Dibayar</p>
                            <p class="mt-0.5 font-semibold text-zinc-900">{{ rupiah($invoice->amount_paid) }}</p>
                        </div>
                        <div>
                            <p class="text-stone-400">Kembalian</p>
                            <p class="mt-0.5 font-semibold text-zinc-900">{{ rupiah($invoice->change_amount) }}</p>
                        </div>
                        <div>
                            <p class="text-stone-400">Tanggal Bayar</p>
                            <p class="mt-0.5 font-semibold text-zinc-900">{{ optional($invoice->issued_at)->translatedFormat('d M Y, H:i') }}</p>
                        </div>
                    </div>
                </section>
            @endif

            {{-- Return inspection results (when present) --}}
            @if($inspection)
                <section class="border-b border-stone-200 p-8">
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-400">Hasil Inspeksi Pengembalian</p>
                    <div class="mt-3 grid gap-3 text-sm sm:grid-cols-3">
                        <div>
                            <p class="text-stone-400">Tgl. Kembali Aktual</p>
                            <p class="mt-0.5 font-semibold text-zinc-900">{{ $inspection->actual_return_date->translatedFormat('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-stone-400">Keterlambatan</p>
                            <p class="mt-0.5 font-semibold text-zinc-900">{{ $inspection->late_days }} hari</p>
                        </div>
                        <div>
                            <p class="text-stone-400">Denda Keterlambatan</p>
                            <p class="mt-0.5 font-semibold text-zinc-900">{{ rupiah($inspection->late_fee_amount) }}</p>
                        </div>
                        <div>
                            <p class="text-stone-400">Kondisi</p>
                            <p class="mt-0.5 font-semibold capitalize text-zinc-900">{{ str_replace('_', ' ', $inspection->condition_status) }}</p>
                        </div>
                        <div>
                            <p class="text-stone-400">Denda Kerusakan</p>
                            <p class="mt-0.5 font-semibold text-zinc-900">{{ rupiah($inspection->damage_penalty_fee) }}</p>
                        </div>
                        <div>
                            <p class="text-stone-400">Total Potongan</p>
                            <p class="mt-0.5 font-semibold text-zinc-900">{{ rupiah($inspection->total_deduction) }}</p>
                        </div>
                        <div>
                            <p class="text-stone-400">Deposit Dikembalikan</p>
                            <p class="mt-0.5 font-bold text-emerald-600">{{ rupiah($inspection->deposit_refunded) }}</p>
                        </div>
                        <div>
                            <p class="text-stone-400">Tagihan Tambahan</p>
                            <p class="mt-0.5 font-bold {{ $inspection->extra_charge > 0 ? 'text-rose-600' : 'text-zinc-900' }}">{{ rupiah($inspection->extra_charge) }}</p>
                        </div>
                    </div>
                    @if($inspection->inspection_notes)
                        <p class="mt-3 text-xs text-stone-500">Catatan: {{ $inspection->inspection_notes }}</p>
                    @endif
                </section>
            @endif

            {{-- Signatures for handover --}}
            <section class="p-8">
                <div class="grid grid-cols-2 gap-8 text-center text-sm">
                    <div>
                        <p class="text-stone-500">Penyewa</p>
                        <div class="mt-16 border-t border-stone-300 pt-2 font-semibold text-zinc-900">
                            {{ $rental->user->name }}
                        </div>
                    </div>
                    <div>
                        <p class="text-stone-500">Petugas</p>
                        <div class="mt-16 border-t border-stone-300 pt-2 font-semibold text-zinc-900">
                            CostumeRent
                        </div>
                    </div>
                </div>
                <p class="mt-8 text-center text-xs text-stone-400">
                    Terima kasih telah mempercayakan sewa kostum Anda kepada CostumeRent.
                    Deposit dikembalikan setelah inspeksi pengembalian selesai.
                </p>
            </section>
        </div>
    </main>
</body>
</html>
