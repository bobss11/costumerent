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

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Kostum</h2>
            <p class="mt-1 text-sm text-slate-500">Kelola katalog kostum, harga sewa, deposit, dan stok.</p>
        </div>
        <a href="{{ route('admin.costumes.create') }}"
           class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">
            <i class="ri-add-line"></i> Tambah Kostum
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50/80 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Kostum</th>
                        <th class="px-5 py-3">Kategori</th>
                        <th class="px-5 py-3">Harga / Hari</th>
                        <th class="px-5 py-3">Deposit</th>
                        <th class="px-5 py-3">Stok</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($costumes as $costume)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 flex-shrink-0 overflow-hidden rounded-lg bg-slate-100">
                                        <img src="{{ $costume->image_url }}" alt="{{ $costume->name }}" class="h-full w-full object-cover">
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ $costume->name }}</p>
                                        <p class="font-mono text-xs text-slate-400">{{ $costume->code }} · {{ $costume->size }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-slate-600">{{ $costume->category->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-slate-700">{{ rupiah($costume->rental_price_per_day) }}</td>
                            <td class="px-5 py-3 text-amber-600">{{ rupiah($costume->deposit_amount) }}</td>
                            <td class="px-5 py-3 text-slate-700">{{ $costume->stock_available }} / {{ $costume->stock_total }}</td>
                            <td class="px-5 py-3">
                                @if($costume->is_active)
                                    <span class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">Aktif</span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-500">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.costumes.edit', $costume) }}"
                                       class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                                        <i class="ri-pencil-line"></i> Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.costumes.destroy', $costume) }}"
                                          onsubmit="return confirm('Hapus kostum {{ $costume->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-rose-200 px-2.5 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-50">
                                            <i class="ri-delete-bin-line"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-sm text-slate-400">Belum ada kostum.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
