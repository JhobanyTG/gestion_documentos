<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use BadMethodCallException;

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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof NotFoundHttpException || $exception instanceof BadMethodCallException) {
            // Si el usuario está autenticado
            if (Auth::check()) {
                // Redirige a la página actual (se utiliza `url()->previous()` para obtener la URL anterior)
                return redirect('/documentos');
            } else {
                // Si el usuario no está autenticado, muestra una página 404
                return response()->view('errors.404', [], 404);
            }
        }

        return parent::render($request, $exception);
    }
}
