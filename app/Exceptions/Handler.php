<?php

namespace App\Exceptions;

use Throwable;
use BadMethodCallException;
use Illuminate\Http\Request;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            return response()->json([
                'error' => 'Not Found',
                'message' => $e->getMessage(),
            ], 404);
        });

        $this->renderable(function (MethodNotAllowedHttpException $e, Request $request) {
            return response()->json([
                'error' => 'Method Not Allowed',
                'message' => $e->getMessage(),
            ], 405);
        });

        $this->renderable(function (BadMethodCallException $e) {
            return response()->json([
                'error' => 'Bad Method Call',
                'message' => $e->getMessage(),
            ], 500);
        });
    }
}
