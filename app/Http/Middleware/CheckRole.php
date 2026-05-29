<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole {
    public function handle(Request $request, Closure $next, ...$roles) {
        if (!auth()->check() || !in_array(auth()->user()->role, $roles)) {
            return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập trang này!');
        }
        return $next($request);
    }
}
