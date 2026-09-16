<?php

namespace App\Http\Middleware;

use App\Services\AuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureManagementRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = app(AuthService::class)->currentUser();

        if (! $user || ! in_array($user->role, ['admin', 'manager'], true)) {
            return redirect()->route('home')->with('error_message', 'دسترسی به مدیریت ساختمان‌ها برای حساب شما فعال نیست.');
        }

        return $next($request);
    }
}
