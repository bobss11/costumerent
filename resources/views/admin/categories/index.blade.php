@extends('layouts.admin')

@section('title', 'Kategori · CostumeRent Admin')
@section('page-title', 'Master Data Kategori')

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
            <h2 class="font-display text-2xl font-extrabold tracking-tight text-zinc-900">Kategori</h2>
            <p class="mt-1 text-sm text-stone-500">Kelompokkan kostum ke dalam kategori.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}"
           class="inline-flex items-center gap-1.5 rounded-xl bg-zinc-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-zinc-800">
            <i class="ri-add-line"></i> Tambah Kategori
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-soft">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-stone-50 text-left text-[11px] font-semibold uppercase tracking-wider text-stone-500">
                    <tr>
                        <th class="px-5 py-4">Nama</th>
                        <th class="px-5 py-4">Slug</th>
                        <th class="px-5 py-4">Deskripsi</th>
                        <th class="px-5 py-4">Kostum</th>
                        <th class="px-5 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($categories as $category)
                        <tr class="hover:bg-stone-50/80">
                            <td class="px-5 py-4 font-semibold text-zinc-900">{{ $category->name }}</td>
                            <td class="px-5 py-4 font-mono text-xs text-stone-400">{{ $category->slug }}</td>
                            <td class="px-5 py-4 text-stone-600">{{ \Illuminate\Support\Str::limit($category->description, 60) ?: '—' }}</td>
                            <td class="px-5 py-4 tabular-nums text-stone-700">{{ $category->costumes_count }}</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.categories.edit', $category) }}"
                                       class="inline-flex items-center gap-1 rounded-full border border-stone-200 px-3 py-1.5 text-xs font-semibold text-stone-700 transition hover:bg-stone-50">
                                        <i class="ri-pencil-line"></i> Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                          onsubmit="return confirm('Hapus kategori {{ $category->name }}?');">
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
                            <td colspan="5" class="px-5 py-12 text-center text-sm text-stone-400">Belum ada kategori.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
