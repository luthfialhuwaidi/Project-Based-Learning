<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak | Sistem MBG</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body{font-family:'Inter',sans-serif;}</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-red-50 to-red-100 flex items-center justify-center p-4">
    <div class="text-center max-w-md">
        <div class="text-8xl mb-6">🚫</div>
        <h1 class="text-6xl font-extrabold text-red-600 mb-2">403</h1>
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Akses Ditolak</h2>
        <p class="text-gray-500 mb-8">
            Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.<br>
            Pastikan Anda login dengan role yang sesuai.
        </p>
        <div class="flex gap-3 justify-center">
            <a href="javascript:history.back()"
                class="px-6 py-3 bg-white border-2 border-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-colors">
                ← Kembali
            </a>
            <a href="{{ url('/') }}"
                class="px-6 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700 transition-colors">
                🏠 Beranda
            </a>
        </div>
    </div>
</body>
</html>
