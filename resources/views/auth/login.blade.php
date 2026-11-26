<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk · CostumeRent</title>

    {{-- Tailwind CSS (CDN) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { canvas: '#FAFAF9', accent: '#D97706' },
                    fontFamily: {
                        display: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        sans: ['"Inter"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
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
        .font-display { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; letter-spacing: -0.02em; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen bg-[#FAFAF9] text-stone-700 antialiased">
    <div class="flex min-h-screen flex-col lg:flex-row" x-data="loginForm()">
        {{-- Left: editorial showcase (55%) --}}
        <div class="relative hidden overflow-hidden bg-zinc-900 lg:flex lg:w-[55%]">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/23/Javanese_Surakarta_Bride.jpg/960px-Javanese_Surakarta_Bride.jpg"
                 alt="Busana adat" class="absolute inset-0 h-full w-full object-cover opacity-40"
                 onerror="this.style.display='none'">
            <div class="absolute inset-0 bg-gradient-to-tr from-zinc-900 via-zinc-900/80 to-zinc-900/30"></div>
            <div class="relative z-10 flex h-full w-full flex-col justify-between p-12">
                <div class="flex items-center gap-2.5 text-white">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 backdrop-blur">
                        <i class="ri-shirt-line text-xl"></i>
                    </span>
                    <span class="font-display text-lg font-extrabold tracking-tight">CostumeRent</span>
                </div>

                <div class="max-w-lg text-white">
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-300 backdrop-blur">
                        <i class="ri-sparkling-2-line"></i> Boutique Rental
                    </span>
                    <h2 class="font-display mt-6 text-4xl font-extrabold leading-[1.05] tracking-tight xl:text-5xl">
                        Curated Traditional &amp; Event Attire Rental
                    </h2>
                    <p class="mt-5 max-w-md leading-relaxed text-stone-300">
                        Kelola penyewaan kostum, pakaian adat, dan busana acara dengan katalog yang rapi, kalkulasi deposit otomatis, dan inspeksi pengembalian yang transparan.
                    </p>
                    <ul class="mt-8 grid max-w-md gap-3 text-sm text-stone-200">
                        <li class="flex items-center gap-3"><i class="ri-checkbox-circle-line text-amber-300"></i> Katalog &amp; reservasi real-time</li>
                        <li class="flex items-center gap-3"><i class="ri-checkbox-circle-line text-amber-300"></i> Kalkulasi deposit &amp; denda otomatis</li>
                        <li class="flex items-center gap-3"><i class="ri-checkbox-circle-line text-amber-300"></i> Nota &amp; inspeksi pengembalian siap cetak</li>
                    </ul>
                </div>

                <p class="text-xs text-stone-400">&copy; {{ date('Y') }} CostumeRent. Semua hak dilindungi.</p>
            </div>
        </div>

        {{-- Right: form (45%) --}}
        <div class="flex flex-1 items-center justify-center px-5 py-12 lg:w-[45%]">
            <div class="w-full max-w-sm">
                <div class="mb-8 lg:hidden">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-zinc-900 text-white"><i class="ri-shirt-line text-xl"></i></span>
                        <span class="font-display text-lg font-extrabold tracking-tight text-zinc-900">CostumeRent</span>
                    </div>
                </div>

                <h1 class="font-display text-3xl font-extrabold tracking-tight text-zinc-900">Selamat datang kembali</h1>
                <p class="mt-2 text-sm text-stone-500">Masuk untuk mengelola penyewaan kostum.</p>

                @if ($errors->any())
                    <div class="mt-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        <div class="flex items-start gap-2">
                            <i class="ri-error-warning-line mt-0.5"></i>
                            <div>
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="mt-7 space-y-5" x-ref="form">
                    @csrf

                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-semibold text-stone-700">Email</label>
                        <div class="relative">
                            <i class="ri-mail-line pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-stone-400"></i>
                            <input id="email" name="email" type="email" autocomplete="username" required
                                   x-model="email" value="{{ old('email') }}"
                                   class="w-full rounded-xl border border-stone-200 bg-[#FAFAF9] py-3 pl-11 pr-3 text-sm text-zinc-900 outline-none transition focus:border-zinc-400 focus:bg-white focus:ring-2 focus:ring-stone-200"
                                   placeholder="nama@costumerent.local">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-semibold text-stone-700">Kata Sandi</label>
                        <div class="relative" x-data="{ show: false }">
                            <i class="ri-lock-2-line pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-stone-400"></i>
                            <input id="password" name="password" autocomplete="current-password" required
                                   x-model="password" :type="show ? 'text' : 'password'"
                                   class="w-full rounded-xl border border-stone-200 bg-[#FAFAF9] py-3 pl-11 pr-11 text-sm text-zinc-900 outline-none transition focus:border-zinc-400 focus:bg-white focus:ring-2 focus:ring-stone-200"
                                   placeholder="••••••••">
                            <button type="button" @click="show = !show"
                                    class="absolute right-3.5 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-600">
                                <i :class="show ? 'ri-eye-off-line' : 'ri-eye-line'"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-stone-600">
                            <input type="checkbox" name="remember" value="1"
                                   class="h-4 w-4 rounded border-stone-300 text-zinc-900 focus:ring-stone-400">
                            Ingat saya
                        </label>
                    </div>

                    <button type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-zinc-900 px-4 py-3.5 text-sm font-semibold text-white transition hover:bg-zinc-800 focus:ring-2 focus:ring-stone-300">
                        <i class="ri-login-box-line"></i> Masuk
                    </button>
                </form>

                {{-- Quick Demo Login --}}
                <div class="mt-8">
                    <div class="relative mb-4 text-center">
                        <span class="relative z-10 bg-[#FAFAF9] px-3 text-xs font-semibold uppercase tracking-wide text-stone-400">Quick Demo Login</span>
                        <span class="absolute inset-x-0 top-1/2 -z-0 block h-px -translate-y-1/2 bg-stone-200"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-2.5">
                        <button type="button" @click="fill('admin@costumerent.local')"
                                class="flex flex-col items-center gap-1.5 rounded-full border border-stone-200 bg-white px-2 py-3 text-xs font-semibold text-stone-700 transition hover:border-zinc-900 hover:bg-zinc-900 hover:text-white">
                            <i class="ri-shield-user-line text-lg"></i> Admin
                        </button>
                        <button type="button" @click="fill('staff@costumerent.local')"
                                class="flex flex-col items-center gap-1.5 rounded-full border border-stone-200 bg-white px-2 py-3 text-xs font-semibold text-stone-700 transition hover:border-zinc-900 hover:bg-zinc-900 hover:text-white">
                            <i class="ri-user-settings-line text-lg"></i> Staff
                        </button>
                        <button type="button" @click="fill('customer@costumerent.local')"
                                class="flex flex-col items-center gap-1.5 rounded-full border border-stone-200 bg-white px-2 py-3 text-xs font-semibold text-stone-700 transition hover:border-zinc-900 hover:bg-zinc-900 hover:text-white">
                            <i class="ri-user-smile-line text-lg"></i> Customer
                        </button>
                    </div>
                    <p class="mt-3 text-center text-xs text-stone-400">Klik untuk mengisi kredensial otomatis &amp; langsung masuk.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function loginForm() {
            return {
                email: @json(old('email', '')),
                password: '',
                fill(email) {
                    this.email = email;
                    this.password = 'password123';
                    // Auto-submit after filling for a true one-click demo login.
                    this.$nextTick(() => this.$refs.form.submit());
                },
            };
        }
    </script>
</body>
</html>
