<?php

namespace App\Support;

use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Response as IlluminateResponse;

class Response
{
    public static function success(array $data, int $code = 200, $headers = []): HttpResponse
    {
        return IlluminateResponse::make(self::build(true, $data, $code), $code, $headers);
    }

    protected static function build(bool $success, mixed $data, int $code): array
    {
        $response = compact('success', 'code');

        $response[$success ? 'result' : 'error'] = $data;

        return $response;
    }

    public static function fail(array $data, int $code, $headers = []): HttpResponse
    {
        return IlluminateResponse::make(self::build(false, $data, $code), $code, $headers);
    }
}
