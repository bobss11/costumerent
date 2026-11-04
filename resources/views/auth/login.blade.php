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
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
    <div class="flex min-h-screen items-center justify-center px-4 py-10"
         x-data="loginForm()">
        <div class="w-full max-w-md">
            {{-- Brand --}}
            <div class="mb-6 flex flex-col items-center text-center">
                <span class="mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-sm">
                    <i class="ri-shirt-line text-2xl"></i>
                </span>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Costume<span class="text-indigo-600">Rent</span></h1>
                <p class="mt-1 text-sm text-slate-500">Masuk untuk mengelola penyewaan kostum.</p>
            </div>

            {{-- Card --}}
            <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8">
                @if ($errors->any())
                    <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
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

                <form method="POST" action="{{ route('login') }}" class="space-y-5" x-ref="form">
                    @csrf

                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-semibold text-slate-700">Email</label>
                        <div class="relative">
                            <i class="ri-mail-line pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input id="email" name="email" type="email" autocomplete="username" required
                                   x-model="email" value="{{ old('email') }}"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20"
                                   placeholder="nama@costumerent.local">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-semibold text-slate-700">Kata Sandi</label>
                        <div class="relative" x-data="{ show: false }">
                            <i class="ri-lock-2-line pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input id="password" name="password" autocomplete="current-password" required
                                   x-model="password" :type="show ? 'text' : 'password'"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-10 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20"
                                   placeholder="••••••••">
                            <button type="button" @click="show = !show"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                <i :class="show ? 'ri-eye-off-line' : 'ri-eye-line'"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" name="remember" value="1"
                                   class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            Ingat saya
                        </label>
                    </div>

                    <button type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500/40">
                        <i class="ri-login-box-line"></i> Masuk
                    </button>
                </form>

                {{-- Quick Demo Login --}}
                <div class="mt-7">
                    <div class="relative mb-4 text-center">
                        <span class="relative z-10 bg-white px-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Quick Demo Login</span>
                        <span class="absolute inset-x-0 top-1/2 -z-0 block h-px -translate-y-1/2 bg-slate-200"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" @click="fill('admin@costumerent.local')"
                                class="flex flex-col items-center gap-1 rounded-xl border border-slate-200 bg-white px-2 py-3 text-xs font-semibold text-slate-700 transition hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700">
                            <i class="ri-shield-user-line text-lg text-indigo-600"></i> Admin
                        </button>
                        <button type="button" @click="fill('staff@costumerent.local')"
                                class="flex flex-col items-center gap-1 rounded-xl border border-slate-200 bg-white px-2 py-3 text-xs font-semibold text-slate-700 transition hover:border-amber-300 hover:bg-amber-50 hover:text-amber-700">
                            <i class="ri-user-settings-line text-lg text-amber-500"></i> Staff
                        </button>
                        <button type="button" @click="fill('customer@costumerent.local')"
                                class="flex flex-col items-center gap-1 rounded-xl border border-slate-200 bg-white px-2 py-3 text-xs font-semibold text-slate-700 transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700">
                            <i class="ri-user-smile-line text-lg text-emerald-600"></i> Customer
                        </button>
                    </div>
                    <p class="mt-3 text-center text-xs text-slate-400">Klik untuk mengisi kredensial otomatis, lalu tekan Masuk.</p>
                </div>
            </div>

            <p class="mt-6 text-center text-xs text-slate-400">&copy; {{ date('Y') }} CostumeRent. Semua hak dilindungi.</p>
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
