<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
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
            [x-cloak] { display: none !important; }

            /* Color Variables for Themes */
            :root {
                --color-primary: 79, 70, 229; /* Indigo 600 */
                --color-primary-hover: 67, 56, 202; /* Indigo 700 */
                --color-primary-light: #eef2ff; /* Indigo 50 */
                --color-primary-glow: 79, 70, 229, 0.15;
                --color-bg: #f8fafc; /* Slate 50 */
                --color-nav-bg: linear-gradient(135deg, #4f46e5, #3730a3);
                --color-text-main: #0f172a; /* Slate 900 */
                --color-text-muted: #475569; /* Slate 600 */
                --color-border: #e2e8f0; /* Slate 200 */
            }

            body.theme-indigo {
                --color-primary: 79, 70, 229;
                --color-primary-hover: 67, 56, 202;
                --color-primary-light: #eef2ff;
                --color-primary-glow: 79, 70, 229, 0.15;
                --color-bg: #eef2ff; /* Soft Lavender Blue */
                --color-nav-bg: linear-gradient(135deg, #4f46e5, #3730a3);
                --color-text-main: #0f172a;
                --color-text-muted: #475569;
                --color-border: #d5deff;
            }

            body.theme-emerald {
                --color-primary: 5, 150, 105; /* Emerald 600 */
                --color-primary-hover: 4, 120, 87; /* Emerald 700 */
                --color-primary-light: #ecfdf5; /* Emerald 50 */
                --color-primary-glow: 16, 185, 129, 0.15;
                --color-bg: #ecfdf5; /* Soft Mint Green */
                --color-nav-bg: linear-gradient(135deg, #059669, #065f46);
                --color-text-main: #0f172a;
                --color-text-muted: #475569;
                --color-border: #d1fae5;
            }

            body.theme-rose {
                --color-primary: 225, 29, 72; /* Rose 600 */
                --color-primary-hover: 190, 24, 74; /* Rose 700 */
                --color-primary-light: #fff1f2; /* Rose 50 */
                --color-primary-glow: 244, 63, 94, 0.15;
                --color-bg: #fff1f2; /* Soft Blossom Pink */
                --color-nav-bg: linear-gradient(135deg, #db2777, #9d174d);
                --color-text-main: #0f172a;
                --color-text-muted: #475569;
                --color-border: #ffe4e6;
            }

            body.theme-amber {
                --color-primary: 217, 119, 6; /* Amber 600 */
                --color-primary-hover: 180, 83, 9; /* Amber 700 */
                --color-primary-light: #fef3c7; /* Amber 50 */
                --color-primary-glow: 245, 158, 11, 0.15;
                --color-bg: #fdf6e2; /* Soft Golden Cream */
                --color-nav-bg: linear-gradient(135deg, #d97706, #92400e);
                --color-text-main: #0f172a;
                --color-text-muted: #475569;
                --color-border: #fef3c7;
            }

            /* Clean Light Theme Overrides */
            body {
                background-color: var(--color-bg) !important;
                color: var(--color-text-main) !important;
                transition: background-color 0.25s ease, color 0.25s ease;
            }

            /* Card components should remain beautiful clean white */
            .bg-white {
                background-color: #ffffff !important;
                border-color: var(--color-border) !important;
                color: var(--color-text-main) !important;
            }

            /* Sub-container cards / gray bands */
            .bg-slate-50, .bg-slate-100/50, .bg-slate-50/50, .bg-slate-50/70, .bg-slate-100 {
                background-color: #f8fafc !important;
            }

            /* Slate borders */
            .border-slate-100, .border-slate-200, .border-slate-50, .border-indigo-100, .border-indigo-100/50, .border-slate-100/50, .border-emerald-100, .border-rose-100, .border-amber-100/50 {
                border-color: var(--color-border) !important;
            }

            /* High contrast text */
            .text-slate-800, .text-slate-900, .text-gray-800, .text-gray-900, .text-slate-700, .text-slate-600 {
                color: var(--color-text-main) !important;
            }

            .text-slate-500, .text-slate-400, .text-gray-500, .text-gray-400 {
                color: var(--color-text-muted) !important;
            }

            /* Form Elements (Inputs & Selects) */
            input, select, textarea {
                background-color: #ffffff !important;
                border-color: #cbd5e1 !important; /* Slate 300 */
                color: #0f172a !important; /* Slate 900 */
            }
            input:focus, select:focus, textarea:focus {
                border-color: rgb(var(--color-primary)) !important;
                background-color: #ffffff !important;
                box-shadow: 0 0 0 4px rgba(var(--color-primary-glow)) !important;
            }

            /* Active / Hover Buttons */
            .bg-slate-100.hover\:bg-slate-200:hover, .bg-slate-100:hover, .hover\:bg-slate-50:hover, .hover\:bg-indigo-50:hover {
                background-color: #f1f5f9 !important;
                color: #0f172a !important;
            }

            /* Dynamic Theme Helpers */
            .theme-primary-bg { background-color: rgb(var(--color-primary)) !important; }
            .theme-primary-hover-bg:hover { background-color: rgb(var(--color-primary-hover)) !important; }
            .theme-primary-text { color: rgb(var(--color-primary)) !important; }
            .theme-primary-border { border-color: rgb(var(--color-primary)) !important; }
            .theme-primary-light-bg { background-color: var(--color-primary-light) !important; }
            .theme-primary-glow-shadow { box-shadow: 0 4px 12px rgba(var(--color-primary-glow)) !important; }
            .theme-primary-ring { --tw-ring-color: rgba(var(--color-primary-glow)) !important; }

            /* Navigation Bar custom styling (Solid color header bar) */
            .main-navbar {
                background: var(--color-nav-bg) !important;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05) !important;
            }
            /* Target text inside navigation bar to be high contrast white */
            .main-navbar a, .main-navbar button, .main-navbar span:not(.theme-dot) {
                color: #ffffff !important;
            }
            .main-navbar .inline-flex.items-center.px-1.pt-1.border-b-2 {
                color: #ffffff !important;
            }
            .main-navbar .inline-flex.items-center.px-1.pt-1.border-b-2.border-indigo-400 {
                border-color: #ffffff !important;
                font-weight: 800 !important;
            }
            .main-navbar .inline-flex.items-center.px-1.pt-1.border-b-2.border-transparent {
                color: rgba(255, 255, 255, 0.75) !important;
            }
            .main-navbar .inline-flex.items-center.px-1.pt-1.border-b-2.border-transparent:hover {
                color: #ffffff !important;
                border-color: rgba(255, 255, 255, 0.3) !important;
            }
            .main-navbar .bg-slate-50, .main-navbar .bg-slate-100 {
                background-color: rgba(255, 255, 255, 0.15) !important;
                border-color: rgba(255, 255, 255, 0.1) !important;
                color: #ffffff !important;
            }
            .main-navbar .bg-slate-50:hover, .main-navbar .bg-slate-100:hover {
                background-color: rgba(255, 255, 255, 0.25) !important;
            }
            .main-navbar svg {
                stroke: #ffffff !important;
            }
            
            /* Profile dropdown content wrapper overrides */
            .main-navbar .absolute {
                background-color: #ffffff !important;
                border: 1px solid #e2e8f0 !important;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
            }
            .main-navbar .absolute a {
                color: #334155 !important;
            }
            .main-navbar .absolute a:hover {
                background-color: #f1f5f9 !important;
                color: #0f172a !important;
            }
            .main-navbar .absolute form button, .main-navbar .absolute form a {
                color: #334155 !important;
                width: 100% !important;
                text-align: left !important;
            }
            .main-navbar .absolute form button:hover, .main-navbar .absolute form a:hover {
                background-color: #f1f5f9 !important;
                color: #0f172a !important;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-slate-50 pb-20 md:pb-0">
        <!-- Persistent initialization script to avoid FOUC -->
        <script>
            (function () {
                const theme = localStorage.getItem('selected-theme') || 'indigo';
                document.body.classList.add('theme-' + theme);
            })();
        </script>

        <div class="min-h-screen flex flex-col" x-data="{ 
            showThemeSheet: false, 
            showProfileModal: {{ ($errors->updatePassword->any() || $errors->any()) ? 'true' : 'false' }}, 
            activeProfileTab: '{{ $errors->updatePassword->any() ? 'password' : 'info' }}',
            setTheme(themeName) { 
                document.body.className = document.body.className.replace(/\btheme-\S+/g, ''); 
                document.body.classList.add('theme-' + themeName); 
                localStorage.setItem('selected-theme', themeName); 
            } 
        }">
            
            <!-- Top Navigation (Desktop & Mobile Header) -->
            @include('layouts.navigation')

            <!-- Page Heading (Hidden on mobile for app shell feel) -->
            @isset($header)
                <header class="bg-white border-b border-slate-100 hidden md:block">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Trial / Expiry Notification Banner -->
            @php
                $userClub = Auth::user()->club;
                $isExpired = false;
                $daysLeft = null;
                
                if ($userClub && $userClub->expiry_date) {
                    $expiry = \Carbon\Carbon::parse($userClub->expiry_date)->startOfDay();
                    $daysLeft = now()->startOfDay()->diffInDays($expiry, false);
                    if ($daysLeft < 0) {
                        $isExpired = true;
                    }
                }
            @endphp

            @if($userClub && $daysLeft !== null && $daysLeft <= 10 && $daysLeft >= 0)
                <div class="bg-orange-500 text-white px-4 py-2 text-center text-sm font-bold shadow-md relative z-50">
                    Bạn còn {{ $daysLeft }} ngày sử dụng miễn phí, liên hệ Zalo 0795.778.778 để được nâng cấp nhé!
                </div>
            @endif

            @if($isExpired)
                <div class="bg-red-600 text-white px-4 py-2 text-center text-xs font-black shadow-md relative z-50 uppercase tracking-wider">
                    Đã hết hạn sử dụng phần mềm
                </div>
                <main class="flex-grow flex items-center justify-center p-4">
                    <div class="bg-white p-8 rounded-3xl shadow-xl border border-red-100 max-w-md w-full text-center space-y-4">
                        <div class="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <h2 class="text-xl font-black text-slate-800">Tài Khoản Đã Hết Hạn</h2>
                        <p class="text-slate-500 font-medium text-sm">Thời gian sử dụng của chi nhánh đã kết thúc. Vui lòng liên hệ với ban quản trị để gia hạn dịch vụ.</p>
                        <a href="https://zalo.me/0795778778" target="_blank" class="block w-full bg-[#0068FF] hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl shadow-md transition active:scale-95">
                            Liên hệ Zalo: 0795.778.778
                        </a>
                    </div>
                </main>
            @else
                <!-- Main Page Content -->
                <main class="flex-grow">
                    {{ $slot }}
                </main>
            @endif

            <!-- Mobile Bottom Navigation Bar (Android style) -->
            @if(Auth::user()->role !== 'manager')
                <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-100 shadow-xl md:hidden z-40 flex justify-around items-center h-16 pb-safe">
                    <!-- Play Tables Tab -->
                    <a href="{{ route('play-tables.index') }}" 
                       class="flex flex-col items-center justify-center flex-1 h-full text-[10px] font-bold transition {{ request()->routeIs('play-tables.index') ? 'theme-primary-text' : 'text-slate-400' }}">
                        <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                        Sơ đồ bàn
                    </a>

                    <!-- Debts Tab (Visible to admin) -->
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('admin.debts.index') }}" 
                           class="flex flex-col items-center justify-center flex-1 h-full text-[10px] font-bold transition {{ request()->routeIs('admin.debts.index') ? 'theme-primary-text' : 'text-slate-400' }}">
                            <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Quản lý Nợ
                        </a>
                    @endif

                    <!-- Mobile Color Settings Tab -->
                    <button @click="showThemeSheet = true" 
                            class="flex flex-col items-center justify-center flex-1 h-full text-[10px] font-bold text-slate-400 hover:text-slate-600 transition">
                        <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Chọn màu
                    </button>
                </div>
            @endif

            <!-- Bottom Sheet theme selector for Mobile (Native Android trượt lên) -->
            <div x-show="showThemeSheet" 
                 class="fixed inset-0 z-50 md:hidden bg-black/60 backdrop-blur-sm"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="showThemeSheet = false"
                 x-cloak>
                
                <div @click.stop 
                     class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl shadow-2xl p-6 space-y-4 max-h-[50vh] flex flex-col"
                     x-show="showThemeSheet"
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="translate-y-full"
                     x-transition:enter-end="translate-y-0"
                     x-transition:leave="transition ease-in duration-200 transform"
                     x-transition:leave-start="translate-y-0"
                     x-transition:leave-end="translate-y-full">
                    
                    <div class="w-12 h-1 bg-slate-300 rounded-full mx-auto mb-2"></div>
                    <div class="flex justify-between items-center pb-2 border-b">
                        <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wide">Thay đổi màu giao diện</h4>
                        <button @click="showThemeSheet = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <!-- Theme Indigo -->
                        <button @click="setTheme('indigo'); showThemeSheet = false;" 
                                class="flex items-center gap-2.5 p-3 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-indigo-50 active:scale-95 transition">
                            <span class="w-5 h-5 rounded-full bg-indigo-600 border border-white/20"></span>
                            <span class="text-xs font-bold text-slate-800">Classic Indigo</span>
                        </button>
                        <!-- Theme Emerald -->
                        <button @click="setTheme('emerald'); showThemeSheet = false;" 
                                class="flex items-center gap-2.5 p-3 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-emerald-50 active:scale-95 transition">
                            <span class="w-5 h-5 rounded-full bg-emerald-600 border border-white/20"></span>
                            <span class="text-xs font-bold text-slate-800">Emerald Mint</span>
                        </button>
                        <!-- Theme Rose -->
                        <button @click="setTheme('rose'); showThemeSheet = false;" 
                                class="flex items-center gap-2.5 p-3 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-rose-50 active:scale-95 transition">
                            <span class="w-5 h-5 rounded-full bg-rose-600 border border-white/20"></span>
                            <span class="text-xs font-bold text-slate-800">Rose Ruby</span>
                        </button>
                        <!-- Theme Amber -->
                        <button @click="setTheme('amber'); showThemeSheet = false;" 
                                class="flex items-center gap-2.5 p-3 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-amber-50 active:scale-95 transition">
                            <span class="w-5 h-5 rounded-full bg-amber-500 border border-white/20"></span>
                            <span class="text-xs font-bold text-slate-800">Amber Gold</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Floating Success Toast Notification -->
            @if(session('status') === 'password-updated' || session('status') === 'profile-updated' || session('status') === 'qr-updated' || session('status') === 'qr-deleted')
                <div x-data="{ showToast: true }" 
                     x-show="showToast"
                     x-init="setTimeout(() => showToast = false, 4000)"
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="-translate-y-12 opacity-0"
                     x-transition:enter-end="translate-y-0 opacity-100"
                     x-transition:leave="transition ease-in duration-300 transform"
                     x-transition:leave-start="translate-y-0 opacity-100"
                     x-transition:leave-end="-translate-y-12 opacity-0"
                     class="fixed top-5 left-1/2 transform -translate-x-1/2 z-[100] bg-emerald-600 text-white font-extrabold px-6 py-4 rounded-2xl shadow-xl flex items-center gap-3 border border-emerald-500/20 max-w-sm w-[90%] text-center justify-between"
                     x-cloak>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 flex-shrink-0 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path>
                        </svg>
                        <span class="text-xs">
                            @if(session('status') === 'password-updated')
                                Cập nhật mật khẩu thành công!
                            @elseif(session('status') === 'profile-updated')
                                Cập nhật thông tin thành công!
                            @elseif(session('status') === 'qr-updated')
                                Cập nhật mã QR thanh toán thành công!
                            @elseif(session('status') === 'qr-deleted')
                                Đã xóa mã QR thanh toán thành công!
                            @endif
                        </span>
                    </div>
                    <button @click="showToast = false" class="text-white/80 hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endif

            <!-- Profile Modal -->
            <div x-show="showProfileModal" 
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="showProfileModal = false"
                 x-cloak>
                
                <div @click.stop 
                     class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition duration-300 border border-slate-100 flex flex-col max-h-[90vh]"
                     x-show="showProfileModal"
                     x-transition:enter="transition ease-out duration-300 transform scale-95 opacity-0"
                     x-transition:enter-end="scale-100 opacity-100"
                     x-transition:leave="transition ease-in duration-200 transform scale-100 opacity-100"
                     x-transition:leave-end="scale-95 opacity-0">
                    
                    <!-- Header -->
                    <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl theme-primary-bg flex items-center justify-center text-white font-black text-lg shadow-md theme-primary-glow-shadow">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-slate-800 uppercase tracking-wide">Thông tin cá nhân</h3>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">{{ Auth::user()->username }}</p>
                            </div>
                        </div>
                        <button @click="showProfileModal = false" class="text-slate-400 hover:text-slate-600 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Modal Navigation Tabs -->
                    <div class="flex border-b border-slate-100 bg-slate-50/30 p-1">
                        <button @click="activeProfileTab = 'info'" 
                                :class="activeProfileTab === 'info' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-400 hover:text-slate-600'" 
                                class="flex-1 py-3 text-xs font-bold rounded-2xl transition duration-150 text-center">
                            Hồ sơ của bạn
                        </button>
                        @if(Auth::user()->role === 'admin')
                            <button @click="activeProfileTab = 'qr'" 
                                    :class="activeProfileTab === 'qr' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-400 hover:text-slate-600'" 
                                    class="flex-1 py-3 text-xs font-bold rounded-2xl transition duration-150 text-center">
                                QR Thanh toán
                            </button>
                        @endif
                        <button @click="activeProfileTab = 'password'" 
                                :class="activeProfileTab === 'password' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-400 hover:text-slate-600'" 
                                class="flex-1 py-3 text-xs font-bold rounded-2xl transition duration-150 text-center">
                            Đổi mật khẩu
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="p-6 overflow-y-auto space-y-4">
                        
                        <!-- Tab 1: Account Information -->
                        <div x-show="activeProfileTab === 'info'" class="space-y-4">
                            <!-- Display Name Edit Form -->
                            <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                                @csrf
                                @method('patch')

                                @if ($errors->any() && !$errors->updatePassword->any())
                                    <div class="bg-rose-50 border border-rose-100 text-rose-700 text-xs font-bold p-3.5 rounded-2xl space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <p>• {{ $error }}</p>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">Tên đăng nhập (Username)</label>
                                    <div class="relative">
                                        <input type="text" value="{{ Auth::user()->username }}" disabled
                                               class="w-full px-4 py-3 rounded-xl bg-slate-100 border border-slate-200 text-slate-500 font-bold cursor-not-allowed">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-1.5">
                                    <label for="profile_name" class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">Họ và Tên</label>
                                    <input type="text" name="name" id="profile_name" required value="{{ old('name', Auth::user()->name) }}"
                                           class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-bold focus:outline-none focus:border-indigo-500 focus:bg-white transition">
                                </div>

                                <div class="grid grid-cols-2 gap-3 pt-2">
                                    <div class="space-y-1">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">Vai trò</span>
                                        <span class="inline-block px-3 py-1.5 rounded-lg text-xs font-bold text-slate-650 bg-slate-150/70 border border-slate-200">
                                            @if(Auth::user()->role === 'manager')
                                                Quản lý hệ thống
                                            @elseif(Auth::user()->role === 'admin')
                                                Quản trị chi nhánh
                                            @else
                                                Nhân viên
                                            @endif
                                        </span>
                                    </div>

                                    <div class="space-y-1">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">Chi nhánh</span>
                                        <span class="inline-block px-3 py-1.5 rounded-lg text-xs font-bold text-indigo-750 bg-indigo-50/50 border border-indigo-100 truncate max-w-full">
                                            {{ Auth::user()->role === 'manager' ? 'Tất cả chi nhánh' : (Auth::user()->club->name ?? 'Mặc định') }}
                                        </span>
                                    </div>
                                </div>

                                <div class="space-y-1.5 pt-2">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">Màu giao diện (Theme Colors)</span>
                                    <div class="flex items-center justify-around bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                        <button type="button" @click="setTheme('indigo')" class="flex flex-col items-center gap-1 group">
                                            <span class="w-6 h-6 rounded-full bg-indigo-600 border border-white hover:scale-110 active:scale-95 transition shadow-sm group-hover:ring-2 group-hover:ring-indigo-650/30"></span>
                                            <span class="text-[9px] font-bold text-slate-500">Indigo</span>
                                        </button>
                                        <button type="button" @click="setTheme('emerald')" class="flex flex-col items-center gap-1 group">
                                            <span class="w-6 h-6 rounded-full bg-emerald-600 border border-white hover:scale-110 active:scale-95 transition shadow-sm group-hover:ring-2 group-hover:ring-emerald-600/30"></span>
                                            <span class="text-[9px] font-bold text-slate-500">Emerald</span>
                                        </button>
                                        <button type="button" @click="setTheme('rose')" class="flex flex-col items-center gap-1 group">
                                            <span class="w-6 h-6 rounded-full bg-rose-600 border border-white hover:scale-110 active:scale-95 transition shadow-sm group-hover:ring-2 group-hover:ring-rose-650/30"></span>
                                            <span class="text-[9px] font-bold text-slate-500">Rose</span>
                                        </button>
                                        <button type="button" @click="setTheme('amber')" class="flex flex-col items-center gap-1 group">
                                            <span class="w-6 h-6 rounded-full bg-amber-500 border border-white hover:scale-110 active:scale-95 transition shadow-sm group-hover:ring-2 group-hover:ring-amber-500/30"></span>
                                            <span class="text-[9px] font-bold text-slate-500">Amber</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="pt-4 flex gap-3">
                                    <button type="button" @click="showProfileModal = false"
                                            class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-650 font-bold py-3 px-4 rounded-xl text-xs transition active:scale-95">
                                        Hủy bỏ
                                    </button>
                                    <button type="submit"
                                            class="flex-1 theme-primary-bg theme-primary-hover-bg text-white font-bold py-3 px-4 rounded-xl text-xs shadow-md theme-primary-glow-shadow transition active:scale-95">
                                        Lưu thay đổi
                                    </button>
                                </div>
                            </form>
                        </div>

                        @if(Auth::user()->role === 'admin')
                            <!-- Tab: Branch QR Code -->
                            <div x-show="activeProfileTab === 'qr'" class="space-y-4" x-cloak>
                                <div class="text-[11px] text-slate-500 font-bold leading-relaxed mb-2 uppercase">
                                    Mã QR Thanh Toán của chi nhánh
                                </div>
                                <p class="text-xs text-slate-400 font-medium">Mã QR này sẽ tự động được đính kèm ở cuối hóa đơn thanh toán khi chi nhánh thực hiện in hóa đơn.</p>
                                
                                <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                    @csrf
                                    
                                    <div class="flex flex-col items-center gap-4">
                                        <!-- QR Preview Container -->
                                        <div class="relative group">
                                            <div class="w-36 h-36 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden relative shadow-inner">
                                                @if(Auth::user()->club->qr_code)
                                                    <img src="{{ Auth::user()->club->qr_code }}" class="w-full h-full object-contain" alt="QR Code">
                                                @else
                                                    <div class="text-center p-3 text-slate-450 italic">
                                                        <span class="text-[10px] font-bold uppercase tracking-wider block">Chưa tải lên</span>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            @if(Auth::user()->club->qr_code)
                                                <!-- Delete button directly below preview -->
                                                <div class="text-center mt-2">
                                                    <button type="submit" name="delete_qr" value="1" 
                                                            class="text-rose-600 hover:text-rose-700 text-[11px] font-bold transition flex items-center justify-center gap-1 mx-auto cursor-pointer">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                        Xóa QR hiện tại
                                                    </button>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Form Input Upload (Browse) -->
                                        <div class="w-full space-y-1.5">
                                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">Chọn ảnh QR</label>
                                            <input type="file" name="qr_code" accept="image/*" required
                                                   class="w-full px-3 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 font-bold focus:outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-black file:bg-indigo-50 file:text-indigo-650 transition">
                                        </div>
                                    </div>

                                    <!-- Unified Footer -->
                                    <div class="pt-4 flex gap-3">
                                        <button type="button" @click="showProfileModal = false"
                                                class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-650 font-bold py-3 px-4 rounded-xl text-xs transition active:scale-95">
                                            Hủy bỏ
                                        </button>
                                        <button type="submit"
                                                class="flex-1 theme-primary-bg theme-primary-hover-bg text-white font-bold py-3 px-4 rounded-xl text-xs shadow-md theme-primary-glow-shadow transition active:scale-95 cursor-pointer">
                                            Lưu thay đổi
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif

                        <!-- Tab 2: Change Password -->
                        <div x-show="activeProfileTab === 'password'" class="space-y-4">
                            <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
                                @csrf
                                @method('put')

                                @if ($errors->updatePassword->any())
                                    <div class="bg-rose-50 border border-rose-100 text-rose-700 text-xs font-bold p-3.5 rounded-2xl space-y-1">
                                        @foreach ($errors->updatePassword->all() as $error)
                                            <p>• {{ $error }}</p>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="space-y-1.5">
                                    <label for="current_password" class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">Mật khẩu hiện tại</label>
                                    <input type="password" name="current_password" id="current_password" required placeholder="••••••••"
                                           class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-bold focus:outline-none focus:border-indigo-500 focus:bg-white transition">
                                </div>

                                <div class="space-y-1.5">
                                    <label for="new_password" class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">Mật khẩu mới</label>
                                    <input type="password" name="password" id="new_password" required placeholder="Tối thiểu 8 ký tự"
                                           class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-bold focus:outline-none focus:border-indigo-500 focus:bg-white transition">
                                </div>

                                <div class="space-y-1.5">
                                    <label for="new_password_confirmation" class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">Xác nhận mật khẩu mới</label>
                                    <input type="password" name="password_confirmation" id="new_password_confirmation" required placeholder="••••••••"
                                           class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-bold focus:outline-none focus:border-indigo-500 focus:bg-white transition">
                                </div>

                                <div class="pt-4 flex gap-3">
                                    <button type="button" @click="showProfileModal = false"
                                            class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-650 font-bold py-3 px-4 rounded-xl text-xs transition active:scale-95">
                                        Hủy bỏ
                                    </button>
                                    <button type="submit"
                                            class="flex-1 theme-primary-bg theme-primary-hover-bg text-white font-bold py-3 px-4 rounded-xl text-xs shadow-md theme-primary-glow-shadow transition active:scale-95">
                                        Đổi mật khẩu
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
