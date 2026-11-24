<?php

namespace Core;

/**
 * Estandariza todas las respuestas JSON emitidas por la API.
 * Garantiza codigos HTTP coherentes (200, 201, 400, 404, 422, 500)
 * y una estructura uniforme {"success": bool, "message": string, "data": mixed}.
 */
class Response
{
    public static function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
        );

        exit;
    }

    public static function success(mixed $data = null, string $message = 'Operacion exitosa', int $status = 200): void
    {
        self::json([
            'success' => true,
            'message' => $message,
            'data'    => $data
        ], $status);
    }

    public static function error(string $message, int $status = 400): void
    {
        self::json([
            'success' => false,
            'message' => $message
        ], $status);
    }
}
