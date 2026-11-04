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

<div class="grid gap-5 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-slate-700">Nama Kostum</label>
        <input type="text" name="name" required value="{{ old('name', $costume->name) }}" class="{{ $inputClass }}">
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700">Kode</label>
        <input type="text" name="code" required value="{{ old('code', $costume->code) }}" class="{{ $inputClass }}" placeholder="CST-XXX-01">
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700">Kategori</label>
        <select name="category_id" class="{{ $inputClass }}">
            <option value="">— Pilih kategori —</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected((int) old('category_id', $costume->category_id) === $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700">Ukuran</label>
        <select name="size" class="{{ $inputClass }}">
            @foreach(['S','M','L','XL','All Size'] as $size)
                <option value="{{ $size }}" @selected(old('size', $costume->size) === $size)>{{ $size }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700">Gender</label>
        <select name="gender" class="{{ $inputClass }}">
            @foreach(['Pria','Wanita','Unisex'] as $gender)
                <option value="{{ $gender }}" @selected(old('gender', $costume->gender) === $gender)>{{ $gender }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700">Harga Sewa / Hari (Rp)</label>
        <input type="number" name="rental_price_per_day" min="0" step="any" required value="{{ old('rental_price_per_day', $costume->rental_price_per_day) }}" class="{{ $inputClass }}">
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700">Nominal Deposit (Rp)</label>
        <input type="number" name="deposit_amount" min="0" step="any" required value="{{ old('deposit_amount', $costume->deposit_amount) }}" class="{{ $inputClass }}">
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700">Denda Keterlambatan / Hari (Rp)</label>
        <input type="number" name="late_fee_per_day" min="0" step="any" required value="{{ old('late_fee_per_day', $costume->late_fee_per_day) }}" class="{{ $inputClass }}">
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700">Stok Total</label>
        <input type="number" name="stock_total" min="0" required value="{{ old('stock_total', $costume->stock_total) }}" class="{{ $inputClass }}">
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700">Stok Tersedia <span class="font-normal text-slate-400">(opsional, default = total)</span></label>
        <input type="number" name="stock_available" min="0" value="{{ old('stock_available', $costume->stock_available) }}" class="{{ $inputClass }}">
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-slate-700">Aksesoris Termasuk <span class="font-normal text-slate-400">(pisahkan dengan koma)</span></label>
        <input type="text" name="accessories_included" required value="{{ old('accessories_included', $costume->accessories_included) }}" class="{{ $inputClass }}" placeholder="Keris, Blangkon, Selop">
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-slate-700">URL Gambar</label>
        <input type="text" name="image_url" required value="{{ old('image_url', $costume->image_url) }}" class="{{ $inputClass }}" placeholder="https://...">
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-slate-700">Deskripsi</label>
        <textarea name="description" rows="3" class="{{ $inputClass }}">{{ old('description', $costume->description) }}</textarea>
    </div>

    <div class="sm:col-span-2">
        <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $costume->is_active ?? true)) class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
            Aktif (tampil di katalog pelanggan)
        </label>
    </div>
</div>

<div class="mt-6 flex items-center justify-end gap-3">
    <a href="{{ route('admin.costumes.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Batal</a>
    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">
        <i class="ri-save-3-line"></i> Simpan
    </button>
</div>
