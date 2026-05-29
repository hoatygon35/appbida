<x-guest-layout>
    <!-- Card container -->
    <div class="bg-white border border-indigo-100 rounded-3xl p-8 shadow-xl shadow-indigo-100/50">
        
        <!-- Logo top -->
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-gradient-to-tr from-indigo-600 to-indigo-800 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-600/20 mx-auto mb-4">
                <!-- SVG Icon like a glowing star -->
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                </svg>
            </div>
            
            <h2 class="text-2xl font-black tracking-wide text-slate-800">Quản lý Cafe-Billards</h2>
            <p class="text-xs text-slate-500 mt-1">Đăng nhập để quản lý tài khoản & bàn chơi</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Username -->
            <div>
                <label for="username" class="block text-xs font-semibold text-slate-500 mb-1.5">Tên đăng nhập</label>
                <div class="relative">
                    <input id="username" type="text" name="username" :value="old('username')" required autofocus autocomplete="username" 
                           class="w-full bg-white border border-slate-250 rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition duration-150">
                </div>
                <x-input-error :messages="$errors->get('username')" class="mt-1" />
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-xs font-semibold text-slate-500 mb-1.5">Mật khẩu</label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                       placeholder="••••••••"
                       class="w-full bg-white border border-slate-250 rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition duration-150">
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between text-xs">
                <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                    <input id="remember_me" type="checkbox" checked class="rounded border-slate-350 bg-white text-indigo-600 shadow-sm focus:ring-0 focus:ring-offset-0 focus:outline-none w-4 h-4 cursor-pointer" name="remember">
                    <span class="ms-2 text-slate-500 font-medium">Ghi nhớ đăng nhập</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit" 
                        class="w-full py-3.5 bg-gradient-to-r from-indigo-600 to-indigo-800 hover:from-indigo-750 hover:to-indigo-850 text-white font-extrabold rounded-xl transition duration-150 shadow-md shadow-indigo-600/10 active:scale-95 flex justify-center items-center gap-1.5 cursor-pointer">
                    Đăng nhập
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
