<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CostumeRent')</title>

    {{-- Tailwind CSS (CDN) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                },
            },
        };
    </script>

    {{-- Alpine.js (CDN) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Google Fonts: Plus Jakarta Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- RemixIcon (CDN) --}}
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">

    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
    @stack('head')
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
    <nav class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/80 backdrop-blur-md">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8"
             x-data="{ open: false }">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white">
                    <i class="ri-shirt-line text-lg"></i>
                </span>
                <span class="text-lg font-extrabold tracking-tight text-slate-900">Costume<span class="text-indigo-600">Rent</span></span>
            </a>

            <div class="hidden items-center gap-1 md:flex">
                @auth
                    @if(Route::has('customer.catalog'))
                        <a href="{{ route('customer.catalog') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-indigo-600">Katalog</a>
                    @endif
                    @if(Route::has('customer.rentals.index'))
                        <a href="{{ route('customer.rentals.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-indigo-600">Sewa Saya</a>
                    @endif
                    <span class="ml-2 hidden text-sm text-slate-500 lg:inline">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="ml-2">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 px-3.5 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                            <i class="ri-logout-box-r-line"></i> Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">
                        <i class="ri-login-box-line"></i> Masuk
                    </a>
                @endauth
            </div>

            {{-- Mobile toggle --}}
            <button @click="open = !open" class="inline-flex items-center justify-center rounded-lg p-2 text-slate-600 hover:bg-slate-100 md:hidden">
                <i class="ri-menu-line text-xl" x-show="!open"></i>
                <i class="ri-close-line text-xl" x-show="open" x-cloak></i>
            </button>

            {{-- Mobile menu --}}
            <div x-show="open" x-cloak @click.outside="open = false"
                 class="absolute inset-x-0 top-16 border-b border-slate-200 bg-white p-4 shadow-sm md:hidden">
                @auth
                    @if(Route::has('customer.catalog'))
                        <a href="{{ route('customer.catalog') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Katalog</a>
                    @endif
                    @if(Route::has('customer.rentals.index'))
                        <a href="{{ route('customer.rentals.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Sewa Saya</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="w-full rounded-lg bg-slate-900 px-3.5 py-2 text-sm font-semibold text-white hover:bg-slate-800">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block rounded-lg bg-indigo-600 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-indigo-700">Masuk</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
