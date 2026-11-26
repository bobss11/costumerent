@extends('layouts.app')

@section('title', 'Katalog Kostum — CostumeRent')

@section('content')
    {{-- Hero --}}
    <section class="mb-10">
        <span class="inline-flex items-center gap-1.5 rounded-full border border-stone-200 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-wide text-accent shadow-soft">
            <i class="ri-sparkling-2-line"></i> Curated Attire Rental
        </span>
        <h1 class="font-display mt-5 max-w-3xl text-4xl font-extrabold leading-[1.05] tracking-tight text-zinc-900 sm:text-5xl">
            Busana adat &amp; kostum pilihan, siap untuk setiap momen.
        </h1>
        <p class="mt-4 max-w-xl text-base text-stone-500">
            Pakaian adat, karnaval, cosplay, hingga busana profesi &mdash; lengkap dengan aksesoris dan jaminan kualitas.
        </p>
    </section>

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('catalog') }}"
          class="mb-8 rounded-2xl border border-stone-200 bg-white p-3 shadow-soft"
          x-data="{ autoSubmit() { this.$root.requestSubmit() } }">
        <div class="grid gap-2.5 lg:grid-cols-[1fr_auto_auto_auto]">
            <div class="relative">
                <i class="ri-search-line pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-stone-400"></i>
                <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Cari nama kostum..."
                       class="w-full rounded-xl border border-stone-200 bg-[#FAFAF9] py-3 pl-11 pr-3 text-sm text-stone-700 outline-none transition focus:border-zinc-400 focus:bg-white focus:ring-2 focus:ring-stone-200">
            </div>

            <select name="category_id" @change="autoSubmit()"
                    class="rounded-xl border border-stone-200 bg-[#FAFAF9] py-3 px-4 text-sm text-stone-700 outline-none transition focus:border-zinc-400 focus:bg-white focus:ring-2 focus:ring-stone-200">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected($filters['category_id'] === $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>

            <select name="size" @change="autoSubmit()"
                    class="rounded-xl border border-stone-200 bg-[#FAFAF9] py-3 px-4 text-sm text-stone-700 outline-none transition focus:border-zinc-400 focus:bg-white focus:ring-2 focus:ring-stone-200">
                <option value="">Semua Ukuran</option>
                @foreach($sizes as $size)
                    <option value="{{ $size }}" @selected($filters['size'] === $size)>{{ $size }}</option>
                @endforeach
            </select>

            <div class="flex gap-2">
                <button type="submit"
                        class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-xl bg-zinc-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-zinc-800">
                    <i class="ri-filter-3-line"></i> Terapkan
                </button>
                @if($filters['search'] !== '' || $filters['category_id'] || $filters['size'] !== '')
                    <a href="{{ route('catalog') }}"
                       class="inline-flex items-center justify-center rounded-xl border border-stone-200 px-3.5 py-3 text-sm font-medium text-stone-500 transition hover:bg-stone-50"
                       title="Reset filter">
                        <i class="ri-close-line"></i>
                    </a>
                @endif
            </div>
        </div>
    </form>

    <div class="mb-6 flex items-baseline justify-between">
        <h2 class="font-display text-xl font-bold tracking-tight text-zinc-900">Katalog Kostum</h2>
        <span class="text-sm tabular-nums text-stone-500">{{ $costumes->count() }} kostum ditemukan</span>
    </div>

    {{-- Costume grid --}}
    @if($costumes->isEmpty())
        <div class="rounded-2xl border border-dashed border-stone-300 bg-white p-16 text-center">
            <i class="ri-t-shirt-air-line text-4xl text-stone-300"></i>
            <p class="mt-3 font-semibold text-stone-700">Tidak ada kostum yang cocok</p>
            <p class="mt-1 text-sm text-stone-500">Coba ubah kata kunci atau filter pencarian Anda.</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-7 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($costumes as $costume)
                <article class="group flex flex-col overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-soft transition hover:-translate-y-0.5 hover:shadow-[0_8px_30px_-12px_rgba(0,0,0,0.12)]">
                    <a href="{{ route('costumes.show', $costume) }}" class="relative block aspect-[4/5] overflow-hidden rounded-t-2xl bg-stone-100">
                        <img src="{{ $costume->image_url }}" alt="{{ $costume->name }}"
                             loading="lazy"
                             onerror="this.onerror=null;this.src='{{ asset('images/costume-placeholder.svg') }}';"
                             class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        <span class="absolute left-3 top-3 inline-flex items-center gap-1 rounded-full border border-white/50 bg-white/90 px-2.5 py-1 text-xs font-semibold text-zinc-800 backdrop-blur">
                            <i class="ri-ruler-line"></i> {{ $costume->size }}
                        </span>
                        @if($costume->stock_available <= 0)
                            <span class="absolute right-3 top-3 inline-flex items-center rounded-full bg-rose-500 px-2.5 py-1 text-xs font-semibold text-white">Stok Habis</span>
                        @endif
                    </a>

                    <div class="flex flex-1 flex-col p-5">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-400">{{ $costume->category->name }}</span>
                        <h3 class="font-display mt-1.5 font-bold leading-snug tracking-tight text-zinc-900">
                            <a href="{{ route('costumes.show', $costume) }}" class="transition hover:text-accent">{{ $costume->name }}</a>
                        </h3>

                        <div class="mt-3 flex flex-wrap gap-1.5">
                            @foreach(array_slice(array_map('trim', explode(',', $costume->accessories_included)), 0, 3) as $item)
                                <span class="inline-flex items-center rounded-md bg-stone-100 px-2 py-0.5 text-xs font-medium text-stone-600">{{ $item }}</span>
                            @endforeach
                            @php $accCount = count(array_filter(array_map('trim', explode(',', $costume->accessories_included)))); @endphp
                            @if($accCount > 3)
                                <span class="inline-flex items-center rounded-md bg-stone-50 px-2 py-0.5 text-xs font-medium text-stone-400">+{{ $accCount - 3 }} lainnya</span>
                            @endif
                        </div>

                        <div class="mt-5 flex items-end justify-between border-t border-stone-100 pt-4">
                            <div>
                                <p class="text-xs text-stone-400">Sewa / hari</p>
                                <p class="text-lg font-extrabold tabular-nums tracking-tight text-zinc-900">{{ rupiah($costume->rental_price_per_day) }}</p>
                                <p class="mt-0.5 text-xs font-medium tabular-nums text-accent">Deposit {{ rupiah($costume->deposit_amount) }}</p>
                            </div>
                            <a href="{{ route('costumes.show', $costume) }}"
                               class="inline-flex items-center gap-1 rounded-xl bg-zinc-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-zinc-800">
                                Sewa <i class="ri-arrow-right-line"></i>
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
@endsection
