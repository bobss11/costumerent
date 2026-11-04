@extends('layouts.admin')

@section('title', 'Inspeksi Pengembalian · CostumeRent Admin')
@section('page-title', 'Inspeksi Pengembalian & Penyelesaian Deposit')

@section('content')
    <div class="mx-auto max-w-5xl">
        <a href="{{ route('admin.dashboard') }}" class="mb-4 inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-slate-800">
            <i class="ri-arrow-left-line"></i> Kembali ke Dashboard
        </a>

        @if($errors->any())
            <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                <p class="flex items-center gap-2 font-semibold"><i class="ri-error-warning-fill text-rose-500"></i> Periksa kembali isian berikut:</p>
                <ul class="mt-2 list-inside list-disc space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $accessories = array_filter(array_map('trim', explode(',', $rental->costume->accessories_included ?? '')));
        @endphp

        <form method="POST" action="{{ route('admin.returns.store', $rental) }}"
              x-data="returnInspection({
                  endDate: '{{ $rental->end_date->toDateString() }}',
                  depositTotal: {{ (float) $rental->deposit_total }},
                  lateFeePerDay: {{ (float) $rental->costume->late_fee_per_day }},
                  qty: {{ (int) $rental->qty }},
                  oldReturnDate: '{{ old('actual_return_date', now()->toDateString()) }}',
                  oldPenalty: {{ (int) old('damage_penalty_fee', 0) }},
              })"
              class="grid gap-6 lg:grid-cols-5">
            @csrf

            {{-- Left: rental + inspection inputs --}}
            <div class="space-y-6 lg:col-span-3">
                <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-xl bg-slate-100">
                            <img src="{{ $rental->costume->image_url }}" alt="{{ $rental->costume->name }}" class="h-full w-full object-cover">
                        </div>
                        <div>
                            <p class="font-mono text-xs font-semibold text-indigo-600">{{ $rental->rental_code }}</p>
                            <h3 class="font-bold text-slate-900">{{ $rental->costume->name }}</h3>
                            <p class="text-xs text-slate-500">{{ $rental->user->name }} · {{ $rental->qty }} unit · jatuh tempo {{ $rental->end_date->translatedFormat('d M Y') }}</p>
                        </div>
                    </div>
                    @if($accessories)
                        <div class="mt-4 flex flex-wrap gap-1.5">
                            @foreach($accessories as $item)
                                <span class="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600"><i class="ri-checkbox-circle-line text-slate-400"></i> {{ $item }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
                    <h3 class="text-lg font-bold text-slate-900">Formulir Inspeksi</h3>

                    <div class="mt-5 space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Tanggal Kembali Aktual</label>
                            <input type="date" name="actual_return_date" required
                                   x-model="actualReturnDate"
                                   class="mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-xs" :class="lateDays > 0 ? 'text-rose-500 font-medium' : 'text-slate-400'">
                                <span x-show="lateDays > 0" x-text="'Terlambat ' + lateDays + ' hari.'"></span>
                                <span x-show="lateDays === 0">Dikembalikan tepat waktu.</span>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Kondisi Kostum &amp; Aksesoris</label>
                            <div class="mt-2 grid gap-2 sm:grid-cols-2">
                                @php
                                    $conditions = [
                                        'good' => ['Baik / Lengkap', 'ri-checkbox-circle-line', 'emerald'],
                                        'minor_stain' => ['Noda Ringan', 'ri-contrast-drop-line', 'amber'],
                                        'damaged' => ['Rusak / Sobek', 'ri-scissors-cut-line', 'rose'],
                                        'incomplete_accessories' => ['Aksesoris Kurang', 'ri-puzzle-line', 'rose'],
                                    ];
                                @endphp
                                @foreach($conditions as $value => $meta)
                                    <label class="flex cursor-pointer items-center gap-2 rounded-xl border px-3 py-2.5 text-sm transition"
                                           :class="conditionStatus === '{{ $value }}' ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">
                                        <input type="radio" name="condition_status" value="{{ $value }}" class="sr-only"
                                               x-model="conditionStatus"
                                               @if(old('condition_status', 'good') === $value) checked @endif>
                                        <i class="{{ $meta[1] }}"></i> {{ $meta[0] }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Denda Kerusakan / Kehilangan</label>
                            <input type="number" name="damage_penalty_fee" min="0" step="any"
                                   x-model.number="damagePenalty"
                                   class="mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Catatan Inspeksi</label>
                            <textarea name="inspection_notes" rows="3"
                                      class="mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="Catatan kondisi saat dikembalikan...">{{ old('inspection_notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: live deposit settlement --}}
            <aside class="lg:col-span-2">
                <div class="sticky top-20 rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                    <p class="flex items-center gap-2 text-sm font-bold text-slate-900">
                        <i class="ri-calculator-line text-indigo-500"></i> Penyelesaian Deposit
                    </p>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Deposit Awal</dt>
                            <dd class="font-semibold text-slate-800" x-text="formatRupiah(depositTotal)"></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Denda Keterlambatan</dt>
                            <dd class="text-slate-700">
                                <span x-text="formatRupiah(lateFeeAmount)"></span>
                                <span class="text-xs text-slate-400" x-text="'(' + lateDays + ' hari × ' + formatRupiah(lateFeePerDay) + ' × ' + qty + ')'"></span>
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Denda Kerusakan</dt>
                            <dd class="text-slate-700" x-text="formatRupiah(damagePenalty)"></dd>
                        </div>
                        <div class="flex justify-between border-t border-slate-100 pt-3">
                            <dt class="font-medium text-slate-600">Total Potongan</dt>
                            <dd class="font-semibold text-rose-600" x-text="formatRupiah(totalDeduction)"></dd>
                        </div>
                    </dl>

                    <div class="mt-4 rounded-xl bg-emerald-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Deposit Dikembalikan</p>
                        <p class="mt-1 text-2xl font-extrabold text-emerald-700" x-text="formatRupiah(depositRefunded)"></p>
                    </div>

                    <div class="mt-3 rounded-xl bg-rose-50 p-4" x-show="extraCharge > 0" x-cloak>
                        <p class="text-xs font-semibold uppercase tracking-wide text-rose-700">Tagihan Tambahan</p>
                        <p class="mt-1 text-xl font-extrabold text-rose-700" x-text="formatRupiah(extraCharge)"></p>
                        <p class="mt-1 text-xs text-rose-600">Potongan melebihi deposit — pelanggan harus membayar selisihnya.</p>
                    </div>

                    <button type="submit"
                            class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">
                        <i class="ri-save-3-line"></i> Simpan &amp; Selesaikan Pengembalian
                    </button>
                </div>
            </aside>
        </form>
    </div>

    @push('scripts')
    <script>
        function returnInspection(config) {
            return {
                endDate: config.endDate,
                depositTotal: config.depositTotal,
                lateFeePerDay: config.lateFeePerDay,
                qty: config.qty,
                actualReturnDate: config.oldReturnDate,
                conditionStatus: @js(old('condition_status', 'good')),
                damagePenalty: config.oldPenalty,
                get lateDays() {
                    if (! this.actualReturnDate) return 0;
                    const end = new Date(this.endDate + 'T00:00:00');
                    const actual = new Date(this.actualReturnDate + 'T00:00:00');
                    const diff = Math.floor((actual - end) / 86400000);
                    return Math.max(0, diff);
                },
                get lateFeeAmount() {
                    return this.lateDays * this.lateFeePerDay * this.qty;
                },
                get totalDeduction() {
                    return this.lateFeeAmount + (Number(this.damagePenalty) || 0);
                },
                get depositRefunded() {
                    return Math.max(0, this.depositTotal - this.totalDeduction);
                },
                get extraCharge() {
                    return Math.max(0, this.totalDeduction - this.depositTotal);
                },
                formatRupiah(value) {
                    const n = Math.round(Number(value) || 0);
                    return 'Rp ' + n.toLocaleString('id-ID');
                },
            };
        }
    </script>
    @endpush
@endsection
