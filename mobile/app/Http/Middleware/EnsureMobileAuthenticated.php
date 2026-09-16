<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use App\Exceptions\UnauthorizedApiException;
use App\Services\AuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMobileAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        $auth = app(AuthService::class);

        if (! $auth->hasToken()) {
            return redirect()->route('login');
        }

        try {
            if (! $auth->currentUser()) {
                return redirect()->route('login');
            }
        } catch (UnauthorizedApiException) {
            return redirect()->route('login')->with('auth_error', 'نشست شما منقضی شده است. دوباره وارد شوید.');
        } catch (ApiException $exception) {
            return redirect()->route('login')->with('auth_error', $exception->getMessage());
        }

        return $next($request);
    }
}
