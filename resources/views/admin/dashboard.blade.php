@extends('layouts.admin')

@section('title', 'Dashboard · CostumeRent Admin')
@section('page-title', 'Dashboard Operasional')

@section('content')
    @if(session('status'))
        <div class="mb-6 flex items-start gap-2 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
            <i class="ri-checkbox-circle-fill mt-0.5 text-emerald-500"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 flex items-start gap-2 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
            <i class="ri-error-warning-fill mt-0.5 text-rose-500"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="mb-7">
        <h2 class="font-display text-2xl font-extrabold tracking-tight text-zinc-900">Selamat datang, {{ Auth::user()->name }}</h2>
        <p class="mt-1 text-sm text-stone-500">Ringkasan performa sewa dan antrean operasional hari ini.</p>
    </div>

    {{-- 4 KPI cards --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
        {{-- Total rental revenue --}}
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-soft">
            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-stone-100 text-stone-700">
                <i class="ri-money-dollar-circle-line text-xl"></i>
            </span>
            <p class="mt-4 text-xs font-semibold uppercase tracking-wider text-stone-400">Total Pendapatan Sewa</p>
            <p class="mt-1.5 text-3xl font-bold tracking-tight tabular-nums text-zinc-900">{{ rupiah($totalRevenue) }}</p>
        </div>

        {{-- Held deposits --}}
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-soft">
            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-stone-100 text-accent">
                <i class="ri-safe-2-line text-xl"></i>
            </span>
            <p class="mt-4 text-xs font-semibold uppercase tracking-wider text-stone-400">Dana Deposit Ditahan</p>
            <p class="mt-1.5 text-3xl font-bold tracking-tight tabular-nums text-zinc-900">{{ rupiah($heldDeposits) }}</p>
        </div>

        {{-- Costumes rented out --}}
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-soft">
            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-stone-100 text-stone-700">
                <i class="ri-shirt-line text-xl"></i>
            </span>
            <p class="mt-4 text-xs font-semibold uppercase tracking-wider text-stone-400">Kostum Sedang Disewa</p>
            <p class="mt-1.5 text-3xl font-bold tracking-tight tabular-nums text-zinc-900">{{ $costumesRentedOut }} <span class="text-base font-semibold text-stone-400">unit</span></p>
        </div>

        {{-- Due / overdue alert today --}}
        <div class="rounded-2xl border {{ $dueTodayAlert > 0 ? 'border-rose-200 bg-rose-50/60' : 'border-stone-200 bg-white' }} p-5 shadow-soft">
            <span class="flex h-11 w-11 items-center justify-center rounded-full {{ $dueTodayAlert > 0 ? 'bg-rose-100 text-rose-600' : 'bg-stone-100 text-stone-700' }}">
                <i class="ri-alarm-warning-line text-xl"></i>
            </span>
            <p class="mt-4 text-xs font-semibold uppercase tracking-wider text-stone-400">Jatuh Tempo / Terlambat Hari Ini</p>
            <p class="mt-1.5 text-3xl font-bold tracking-tight tabular-nums {{ $dueTodayAlert > 0 ? 'text-rose-600' : 'text-zinc-900' }}">{{ $dueTodayAlert }} <span class="text-base font-semibold text-stone-400">rental</span></p>
        </div>
    </div>

    {{-- Queues --}}
    <div class="mt-8 grid grid-cols-1 gap-6 xl:grid-cols-2">
        {{-- Pickup queue --}}
        <section id="pickups" class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-soft">
            <div class="flex items-center justify-between border-b border-stone-100 px-5 py-4">
                <div class="flex items-center gap-2">
                    <i class="ri-hand-coin-line text-stone-500"></i>
                    <h3 class="font-display font-bold tracking-tight text-zinc-900">Antrean Pengambilan</h3>
                </div>
                <span class="rounded-full bg-stone-100 px-2.5 py-0.5 text-xs font-semibold tabular-nums text-stone-600">{{ $pickupQueue->count() }}</span>
            </div>
            @if($pickupQueue->isEmpty())
                <div class="p-10 text-center text-sm text-stone-400">
                    <i class="ri-inbox-line text-2xl"></i>
                    <p class="mt-2">Tidak ada antrean pengambilan.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-stone-50 text-left text-[11px] font-semibold uppercase tracking-wider text-stone-500">
                            <tr>
                                <th class="px-5 py-4">Kode</th>
                                <th class="px-5 py-4">Pelanggan</th>
                                <th class="px-5 py-4">Kostum</th>
                                <th class="px-5 py-4">Status</th>
                                <th class="px-5 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @foreach($pickupQueue as $rental)
                                <tr class="hover:bg-stone-50/80">
                                    <td class="px-5 py-4 font-mono text-xs font-semibold tabular-nums text-accent">{{ $rental->rental_code }}</td>
                                    <td class="px-5 py-4 text-stone-700">{{ $rental->user->name }}</td>
                                    <td class="px-5 py-4 text-stone-600">{{ $rental->costume->name }} <span class="tabular-nums text-stone-400">×{{ $rental->qty }}</span></td>
                                    <td class="px-5 py-4">@include('partials.status-badge', ['status' => $rental->status])</td>
                                    <td class="px-5 py-4 text-right">
                                        <a href="{{ route('admin.pickups.edit', $rental) }}"
                                           class="inline-flex items-center gap-1 rounded-full bg-zinc-900 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-zinc-800">
                                            <i class="ri-hand-coin-line"></i> Proses
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        {{-- Return queue --}}
        <section id="returns" class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-soft">
            <div class="flex items-center justify-between border-b border-stone-100 px-5 py-4">
                <div class="flex items-center gap-2">
                    <i class="ri-arrow-go-back-line text-stone-500"></i>
                    <h3 class="font-display font-bold tracking-tight text-zinc-900">Antrean Pengembalian</h3>
                </div>
                <span class="rounded-full bg-stone-100 px-2.5 py-0.5 text-xs font-semibold tabular-nums text-stone-600">{{ $returnQueue->count() }}</span>
            </div>
            @if($returnQueue->isEmpty())
                <div class="p-10 text-center text-sm text-stone-400">
                    <i class="ri-inbox-line text-2xl"></i>
                    <p class="mt-2">Tidak ada antrean pengembalian.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-stone-50 text-left text-[11px] font-semibold uppercase tracking-wider text-stone-500">
                            <tr>
                                <th class="px-5 py-4">Kode</th>
                                <th class="px-5 py-4">Pelanggan</th>
                                <th class="px-5 py-4">Jatuh Tempo</th>
                                <th class="px-5 py-4">Status</th>
                                <th class="px-5 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @foreach($returnQueue as $rental)
                                @php $overdue = $rental->end_date->lte($today); @endphp
                                <tr class="hover:bg-stone-50/80">
                                    <td class="px-5 py-4 font-mono text-xs font-semibold tabular-nums text-accent">{{ $rental->rental_code }}</td>
                                    <td class="px-5 py-4 text-stone-700">{{ $rental->user->name }}</td>
                                    <td class="px-5 py-4">
                                        <span class="tabular-nums {{ $overdue ? 'font-semibold text-rose-600' : 'text-stone-600' }}">{{ $rental->end_date->translatedFormat('d M Y') }}</span>
                                        @if($overdue)<i class="ri-alarm-warning-line text-rose-500" title="Jatuh tempo / terlambat"></i>@endif
                                    </td>
                                    <td class="px-5 py-4">@include('partials.status-badge', ['status' => $rental->status])</td>
                                    <td class="px-5 py-4 text-right">
                                        <a href="{{ route('admin.returns.create', $rental) }}"
                                           class="inline-flex items-center gap-1 rounded-full bg-zinc-900 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-zinc-800">
                                            <i class="ri-search-eye-line"></i> Inspeksi
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection
