<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem MBG</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #16a34a 0%, #15803d 50%, #14532d 100%); }
        .food-pattern { background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"); }
    </style>
</head>
<body class="min-h-screen flex">
    {{-- Left Side - Branding --}}
    <div class="hidden lg:flex lg:w-1/2 gradient-bg food-pattern flex-col justify-center items-center p-12 text-white">
        <div class="max-w-md text-center">
            <div class="w-24 h-24 bg-white/20 rounded-3xl flex items-center justify-center mx-auto mb-8 backdrop-blur-sm border border-white/30">
                <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
            <h1 class="text-4xl font-extrabold mb-4 leading-tight">
                Sistem MBG<br>
                <span class="text-green-200">Makanan Bergizi Gratis</span>
            </h1>
            <p class="text-green-100 text-lg leading-relaxed mb-10">
                Platform digital untuk monitoring dan tracking distribusi makanan bergizi gratis kepada siswa sekolah dasar.
            </p>

            <div class="space-y-4 text-left">
                @foreach([
                    ['icon' => '🚚', 'title' => 'Live Tracking Kurir', 'desc' => 'Pantau lokasi kurir secara realtime di peta'],
                    ['icon' => '📢', 'title' => 'Notifikasi Instan', 'desc' => 'Orang tua langsung mendapat info status makanan'],
                    ['icon' => '✅', 'title' => 'Konfirmasi Digital', 'desc' => 'Guru & orang tua konfirmasi penerimaan makanan'],
                ] as $feature)
                <div class="flex items-start gap-4 bg-white/10 rounded-2xl p-4 backdrop-blur-sm border border-white/20">
                    <span class="text-2xl">{{ $feature['icon'] }}</span>
                    <div>
                        <p class="font-semibold">{{ $feature['title'] }}</p>
                        <p class="text-green-200 text-sm">{{ $feature['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Right Side - Login Form --}}
    <div class="flex-1 flex items-center justify-center p-8 bg-gray-50">
        <div class="w-full max-w-md">
            {{-- Mobile Logo --}}
            <div class="lg:hidden text-center mb-8">
                <div class="w-16 h-16 bg-green-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Sistem MBG</h1>
            </div>

            <div class="bg-white rounded-3xl shadow-xl p-8 border border-gray-100">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Selamat Datang 👋</h2>
                <p class="text-gray-500 mb-8">Masuk ke akun Anda untuk melanjutkan</p>

                @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                    @foreach($errors->all() as $error)
                    <p class="text-red-600 text-sm">{{ $error }}</p>
                    @endforeach
                </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="nama@email.com"
                            required
                            class="w-full px-4 py-3.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all text-gray-800 placeholder-gray-400 bg-gray-50 focus:bg-white"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <input
                                type="password"
                                name="password"
                                id="password"
                                placeholder="••••••••"
                                required
                                class="w-full px-4 py-3.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all text-gray-800 placeholder-gray-400 bg-gray-50 focus:bg-white pr-12"
                            >
                            <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            <span class="text-sm text-gray-600">Ingat saya</span>
                        </label>
                    </div>
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3.5 rounded-xl transition-all duration-200 shadow-lg shadow-green-200 hover:shadow-green-300 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Masuk
                    </button>
                </form>

                {{-- Demo Accounts --}}
                <div class="mt-8 pt-6 border-t border-gray-100">
                    <p class="text-xs font-semibold text-gray-400 text-center uppercase tracking-wider mb-4">Akun Demo</p>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach([
                            ['role' => 'Kurir', 'email' => 'petugas@mbg.test', 'color' => 'blue'],
                            ['role' => 'Guru', 'email' => 'guru@mbg.test', 'color' => 'purple'],
                            ['role' => 'Ortu', 'email' => 'orangtua@mbg.test', 'color' => 'orange'],
                        ] as $demo)
                        <button onclick="fillDemo('{{ $demo['email'] }}')"
                            class="text-center p-3 rounded-xl border-2 border-dashed border-gray-200 hover:border-{{ $demo['color'] }}-300 hover:bg-{{ $demo['color'] }}-50 transition-all cursor-pointer group">
                            <p class="text-xs font-semibold text-gray-600 group-hover:text-{{ $demo['color'] }}-700">{{ $demo['role'] }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ explode('@', $demo['email'])[0] }}</p>
                        </button>
                        @endforeach
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            input.type = input.type === 'password' ? 'text' : 'password';
        }
        function fillDemo(email) {
            document.querySelector('[name=email]').value = email;
            document.querySelector('[name=password]').value = 'password';
        }
    </script>
</body>
</html>
