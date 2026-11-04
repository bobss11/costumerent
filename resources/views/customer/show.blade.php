@extends('layouts.app')

@section('title', $costume->name.' — CostumeRent')

@section('content')
    @php
        $accessories = array_filter(array_map('trim', explode(',', $costume->accessories_included)));
        $today = now()->toDateString();
    @endphp

    <nav class="mb-6 flex items-center gap-2 text-sm text-slate-500">
        <a href="{{ route('catalog') }}" class="transition hover:text-indigo-600">Katalog</a>
        <i class="ri-arrow-right-s-line"></i>
        <span class="text-slate-400">{{ $costume->category->name }}</span>
        <i class="ri-arrow-right-s-line"></i>
        <span class="font-medium text-slate-700">{{ $costume->name }}</span>
    </nav>

    <div class="grid gap-8 lg:grid-cols-5">
        {{-- Image + details --}}
        <div class="lg:col-span-3">
            <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
                <div class="aspect-[4/3] w-full overflow-hidden bg-slate-100">
                    <img src="{{ $costume->image_url }}" alt="{{ $costume->name }}" class="h-full w-full object-cover">
                </div>
            </div>

            <div class="mt-6 rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-semibold text-indigo-700">{{ $costume->category->name }}</span>
                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600"><i class="ri-ruler-line"></i> {{ $costume->size }}</span>
                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600"><i class="ri-user-line"></i> {{ $costume->gender }}</span>
                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-mono font-semibold text-slate-600">{{ $costume->code }}</span>
                </div>

                <h1 class="mt-4 text-2xl font-extrabold tracking-tight text-slate-900">{{ $costume->name }}</h1>

                @if($costume->description)
                    <p class="mt-3 leading-relaxed text-slate-600">{{ $costume->description }}</p>
                @endif

                <div class="mt-6">
                    <h2 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="ri-shopping-bag-3-line text-indigo-600"></i> Aksesoris Termasuk</h2>
                    <ul class="mt-3 grid gap-2 sm:grid-cols-2">
                        @foreach($accessories as $item)
                            <li class="flex items-center gap-2 text-sm text-slate-700">
                                <i class="ri-checkbox-circle-fill text-emerald-500"></i> {{ $item }}
                            </li>
                        @endforeach
                    </ul>
                    <p class="mt-3 text-xs text-slate-400"><i class="ri-information-line"></i> Seluruh aksesoris wajib dikembalikan lengkap untuk pengembalian deposit penuh.</p>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-4 border-t border-slate-100 pt-6 sm:grid-cols-4">
                    <div>
                        <p class="text-xs text-slate-400">Sewa / hari</p>
                        <p class="mt-1 font-bold text-slate-900">{{ rupiah($costume->rental_price_per_day) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Deposit / unit</p>
                        <p class="mt-1 font-bold text-amber-600">{{ rupiah($costume->deposit_amount) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Denda telat / hari</p>
                        <p class="mt-1 font-bold text-slate-900">{{ rupiah($costume->late_fee_per_day) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Stok tersedia</p>
                        <p class="mt-1 font-bold {{ $costume->stock_available > 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $costume->stock_available }} unit</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Reservation form + calculator --}}
        <div class="lg:col-span-2">
            <div class="sticky top-24 rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm"
                 x-data="reservation({
                    pricePerDay: {{ (float) $costume->rental_price_per_day }},
                    deposit: {{ (float) $costume->deposit_amount }},
                    stock: {{ (int) $costume->stock_available }}
                 })">
                <h2 class="text-lg font-bold text-slate-900">Formulir Reservasi</h2>
                <p class="mt-1 text-sm text-slate-500">Kalkulasi biaya diperbarui otomatis.</p>

                @if($errors->any())
                    <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700">
                        <ul class="list-inside list-disc space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @auth
                    @if(Auth::user()->isCustomer())
                        <form method="POST" action="{{ route('customer.rentals.store') }}" class="mt-5 space-y-4">
                            @csrf
                            <input type="hidden" name="costume_id" value="{{ $costume->id }}">

                            <div>
                                <label for="start_date" class="block text-sm font-medium text-slate-700">Tanggal Mulai Sewa</label>
                                <input type="date" id="start_date" name="start_date" x-model="start_date" :min="todayStr"
                                       class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100" required>
                            </div>

                            <div>
                                <label for="end_date" class="block text-sm font-medium text-slate-700">Tanggal Selesai Sewa</label>
                                <input type="date" id="end_date" name="end_date" x-model="end_date" :min="start_date || todayStr"
                                       class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100" required>
                            </div>

                            <div>
                                <label for="qty" class="block text-sm font-medium text-slate-700">Jumlah Unit</label>
                                <input type="number" id="qty" name="qty" x-model.number="qty" min="1" :max="stock"
                                       class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100" required>
                                <p class="mt-1 text-xs" :class="qty > stock ? 'text-rose-600' : 'text-slate-400'"
                                   x-text="qty > stock ? ('Melebihi stok tersedia (' + stock + ' unit)') : ('Maks. ' + stock + ' unit tersedia')"></p>
                            </div>

                            {{-- Live calculator --}}
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <dl class="space-y-2.5 text-sm">
                                    <div class="flex items-center justify-between">
                                        <dt class="text-slate-500">Durasi Sewa</dt>
                                        <dd class="font-semibold text-slate-900"><span x-text="totalDays"></span> hari</dd>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <dt class="text-slate-500">Subtotal Sewa</dt>
                                        <dd class="font-semibold text-slate-900" x-text="formatRupiah(subtotal)"></dd>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <dt class="text-slate-500">Total Deposit Jaminan</dt>
                                        <dd class="font-semibold text-amber-600" x-text="formatRupiah(depositTotal)"></dd>
                                    </div>
                                    <div class="flex items-center justify-between border-t border-slate-200 pt-2.5">
                                        <dt class="font-bold text-slate-900">Grand Total</dt>
                                        <dd class="text-lg font-extrabold text-indigo-600" x-text="formatRupiah(grandTotal)"></dd>
                                    </div>
                                </dl>
                                <p class="mt-2 text-xs text-slate-400">Deposit dikembalikan penuh bila kostum &amp; aksesoris dikembalikan lengkap dan tepat waktu.</p>
                            </div>

                            <button type="submit" :disabled="! canSubmit"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50">
                                <i class="ri-calendar-check-line"></i> Buat Reservasi
                            </button>
                            <p class="text-center text-xs text-slate-400" x-show="! canSubmit" x-cloak
                               x-text="stock <= 0 ? 'Stok tidak tersedia saat ini.' : 'Lengkapi tanggal yang valid dan jumlah unit sesuai stok.'"></p>
                        </form>
                    @else
                        <div class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                            Reservasi hanya dapat dilakukan oleh akun pelanggan.
                        </div>
                    @endif
                @else
                    <div class="mt-5 rounded-xl border border-indigo-100 bg-indigo-50 p-4 text-center">
                        <p class="text-sm text-slate-600">Masuk sebagai pelanggan untuk membuat reservasi.</p>
                        <a href="{{ route('login') }}"
                           class="mt-3 inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">
                            <i class="ri-login-box-line"></i> Masuk untuk Menyewa
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function reservation(config) {
            return {
                pricePerDay: config.pricePerDay,
                deposit: config.deposit,
                stock: config.stock,
                todayStr: '{{ $today }}',
                start_date: '',
                end_date: '',
                qty: config.stock > 0 ? 1 : 0,

                get totalDays() {
                    if (! this.start_date || ! this.end_date) return 0;
                    const start = new Date(this.start_date + 'T00:00:00');
                    const end = new Date(this.end_date + 'T00:00:00');
                    if (isNaN(start) || isNaN(end) || end < start) return 0;
                    const diff = Math.round((end - start) / 86400000);
                    return diff + 1; // inclusive
                },
                get quantity() {
                    const q = parseInt(this.qty);
                    return isNaN(q) || q < 1 ? 0 : q;
                },
                get subtotal() {
                    return this.pricePerDay * this.totalDays * this.quantity;
                },
                get depositTotal() {
                    return this.deposit * this.quantity;
                },
                get grandTotal() {
                    return this.subtotal + this.depositTotal;
                },
                get canSubmit() {
                    return this.totalDays > 0
                        && this.quantity >= 1
                        && this.quantity <= this.stock
                        && this.stock > 0;
                },
                formatRupiah(value) {
                    const n = Math.round(value || 0);
                    return 'Rp ' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                },
            };
        }
    </script>
    @endpush
@endsection
