@extends('layouts.app')

@section('title', 'Katalog Kostum — CostumeRent')

@section('content')
    {{-- Hero --}}
    <section class="mb-8 overflow-hidden rounded-3xl border border-slate-200/80 bg-gradient-to-br from-indigo-600 via-indigo-600 to-indigo-800 p-8 text-white shadow-sm sm:p-12">
        <div class="max-w-2xl">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-200">
                <i class="ri-sparkling-2-line"></i> Sewa Kostum Premium
            </span>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight sm:text-4xl">Temukan Kostum Sempurna untuk Setiap Momen</h1>
            <p class="mt-3 text-indigo-100">Pakaian adat, karnaval, cosplay, hingga busana profesi &mdash; lengkap dengan aksesoris dan jaminan kualitas.</p>
        </div>
    </section>

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('catalog') }}"
          class="mb-8 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm sm:p-5"
          x-data="{ autoSubmit() { this.$root.requestSubmit() } }">
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-[1fr_auto_auto_auto]">
            <div class="relative">
                <i class="ri-search-line pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Cari nama kostum..."
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-3 text-sm text-slate-700 outline-none transition focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100">
            </div>

            <select name="category_id" @change="autoSubmit()"
                    class="rounded-xl border border-slate-200 bg-slate-50 py-2.5 px-3 text-sm text-slate-700 outline-none transition focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected($filters['category_id'] === $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>

            <select name="size" @change="autoSubmit()"
                    class="rounded-xl border border-slate-200 bg-slate-50 py-2.5 px-3 text-sm text-slate-700 outline-none transition focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100">
                <option value="">Semua Ukuran</option>
                @foreach($sizes as $size)
                    <option value="{{ $size }}" @selected($filters['size'] === $size)>{{ $size }}</option>
                @endforeach
            </select>

            <div class="flex gap-2">
                <button type="submit"
                        class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">
                    <i class="ri-filter-3-line"></i> Terapkan
                </button>
                @if($filters['search'] !== '' || $filters['category_id'] || $filters['size'] !== '')
                    <a href="{{ route('catalog') }}"
                       class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-medium text-slate-500 transition hover:bg-slate-50"
                       title="Reset filter">
                        <i class="ri-close-line"></i>
                    </a>
                @endif
            </div>
        </div>
    </form>

    <div class="mb-5 flex items-center justify-between">
        <h2 class="text-lg font-bold text-slate-900">Katalog Kostum</h2>
        <span class="text-sm text-slate-500">{{ $costumes->count() }} kostum ditemukan</span>
    </div>

    {{-- Costume grid --}}
    @if($costumes->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
            <i class="ri-t-shirt-air-line text-4xl text-slate-300"></i>
            <p class="mt-3 font-semibold text-slate-700">Tidak ada kostum yang cocok</p>
            <p class="mt-1 text-sm text-slate-500">Coba ubah kata kunci atau filter pencarian Anda.</p>
        </div>
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($costumes as $costume)
                <article class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm transition hover:shadow-md">
                    <a href="{{ route('costumes.show', $costume) }}" class="relative block aspect-[4/3] overflow-hidden bg-slate-100">
                        <img src="{{ $costume->image_url }}" alt="{{ $costume->name }}"
                             loading="lazy"
                             class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                        <span class="absolute left-3 top-3 inline-flex items-center gap-1 rounded-full border border-white/40 bg-white/90 px-2.5 py-0.5 text-xs font-semibold text-slate-700 backdrop-blur">
                            <i class="ri-ruler-line"></i> {{ $costume->size }}
                        </span>
                        @if($costume->stock_available <= 0)
                            <span class="absolute right-3 top-3 inline-flex items-center rounded-full bg-rose-500 px-2.5 py-0.5 text-xs font-semibold text-white">Stok Habis</span>
                        @endif
                    </a>

                    <div class="flex flex-1 flex-col p-5">
                        <span class="text-xs font-semibold uppercase tracking-wide text-indigo-600">{{ $costume->category->name }}</span>
                        <h3 class="mt-1 font-bold leading-snug text-slate-900">
                            <a href="{{ route('costumes.show', $costume) }}" class="transition hover:text-indigo-600">{{ $costume->name }}</a>
                        </h3>

                        <div class="mt-3 flex flex-wrap gap-1.5">
                            @foreach(array_slice(array_map('trim', explode(',', $costume->accessories_included)), 0, 3) as $item)
                                <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">{{ $item }}</span>
                            @endforeach
                            @php $accCount = count(array_filter(array_map('trim', explode(',', $costume->accessories_included)))); @endphp
                            @if($accCount > 3)
                                <span class="inline-flex items-center rounded-md bg-slate-50 px-2 py-0.5 text-xs font-medium text-slate-400">+{{ $accCount - 3 }} lainnya</span>
                            @endif
                        </div>

                        <div class="mt-4 flex items-end justify-between border-t border-slate-100 pt-4">
                            <div>
                                <p class="text-xs text-slate-400">Sewa / hari</p>
                                <p class="text-lg font-extrabold text-slate-900">{{ rupiah($costume->rental_price_per_day) }}</p>
                                <p class="mt-0.5 text-xs text-amber-600">Deposit {{ rupiah($costume->deposit_amount) }}</p>
                            </div>
                            <a href="{{ route('costumes.show', $costume) }}"
                               class="inline-flex items-center gap-1 rounded-lg bg-indigo-50 px-3 py-2 text-sm font-semibold text-indigo-600 transition hover:bg-indigo-600 hover:text-white">
                                Sewa <i class="ri-arrow-right-line"></i>
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
@endsection
