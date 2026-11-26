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
                    colors: {
                        canvas: '#FAFAF9',
                        accent: '#D97706',
                    },
                    fontFamily: {
                        display: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        sans: ['"Inter"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    boxShadow: {
                        soft: '0 2px 12px -4px rgba(0,0,0,0.04)',
                    },
                },
            },
        };
    </script>

    {{-- Alpine.js (CDN) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Google Fonts: Plus Jakarta Sans (display) + Inter (body) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    {{-- RemixIcon (CDN) --}}
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        h1, h2, h3, h4, .font-display { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; letter-spacing: -0.02em; }
        .tabular-nums { font-variant-numeric: tabular-nums; }
        [x-cloak] { display: none !important; }
    </style>
    @stack('head')
</head>
<body class="min-h-screen bg-[#FAFAF9] text-stone-700 antialiased">
    <nav class="sticky top-0 z-40 border-b border-stone-200/70 bg-[#FAFAF9]/85 backdrop-blur-md">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8"
             x-data="{ open: false }">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-zinc-900 text-white">
                    <i class="ri-shirt-line text-lg"></i>
                </span>
                <span class="font-display text-lg font-extrabold tracking-tight text-zinc-900">CostumeRent</span>
            </a>

            {{-- Center pill nav --}}
            <div class="hidden items-center gap-1 rounded-full border border-stone-200/80 bg-white p-1 shadow-soft md:flex">
                @auth
                    @if(Route::has('customer.catalog'))
                        <a href="{{ route('customer.catalog') }}"
                           class="rounded-full px-4 py-1.5 text-sm font-medium transition {{ request()->routeIs('customer.catalog') || request()->routeIs('costumes.*') ? 'bg-zinc-900 text-white' : 'text-stone-600 hover:text-zinc-900' }}">Katalog</a>
                    @endif
                    @if(Route::has('customer.rentals.index'))
                        <a href="{{ route('customer.rentals.index') }}"
                           class="rounded-full px-4 py-1.5 text-sm font-medium transition {{ request()->routeIs('customer.rentals.*') ? 'bg-zinc-900 text-white' : 'text-stone-600 hover:text-zinc-900' }}">Sewa Saya</a>
                    @endif
                @endauth
            </div>

            {{-- Right side --}}
            <div class="hidden items-center gap-3 md:flex">
                @auth
                    <span class="hidden text-sm font-medium text-stone-500 lg:inline">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-full border border-stone-200 bg-white px-4 py-2 text-sm font-semibold text-stone-700 transition hover:border-stone-300 hover:text-zinc-900">
                            <i class="ri-logout-box-r-line"></i> Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 rounded-full bg-zinc-900 px-5 py-2 text-sm font-semibold text-white transition hover:bg-zinc-800">
                        <i class="ri-login-box-line"></i> Masuk
                    </a>
                @endauth
            </div>

            {{-- Mobile toggle --}}
            <button @click="open = !open" class="inline-flex items-center justify-center rounded-xl p-2 text-stone-600 hover:bg-stone-100 md:hidden">
                <i class="ri-menu-line text-xl" x-show="!open"></i>
                <i class="ri-close-line text-xl" x-show="open" x-cloak></i>
            </button>

            {{-- Mobile menu --}}
            <div x-show="open" x-cloak @click.outside="open = false"
                 class="absolute inset-x-0 top-16 border-b border-stone-200 bg-[#FAFAF9] p-4 shadow-soft md:hidden">
                @auth
                    @if(Route::has('customer.catalog'))
                        <a href="{{ route('customer.catalog') }}" class="block rounded-xl px-3 py-2 text-sm font-medium text-stone-600 hover:bg-stone-100">Katalog</a>
                    @endif
                    @if(Route::has('customer.rentals.index'))
                        <a href="{{ route('customer.rentals.index') }}" class="block rounded-xl px-3 py-2 text-sm font-medium text-stone-600 hover:bg-stone-100">Sewa Saya</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="w-full rounded-xl bg-zinc-900 px-3.5 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block rounded-xl bg-zinc-900 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-zinc-800">Masuk</a>
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
