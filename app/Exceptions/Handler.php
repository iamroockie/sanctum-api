<?php

namespace App\Exceptions;

use App\Support\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends \Illuminate\Foundation\Exceptions\Handler
{
    public function render($request, Throwable $e)
    {
        // Log::error($e->getMessage());

        foreach ($this->handlers() as $exceptionType => $handler) {
            if ($e instanceof $exceptionType && method_exists($this, $handler)) {
                return $this->$handler($e);
            }
        }

        return Response::fail(
            [
                'message' => $e->getMessage(),
                'exception' => class_basename($e),
            ],
            $e->getCode() ?: HttpResponse::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    protected function handlers(): array
    {
        return [
            NotFoundHttpException::class => 'handleNotFound',
            MethodNotAllowedHttpException::class => 'handleNotSupportHttpMethod',
        ];
    }

    protected function handleNotFound(NotFoundHttpException $e): \Illuminate\Http\Response
    {
        $message = 'Ресурс не найден.';

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
}
