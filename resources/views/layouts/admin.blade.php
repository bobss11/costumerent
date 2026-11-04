<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CostumeRent · Admin')</title>

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

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
    @stack('head')
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
    @php
        $navLinks = [
            ['label' => 'Dashboard', 'icon' => 'ri-dashboard-line', 'route' => 'admin.dashboard'],
            ['label' => 'Antrian Pengambilan', 'icon' => 'ri-hand-coin-line', 'route' => 'admin.pickups.index'],
            ['label' => 'Antrian Pengembalian', 'icon' => 'ri-arrow-go-back-line', 'route' => 'admin.returns.index'],
            ['label' => 'Kostum', 'icon' => 'ri-shirt-line', 'route' => 'admin.costumes.index'],
            ['label' => 'Kategori', 'icon' => 'ri-price-tag-3-line', 'route' => 'admin.categories.index'],
        ];
    @endphp

    <div x-data="{ sidebarOpen: false }" class="flex min-h-screen">
        {{-- Mobile backdrop --}}
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
             class="fixed inset-0 z-30 bg-slate-900/50 lg:hidden"></div>

        {{-- Sidebar --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-40 flex w-64 transform flex-col bg-slate-900 text-slate-100 transition-transform duration-200 lg:translate-x-0">
            <div class="flex h-16 items-center gap-2 border-b border-white/10 px-5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white">
                    <i class="ri-shirt-line text-lg"></i>
                </span>
                <span class="text-lg font-extrabold tracking-tight">Costume<span class="text-amber-400">Rent</span></span>
            </div>
            <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
                @foreach($navLinks as $link)
                    @php
                        $exists = Route::has($link['route']);
                        $active = $exists && request()->routeIs($link['route'].'*');
                    @endphp
                    <a href="{{ $exists ? route($link['route']) : '#' }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                              {{ $active ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <i class="{{ $link['icon'] }} text-lg"></i>
                        <span>{{ $link['label'] }}</span>
                    </a>
                @endforeach
            </nav>
            <div class="border-t border-white/10 p-4 text-xs text-slate-400">
                &copy; {{ date('Y') }} CostumeRent
            </div>
        </aside>

        {{-- Main column --}}
        <div class="flex min-w-0 flex-1 flex-col lg:pl-64">
            {{-- Top bar --}}
            <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-slate-200/80 bg-white/80 px-4 backdrop-blur-md sm:px-6">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 lg:hidden">
                        <i class="ri-menu-line text-xl"></i>
                    </button>
                    <h1 class="text-base font-semibold text-slate-900">@yield('page-title', 'Panel Operasional')</h1>
                </div>
                <div class="flex items-center gap-3" x-data="{ menu: false }">
                    @auth
                        <div class="hidden text-right sm:block">
                            <p class="text-sm font-semibold text-slate-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs capitalize text-slate-500">{{ Auth::user()->role }}</p>
                        </div>
                        <div class="relative">
                            <button @click="menu = !menu" class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-600 font-semibold text-white">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </button>
                            <div x-show="menu" x-cloak @click.outside="menu = false"
                                 class="absolute right-0 mt-2 w-44 rounded-xl border border-slate-200 bg-white p-1.5 shadow-lg">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm font-medium text-rose-600 hover:bg-rose-50">
                                        <i class="ri-logout-box-r-line"></i> Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
