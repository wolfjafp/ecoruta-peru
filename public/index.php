<?php

// =============================================================================
// FRONT CONTROLLER - ECORUTA PERU API MVC
// Punto de entrada unico para todas las peticiones a la API REST.
// =============================================================================

require_once __DIR__ . '/../vendor/autoload.php';

use App\Middleware\CorsMiddleware;
use Config\Database;
use Config\Env;
use Core\Response;
use Core\Router;

// 1. Cargar variables de entorno desde el archivo .env
Env::load(__DIR__ . '/../.env');

// 2. Gestion de CORS
CorsMiddleware::handle();

// 3. Inicializar conexion a MySQL mediante PDO
try {
    $database = new Database();
    $db = $database->connect();
} catch (Exception $e) {
    Response::error('Error critico en la conexion de base de datos: ' . $e->getMessage(), 500);
}

// 4. Instanciar enrutador
$router = new Router();

// 5. Cargar definicion de rutas
require_once __DIR__ . '/../routes/api.php';

// 6. Obtener metodo HTTP y URI solicitada
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

// 7. Normalizar URI para que funcione tanto en Apache (XAMPP) en cualquier subcarpeta
//    como en el servidor embebido de PHP (localhost:8000).
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
if ($scriptDir !== '/' && $scriptDir !== '.' && !empty($scriptDir) && str_starts_with($uri, $scriptDir)) {
    $uri = substr($uri, strlen($scriptDir));
}

// Limpiamos prefijo /public si estuviera presente
if (str_starts_with($uri, '/public')) {
    $uri = substr($uri, 7);
}

if ($uri === '' || $uri === false) {
    $uri = '/';
}

// Ruta de bienvenida en la raiz de la API
$router->get('/', function () {
    Response::success([
        'proyecto'    => 'EcoRuta Peru - API REST MVC',
        'curso'       => 'Programacion Web Basica (NRC 3657)',
        'estudiante'  => 'Fernandez Poma Jose Alberto',
        'evaluacion'  => 'PA4 (Evaluacion Continua 4)',
        'servidor'    => 'MySQL PDO en XAMPP',
        'documentacion' => 'Consulte los endpoints en /api/{recurso}'
    ], 'API REST de EcoRuta Peru operativa');
});

// 8. Despachar la peticion al controlador correspondiente
$router->dispatch($method, $uri);
