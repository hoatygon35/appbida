<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Quản lý Cafe-Billards</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                font-family: 'Outfit', sans-serif;
            }
        </style>
    </head>
    <body class="text-slate-800 antialiased bg-[#f0f2fe] overflow-x-hidden min-h-screen relative flex items-center justify-center">
        <!-- Glowing background blobs with soft light colors -->
        <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-indigo-400/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-purple-400/25 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-[30%] right-[10%] w-[30%] h-[30%] bg-blue-400/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="w-full sm:max-w-md px-4 py-8 relative z-10 flex flex-col justify-center">
            {{ $slot }}
        </div>
    </body>
</html>
