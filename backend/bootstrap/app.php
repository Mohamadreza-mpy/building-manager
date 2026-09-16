<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['success' => false, 'message' => 'اطلاعات واردشده معتبر نیست.', 'errors' => $e->errors()], 422);
            }
        });
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['success' => false, 'message' => 'برای دسترسی باید وارد حساب کاربری شوید.', 'errors' => []], 401);
            }
        });
        $exceptions->render(function (AuthorizationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['success' => false, 'message' => 'شما اجازه انجام این عملیات را ندارید.', 'errors' => []], 403);
            }
        });
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['success' => false, 'message' => 'اطلاعات موردنظر یافت نشد.', 'errors' => []], 404);
            }
        });
        $exceptions->render(function (HttpExceptionInterface $e, Request $request) {
            if ($request->is('api/*')) {
                $messages = [403 => 'شما اجازه انجام این عملیات را ندارید.', 404 => 'مسیر یا اطلاعات موردنظر یافت نشد.', 429 => 'تعداد درخواست‌ها بیش از حد مجاز است.'];

                return response()->json(['success' => false, 'message' => $messages[$e->getStatusCode()] ?? 'خطایی در پردازش درخواست رخ داد.', 'errors' => []], $e->getStatusCode());
            }
        });
    })->create();
