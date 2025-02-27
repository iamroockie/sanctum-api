<?php

namespace App\Exceptions;

use App\Support\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends \Illuminate\Foundation\Exceptions\Handler
{
    public function render($request, Throwable $e)
    {
        foreach ($this->handlers() as $exceptionType => $handler) {
            if ($e instanceof $exceptionType && method_exists($this, $handler)) {
                return $this->$handler($e, $request);
            }
        }

        Log::error($e->getMessage());

        return Response::fail(
            [
                'message' => $e->getMessage(),
                'exception' => $e::class,
            ],
            $e->getCode() ?: HttpResponse::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    protected function handlers(): array
    {
        return [
            NotFoundHttpException::class => 'handleNotFound',
            MethodNotAllowedHttpException::class => 'handleNotSupportHttpMethod',
            ValidationException::class => 'handleValidation',
            AuthenticationException::class => 'handleAuthentication',
            AlreadyAuthenticatedException::class => 'handleAlreadyAuthenticated',
            // RuntimeException::class => 'handleRuntimeException',
        ];
    }

    protected function handleNotFound(NotFoundHttpException $e, $request): \Illuminate\Http\Response
    {
        $message = 'Ресурс не найден. '.$request->getPathInfo();

        return Response::fail(compact('message'), HttpResponse::HTTP_NOT_FOUND);
    }

    protected function handleNotSupportHttpMethod(MethodNotAllowedHttpException $e): \Illuminate\Http\Response
    {
        $headers = $e->getHeaders();

        $message = 'Метод недоступен для данного маршрута.';

        if (! empty($headers['Allow'])) {
            $message .= ' Доступные методы: '.$headers['Allow'];
        }

        return Response::fail(compact('message'), HttpResponse::HTTP_METHOD_NOT_ALLOWED, $headers);
    }

    protected function handleValidation(ValidationException $e): \Illuminate\Http\Response
    {
        return Response::fail([
            'message' => 'Ошибка валидации.',
            'errors' => $e->errors(),
        ], HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
    }

    protected function handleAuthentication(): \Illuminate\Http\Response
    {
        return Response::fail(['message' => 'Ошибка аутентификации.'], HttpResponse::HTTP_UNAUTHORIZED);
    }

    protected function handleAlreadyAuthenticated(): \Illuminate\Http\Response
    {
        return Response::fail(['message' => 'Вход уже выполнен ранее.'], HttpResponse::HTTP_FORBIDDEN);
    }

    protected function handleRuntimeException(): \Illuminate\Http\Response
    {
        return Response::fail(['message' => 'Внутрення ошибка.'], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}
