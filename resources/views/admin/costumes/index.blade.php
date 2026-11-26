@extends('layouts.admin')

@section('title', 'Kostum · CostumeRent Admin')
@section('page-title', 'Master Data Kostum')

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

    <div class="mb-7 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="font-display text-2xl font-extrabold tracking-tight text-zinc-900">Kostum</h2>
            <p class="mt-1 text-sm text-stone-500">Kelola katalog kostum, harga sewa, deposit, dan stok.</p>
        </div>
        <a href="{{ route('admin.costumes.create') }}"
           class="inline-flex items-center gap-1.5 rounded-xl bg-zinc-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-zinc-800">
            <i class="ri-add-line"></i> Tambah Kostum
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-soft">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-stone-50 text-left text-[11px] font-semibold uppercase tracking-wider text-stone-500">
                    <tr>
                        <th class="px-5 py-4">Kostum</th>
                        <th class="px-5 py-4">Kategori</th>
                        <th class="px-5 py-4">Harga / Hari</th>
                        <th class="px-5 py-4">Deposit</th>
                        <th class="px-5 py-4">Stok</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($costumes as $costume)
                        <tr class="hover:bg-stone-50/80">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-11 w-11 flex-shrink-0 overflow-hidden rounded-lg bg-stone-100">
                                        <img src="{{ $costume->image_url }}" alt="{{ $costume->name }}" onerror="this.onerror=null;this.src='{{ asset('images/costume-placeholder.svg') }}';" class="h-full w-full object-cover">
                                    </div>
                                    <div>
                                        <p class="font-semibold text-zinc-900">{{ $costume->name }}</p>
                                        <p class="font-mono text-xs tabular-nums text-stone-400">{{ $costume->code }} · {{ $costume->size }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-stone-600">{{ $costume->category->name ?? '—' }}</td>
                            <td class="px-5 py-4 tabular-nums text-stone-700">{{ rupiah($costume->rental_price_per_day) }}</td>
                            <td class="px-5 py-4 tabular-nums text-accent">{{ rupiah($costume->deposit_amount) }}</td>
                            <td class="px-5 py-4 tabular-nums text-stone-700">{{ $costume->stock_available }} / {{ $costume->stock_total }}</td>
                            <td class="px-5 py-4">
                                @if($costume->is_active)
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200/80 bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-800"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Aktif</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-stone-200 bg-stone-100 px-3 py-1 text-xs font-medium text-stone-500"><span class="h-1.5 w-1.5 rounded-full bg-stone-400"></span> Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.costumes.edit', $costume) }}"
                                       class="inline-flex items-center gap-1 rounded-full border border-stone-200 px-3 py-1.5 text-xs font-semibold text-stone-700 transition hover:bg-stone-50">
                                        <i class="ri-pencil-line"></i> Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.costumes.destroy', $costume) }}"
                                          onsubmit="return confirm('Hapus kostum {{ $costume->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 rounded-full border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-50">
                                            <i class="ri-delete-bin-line"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-sm text-stone-400">Belum ada kostum.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
