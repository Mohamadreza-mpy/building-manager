<?php

namespace App\Http\Middleware;

use App\Services\AuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureResidentRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = app(AuthService::class)->currentUser();

        if (! $user || $user->role !== 'resident') {
            return redirect()->route('home')->with('error_message', 'این بخش مخصوص ساکنان است.');
        }

        return $next($request);
    }
}
