<?php

namespace Config;

/**
 * Clase utilitaria para cargar variables de entorno desde el archivo .env.
 * Nos permite mantener seguras las credenciales de la base de datos sin
 * exponerlas en el repositorio de GitHub tal como explico el profesor.
 */
class Env
{
    private static array $variables = [];

    public static function load(string $filePath): void
    {
        if (!file_exists($filePath)) {
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);

            // Ignoramos comentarios que empiecen con #
            if (empty($line) || str_starts_with($line, '#')) {
                continue;
            }

            // Dividimos en nombre de variable y su valor
            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Quitamos comillas si vienen con comillas simples o dobles
                $value = trim($value, "\"'");

                self::$variables[$key] = $value;
                $_ENV[$key] = $value;
                putenv("{$key}={$value}");
            }
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::$variables[$key] ?? $_ENV[$key] ?? getenv($key) ?: $default;
    }
}
