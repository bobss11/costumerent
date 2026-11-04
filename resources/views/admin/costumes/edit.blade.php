@extends('layouts.admin')

@section('title', 'Edit Kostum · CostumeRent Admin')
@section('page-title', 'Edit Kostum')

@section('content')
    <div class="mx-auto max-w-3xl">
        <a href="{{ route('admin.costumes.index') }}" class="mb-4 inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-slate-800">
            <i class="ri-arrow-left-line"></i> Kembali ke daftar kostum
        </a>
        <form method="POST" action="{{ route('admin.costumes.update', $costume) }}" class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')
            @include('admin.costumes._form')
        </form>
    </div>
@endsection
