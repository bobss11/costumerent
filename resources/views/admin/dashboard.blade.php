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

    <div class="mb-6">
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Selamat datang, {{ Auth::user()->name }}</h2>
        <p class="mt-1 text-sm text-slate-500">Ringkasan performa sewa dan antrean operasional hari ini.</p>
    </div>

    {{-- 4 KPI cards --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
        {{-- Total rental revenue --}}
        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                    <i class="ri-money-dollar-circle-line text-xl"></i>
                </span>
            </div>
            <p class="mt-4 text-sm font-medium text-slate-500">Total Pendapatan Sewa</p>
            <p class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900">{{ rupiah($totalRevenue) }}</p>
        </div>

        {{-- Held deposits --}}
        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                    <i class="ri-safe-2-line text-xl"></i>
                </span>
            </div>
            <p class="mt-4 text-sm font-medium text-slate-500">Dana Deposit Ditahan</p>
            <p class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900">{{ rupiah($heldDeposits) }}</p>
        </div>

        {{-- Costumes rented out --}}
        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-sky-50 text-sky-600">
                    <i class="ri-shirt-line text-xl"></i>
                </span>
            </div>
            <p class="mt-4 text-sm font-medium text-slate-500">Kostum Sedang Disewa</p>
            <p class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900">{{ $costumesRentedOut }} <span class="text-base font-semibold text-slate-400">unit</span></p>
        </div>

        {{-- Due / overdue alert today --}}
        <div class="rounded-2xl border {{ $dueTodayAlert > 0 ? 'border-rose-200 bg-rose-50/60' : 'border-slate-200/80 bg-white' }} p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-rose-50 text-rose-600">
                    <i class="ri-alarm-warning-line text-xl"></i>
                </span>
            </div>
            <p class="mt-4 text-sm font-medium text-slate-500">Jatuh Tempo / Terlambat Hari Ini</p>
            <p class="mt-1 text-2xl font-extrabold tracking-tight {{ $dueTodayAlert > 0 ? 'text-rose-600' : 'text-slate-900' }}">{{ $dueTodayAlert }} <span class="text-base font-semibold text-slate-400">rental</span></p>
        </div>
    </div>

    {{-- Queues --}}
    <div class="mt-8 grid grid-cols-1 gap-6 xl:grid-cols-2">
        {{-- Pickup queue --}}
        <section id="pickups" class="rounded-2xl border border-slate-200/80 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div class="flex items-center gap-2">
                    <i class="ri-hand-coin-line text-indigo-500"></i>
                    <h3 class="font-bold text-slate-900">Antrean Pengambilan</h3>
                </div>
                <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">{{ $pickupQueue->count() }}</span>
            </div>
            @if($pickupQueue->isEmpty())
                <div class="p-8 text-center text-sm text-slate-400">
                    <i class="ri-inbox-line text-2xl"></i>
                    <p class="mt-2">Tidak ada antrean pengambilan.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50/80 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3">Kode</th>
                                <th class="px-5 py-3">Pelanggan</th>
                                <th class="px-5 py-3">Kostum</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($pickupQueue as $rental)
                                <tr class="hover:bg-slate-50/60">
                                    <td class="px-5 py-3 font-mono text-xs font-semibold text-indigo-600">{{ $rental->rental_code }}</td>
                                    <td class="px-5 py-3 text-slate-700">{{ $rental->user->name }}</td>
                                    <td class="px-5 py-3 text-slate-600">{{ $rental->costume->name }} <span class="text-slate-400">×{{ $rental->qty }}</span></td>
                                    <td class="px-5 py-3">@include('partials.status-badge', ['status' => $rental->status])</td>
                                    <td class="px-5 py-3 text-right">
                                        <a href="{{ route('admin.pickups.edit', $rental) }}"
                                           class="inline-flex items-center gap-1 rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-indigo-700">
                                            <i class="ri-hand-coin-line"></i> Proses Pengambilan
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
        <section id="returns" class="rounded-2xl border border-slate-200/80 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div class="flex items-center gap-2">
                    <i class="ri-arrow-go-back-line text-emerald-500"></i>
                    <h3 class="font-bold text-slate-900">Antrean Pengembalian</h3>
                </div>
                <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">{{ $returnQueue->count() }}</span>
            </div>
            @if($returnQueue->isEmpty())
                <div class="p-8 text-center text-sm text-slate-400">
                    <i class="ri-inbox-line text-2xl"></i>
                    <p class="mt-2">Tidak ada antrean pengembalian.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50/80 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3">Kode</th>
                                <th class="px-5 py-3">Pelanggan</th>
                                <th class="px-5 py-3">Jatuh Tempo</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($returnQueue as $rental)
                                @php $overdue = $rental->end_date->lte($today); @endphp
                                <tr class="hover:bg-slate-50/60">
                                    <td class="px-5 py-3 font-mono text-xs font-semibold text-indigo-600">{{ $rental->rental_code }}</td>
                                    <td class="px-5 py-3 text-slate-700">{{ $rental->user->name }}</td>
                                    <td class="px-5 py-3">
                                        <span class="{{ $overdue ? 'font-semibold text-rose-600' : 'text-slate-600' }}">{{ $rental->end_date->translatedFormat('d M Y') }}</span>
                                        @if($overdue)<i class="ri-alarm-warning-line text-rose-500" title="Jatuh tempo / terlambat"></i>@endif
                                    </td>
                                    <td class="px-5 py-3">@include('partials.status-badge', ['status' => $rental->status])</td>
                                    <td class="px-5 py-3 text-right">
                                        <a href="{{ route('admin.returns.create', $rental) }}"
                                           class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-emerald-700">
                                            <i class="ri-search-eye-line"></i> Inspeksi Pengembalian
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
