@extends('layouts.app')

@section('title', 'Riwayat Sewa — CostumeRent')

@section('content')
    <div class="mb-8 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="font-display text-3xl font-extrabold tracking-tight text-zinc-900">Riwayat Sewa Saya</h1>
            <p class="mt-1.5 text-sm text-stone-500">Pantau status booking, aksesoris yang wajib dijaga, dan pengembalian deposit Anda.</p>
        </div>
        <a href="{{ route('catalog') }}"
           class="inline-flex items-center gap-1.5 rounded-xl bg-zinc-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-zinc-800">
            <i class="ri-add-line"></i> Sewa Lagi
        </a>
    </div>

    @if(session('status'))
        <div class="mb-6 flex items-start gap-2 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
            <i class="ri-checkbox-circle-fill mt-0.5 text-emerald-500"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if($rentals->isEmpty())
        <div class="rounded-2xl border border-dashed border-stone-300 bg-white p-16 text-center">
            <i class="ri-calendar-todo-line text-4xl text-stone-300"></i>
            <p class="mt-3 font-semibold text-stone-700">Belum ada riwayat sewa</p>
            <p class="mt-1 text-sm text-stone-500">Mulai jelajahi katalog dan buat reservasi pertama Anda.</p>
            <a href="{{ route('catalog') }}" class="mt-5 inline-flex items-center gap-1.5 rounded-xl bg-zinc-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-zinc-800">
                <i class="ri-store-2-line"></i> Lihat Katalog
            </a>
        </div>
    @else
        <div class="space-y-5">
            @foreach($rentals as $rental)
                @php
                    $accessories = array_filter(array_map('trim', explode(',', $rental->costume->accessories_included ?? '')));
                    $inspection = $rental->returnInspection;
                @endphp
                <article class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-soft">
                    <div class="flex flex-col gap-4 border-b border-stone-100 p-5 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-4">
                            <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-xl bg-stone-100">
                                <img src="{{ $rental->costume->image_url }}" alt="{{ $rental->costume->name }}" onerror="this.onerror=null;this.src='{{ asset('images/costume-placeholder.svg') }}';" class="h-full w-full object-cover">
                            </div>
                            <div>
                                <p class="font-mono text-xs font-semibold tabular-nums text-accent">{{ $rental->rental_code }}</p>
                                <h3 class="font-display font-bold tracking-tight text-zinc-900">{{ $rental->costume->name }}</h3>
                                <p class="text-xs tabular-nums text-stone-500">{{ $rental->qty }} unit &middot; {{ $rental->total_days }} hari</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            @include('partials.status-badge', ['status' => $rental->status])
                            @include('partials.status-badge', ['status' => $rental->payment_status, 'type' => 'payment'])
                        </div>
                    </div>

                    <div class="grid gap-5 p-5 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-stone-400">Periode Sewa</p>
                            <p class="mt-1.5 text-sm font-medium tabular-nums text-stone-700">{{ $rental->start_date->translatedFormat('d M Y') }}</p>
                            <p class="text-sm tabular-nums text-stone-500">s/d {{ $rental->end_date->translatedFormat('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-stone-400">Rincian Biaya</p>
                            <p class="mt-1.5 text-sm tabular-nums text-stone-600">Sewa: {{ rupiah($rental->rental_subtotal) }}</p>
                            <p class="text-sm tabular-nums text-accent">Deposit: {{ rupiah($rental->deposit_total) }}</p>
                            <p class="text-sm font-bold tabular-nums text-zinc-900">Total: {{ rupiah($rental->grand_total) }}</p>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-stone-400">Aksesoris Wajib Dijaga</p>
                            <div class="mt-1.5 flex flex-wrap gap-1.5">
                                @forelse($accessories as $item)
                                    <span class="inline-flex items-center gap-1 rounded-md bg-stone-100 px-2 py-0.5 text-xs font-medium text-stone-600"><i class="ri-shield-check-line text-stone-400"></i> {{ $item }}</span>
                                @empty
                                    <span class="text-xs text-stone-400">&mdash;</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Deposit refund details (shown once returned/inspected) --}}
                    @if($inspection)
                        <div class="border-t border-stone-100 bg-[#FAFAF9] p-5">
                            <p class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-stone-500">
                                <i class="ri-refund-2-line text-accent"></i> Rincian Pengembalian Deposit
                            </p>
                            <div class="mt-3 grid gap-3 text-sm sm:grid-cols-2 lg:grid-cols-4">
                                <div>
                                    <p class="text-stone-400">Kondisi</p>
                                    <div class="mt-1">@include('partials.status-badge', ['status' => $inspection->condition_status, 'type' => 'condition'])</div>
                                </div>
                                <div>
                                    <p class="text-stone-400">Keterlambatan</p>
                                    <p class="mt-1 font-medium tabular-nums text-stone-700">{{ $inspection->late_days }} hari &middot; {{ rupiah($inspection->late_fee_amount) }}</p>
                                </div>
                                <div>
                                    <p class="text-stone-400">Denda Kerusakan</p>
                                    <p class="mt-1 font-medium tabular-nums text-stone-700">{{ rupiah($inspection->damage_penalty_fee) }}</p>
                                </div>
                                <div>
                                    <p class="text-stone-400">Deposit Dikembalikan</p>
                                    <p class="mt-1 font-bold tabular-nums text-emerald-600">{{ rupiah($inspection->deposit_refunded) }}</p>
                                    @if($inspection->extra_charge > 0)
                                        <p class="text-xs font-semibold tabular-nums text-rose-600">Tambahan: {{ rupiah($inspection->extra_charge) }}</p>
                                    @endif
                                </div>
                            </div>
                            @if($inspection->inspection_notes)
                                <p class="mt-3 text-xs text-stone-500"><i class="ri-sticky-note-line"></i> {{ $inspection->inspection_notes }}</p>
                            @endif
                        </div>
                    @endif

                    <div class="flex flex-wrap items-center justify-end gap-2 border-t border-stone-100 p-4">
                        @if(Route::has('invoice.show'))
                            <a href="{{ route('invoice.show', $rental->rental_code) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 rounded-xl border border-stone-200 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:bg-stone-50">
                                <i class="ri-printer-line"></i> Lihat / Cetak Nota
                            </a>
                        @else
                            <a href="{{ url('/invoice/'.$rental->rental_code) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 rounded-xl border border-stone-200 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:bg-stone-50">
                                <i class="ri-printer-line"></i> Lihat / Cetak Nota
                            </a>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @endif
@endsection
