@extends('layouts.admin')

@section('title', 'Edit Kategori · CostumeRent Admin')
@section('page-title', 'Edit Kategori')

@section('content')
    <div class="mx-auto max-w-2xl">
        <a href="{{ route('admin.categories.index') }}" class="mb-4 inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-slate-800">
            <i class="ri-arrow-left-line"></i> Kembali ke daftar kategori
        </a>
        <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')
            @include('admin.categories._form')
        </form>
    </div>
@endsection
