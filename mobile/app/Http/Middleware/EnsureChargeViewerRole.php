<?php

namespace App\Http\Middleware;

use App\Services\AuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureChargeViewerRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = app(AuthService::class)->currentUser();

        if (! $user || ! in_array($user->role, ['resident', 'manager', 'admin'], true)) {
            return redirect()->route('home')->with('error_message', 'این بخش مخصوص ساکنان و مدیران ساختمان است.');
        }

        return $next($request);
    }
}
