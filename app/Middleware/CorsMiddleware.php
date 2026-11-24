<?php

namespace App\Middleware;

/**
 * Middleware para el manejo de cabeceras CORS (Cross-Origin Resource Sharing).
 * Permite que clientes web (fetch, Postman, Thunder Client) se comuniquen
 * con la API sin bloqueos de seguridad del navegador.
 */
class CorsMiddleware
{
    public static function handle(): void
    {
        // Permitimos peticiones desde cualquier origen (localhost, 127.0.0.1, etc.)
        header('Access-Control-Allow-Origin: *');

        // Metodos HTTP permitidos para nuestro CRUD
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

        // Cabeceras permitidas en las peticiones
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

        // Codificacion y tipo de contenido por defecto
        header('Content-Type: application/json; charset=UTF-8');

        // Si es una solicitud de sondeo preflight OPTIONS, respondemos 204 y terminamos
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }
}
