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

@php $inputClass = 'mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500'; @endphp

<div class="space-y-5">
    <div>
        <label class="block text-sm font-semibold text-slate-700">Nama Kategori</label>
        <input type="text" name="name" required value="{{ old('name', $category->name) }}" class="{{ $inputClass }}">
        <p class="mt-1 text-xs text-slate-400">Slug akan dibuat otomatis dari nama.</p>
    </div>
    <div>
        <label class="block text-sm font-semibold text-slate-700">Deskripsi</label>
        <textarea name="description" rows="3" class="{{ $inputClass }}">{{ old('description', $category->description) }}</textarea>
    </div>
</div>

<div class="mt-6 flex items-center justify-end gap-3">
    <a href="{{ route('admin.categories.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Batal</a>
    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">
        <i class="ri-save-3-line"></i> Simpan
    </button>
</div>
