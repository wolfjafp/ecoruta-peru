<?php

namespace Core;

/**
 * Captura y procesa los datos que llegan en la solicitud HTTP.
 */
class Request
{
    /**
     * Lee el cuerpo de la peticion enviado en formato JSON desde Postman, Thunder Client o fetch().
     */
    public static function body(): array
    {
        $input = file_get_contents('php://input');

        if (empty($input)) {
            return [];
        }

        $data = json_decode($input, true);

        return is_array($data) ? $data : [];
    }

    /**
     * Obtiene los parametros enviados por URL (Query String en GET).
     */
    public static function query(): array
    {
        return $_GET ?? [];
    }
}
