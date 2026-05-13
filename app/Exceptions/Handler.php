<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    public function register(): void
    {
        $this->renderable(function (\Symfony\Component\Routing\Exception\RouteNotFoundException $e, $request) {

            if (str_contains($e->getMessage(), 'login')) {
                return response()->json([
                    'message' => 'Unauthenticated (login route missing)'
                ], 401);
            }

        });
    }
}