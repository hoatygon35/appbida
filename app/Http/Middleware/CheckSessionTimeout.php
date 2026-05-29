<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckSessionTimeout {
    public function handle($request, Closure $next) {
        if (Auth::check()) {
            $lastActivity = session('last_activity');
            if ($lastActivity && (time() - $lastActivity > 7200)) { // 2 giờ
                Auth::logout();
                session()->forget('last_activity');
                return redirect()->route('login')->with('error', 'Tự động đăng xuất sau 2 giờ không hoạt động!');
            }
            session(['last_activity' => time()]);
        }
        return $next($request);
    }
}
