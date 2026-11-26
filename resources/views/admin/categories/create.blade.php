@extends('layouts.admin')

@section('title', 'Tambah Kategori · CostumeRent Admin')
@section('page-title', 'Tambah Kategori')

@section('content')
    <div class="mx-auto max-w-2xl">
        <a href="{{ route('admin.categories.index') }}" class="mb-4 inline-flex items-center gap-1.5 text-sm font-medium text-stone-500 transition hover:text-zinc-900">
            <i class="ri-arrow-left-line"></i> Kembali ke daftar kategori
        </a>
        <form method="POST" action="{{ route('admin.categories.store') }}" class="rounded-2xl border border-stone-200 bg-white p-6 shadow-soft">
            @csrf
            @include('admin.categories._form')
        </form>
    </div>
@endsection
