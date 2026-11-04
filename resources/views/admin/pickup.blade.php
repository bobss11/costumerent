@extends('layouts.admin')

@section('title', 'Proses Pengambilan · CostumeRent Admin')
@section('page-title', 'Serah-Terima & Verifikasi Pembayaran')

@section('content')
    <div class="mx-auto max-w-4xl">
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

        <div class="grid gap-6 lg:grid-cols-5">
            {{-- Rental summary --}}
            <aside class="lg:col-span-2">
                <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                    <p class="font-mono text-xs font-semibold text-indigo-600">{{ $rental->rental_code }}</p>
                    <div class="mt-3 flex items-center gap-4">
                        <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-xl bg-slate-100">
                            <img src="{{ $rental->costume->image_url }}" alt="{{ $rental->costume->name }}" class="h-full w-full object-cover">
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900">{{ $rental->costume->name }}</h3>
                            <p class="text-xs text-slate-500">{{ $rental->costume->code }} · {{ $rental->costume->size }}</p>
                        </div>
                    </div>

                    <dl class="mt-5 space-y-2.5 text-sm">
                        <div class="flex justify-between"><dt class="text-slate-500">Pelanggan</dt><dd class="font-medium text-slate-800">{{ $rental->user->name }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Jumlah</dt><dd class="font-medium text-slate-800">{{ $rental->qty }} unit</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Durasi</dt><dd class="font-medium text-slate-800">{{ $rental->total_days }} hari</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Periode</dt><dd class="text-right font-medium text-slate-800">{{ $rental->start_date->translatedFormat('d M Y') }} – {{ $rental->end_date->translatedFormat('d M Y') }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Stok tersedia</dt><dd class="font-medium text-slate-800">{{ $rental->costume->stock_available }}</dd></div>
                    </dl>

                    <div class="mt-5 space-y-2 border-t border-slate-100 pt-4 text-sm">
                        <div class="flex justify-between"><span class="text-slate-500">Subtotal sewa</span><span class="text-slate-700">{{ rupiah($rental->rental_subtotal) }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Deposit</span><span class="text-amber-600">{{ rupiah($rental->deposit_total) }}</span></div>
                        <div class="flex justify-between border-t border-slate-100 pt-2 text-base font-bold text-slate-900"><span>Total Tagihan</span><span>{{ rupiah($rental->grand_total) }}</span></div>
                    </div>
                </div>
            </aside>

            {{-- POS form --}}
            <div class="lg:col-span-3">
                <form method="POST" action="{{ route('admin.pickups.update', $rental) }}"
                      x-data="pickupPos({{ (float) $rental->grand_total }})"
                      class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
                    @csrf
                    @method('PUT')

                    <h3 class="text-lg font-bold text-slate-900">Verifikasi Serah-Terima</h3>
                    <p class="mt-1 text-sm text-slate-500">Catat jaminan, kondisi awal, dan terima pembayaran.</p>

                    <div class="mt-5 space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Kartu Identitas Jaminan Ditahan</label>
                            <input type="text" name="guarantee_card_held" required
                                   value="{{ old('guarantee_card_held', 'KTP Asli a.n '.$rental->user->name) }}"
                                   class="mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-xs text-slate-400">Contoh: "KTP Asli a.n Pelanggan"</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Catatan Kondisi Awal Kostum</label>
                            <textarea name="pickup_condition_notes" rows="3"
                                      class="mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="Kostum lengkap, aksesoris sesuai, tanpa cacat...">{{ old('pickup_condition_notes') }}</textarea>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700">Metode Pembayaran</label>
                                <select name="payment_method" class="mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="cash" @selected(old('payment_method')==='cash')>Tunai (Cash)</option>
                                    <option value="qris" @selected(old('payment_method')==='qris')>QRIS</option>
                                    <option value="transfer" @selected(old('payment_method')==='transfer')>Transfer</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700">Jumlah Dibayar</label>
                                <input type="number" name="amount_paid" min="0" step="any" required
                                       x-model.number="amountPaid"
                                       value="{{ old('amount_paid', (int) $rental->grand_total) }}"
                                       class="mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-medium text-slate-600">Kembalian</span>
                                <span class="text-lg font-extrabold" :class="change >= 0 ? 'text-emerald-600' : 'text-rose-600'" x-text="formatRupiah(change)"></span>
                            </div>
                            <p class="mt-1 text-xs text-slate-400" x-show="change < 0">Pembayaran belum menutup total tagihan.</p>
                        </div>

                        <button type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700">
                            <i class="ri-check-double-line"></i> Konfirmasi &amp; Serahkan Kostum
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function pickupPos(grandTotal) {
            return {
                grandTotal: grandTotal,
                amountPaid: {{ (int) old('amount_paid', (int) $rental->grand_total) }},
                get change() {
                    return (Number(this.amountPaid) || 0) - this.grandTotal;
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
