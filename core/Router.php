<?php

namespace Core;

/**
 * Enrutador de la aplicacion que registra y despacha endpoints RESTful.
 * Soporta metodos GET, POST, PUT, DELETE y parametros dinamicos como {id}.
 */
class Router
{
    private array $routes = [];

    public function get(string $route, callable $action): void
    {
        $this->add('GET', $route, $action);
    }

    public function post(string $route, callable $action): void
    {
        $this->add('POST', $route, $action);
    }

    public function put(string $route, callable $action): void
    {
        $this->add('PUT', $route, $action);
    }

    public function delete(string $route, callable $action): void
    {
        $this->add('DELETE', $route, $action);
    }

    private function add(string $method, string $route, callable $action): void
    {
        // Normalizamos la ruta eliminando la barra final si no es la raiz
        if ($route !== '/') {
            $route = rtrim($route, '/');
        }

        $this->routes[] = [
            'method' => strtoupper($method),
            'route'  => $route,
            'action' => $action
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);

        // Obtenemos unicamente el PATH ignorando posibles query strings (?param=valor)
        $uri = parse_url($uri, PHP_URL_PATH) ?? '/';
        $uri = rawurldecode($uri);

        if (!str_starts_with($uri, '/')) {
            $uri = '/' . $uri;
        }

        if ($uri !== '/') {
            $uri = rtrim($uri, '/');
        }

        // Buscamos coincidencia entre las rutas registradas
        foreach ($this->routes as $route) {
            // Convertimos parametros dinamicos como {id} en expresiones regulares (\d+)
            $pattern = preg_quote($route['route'], '#');
            $pattern = str_replace('\{id\}', '(\d+)', $pattern);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                // Verificamos que el metodo HTTP coincida (GET, POST, etc.)
                if ($route['method'] !== $method) {
                    continue;
                }

                // Eliminamos la coincidencia completa para quedarnos solo con los parametros capturados
                array_shift($matches);

                // Convertimos parametros a enteros si son numericos
                $params = array_map(function ($val) {
                    return is_numeric($val) ? (int)$val : $val;
                }, $matches);

                // Ejecutamos la accion del controlador pasando los parametros
                call_user_func_array($route['action'], $params);
                return;
            }
        }

        // Si recorrio todas las rutas y no hubo coincidencia
        Response::error('Ruta no encontrada en el servidor: ' . $uri, 404);
    }
}
