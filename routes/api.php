<?php

// =============================================================================
// DEFINICION DE RUTAS RESTFUL - ECORUTA PERU API
// Organizado por modulos correspondientes a las 6 tablas principales del proyecto.
// =============================================================================

use App\Controllers\CategoriaController;
use App\Controllers\GuiaController;
use App\Controllers\RegionController;
use App\Controllers\ReservaController;
use App\Controllers\RutaController;
use App\Controllers\UsuarioController;

// 1. Instanciamos los controladores inyectando la conexion PDO
$regionController    = new RegionController($db);
$categoriaController = new CategoriaController($db);
$rutaController      = new RutaController($db);
$guiaController      = new GuiaController($db);
$usuarioController   = new UsuarioController($db);
$reservaController   = new ReservaController($db);


// =============================================================================
// MODULO 1: REGIONES (/api/regiones)
// =============================================================================

// Listar todas las regiones
$router->get('/api/regiones', [$regionController, 'index']);

// Obtener una region por su identificador
$router->get('/api/regiones/{id}', [$regionController, 'show']);

// Registrar una nueva region
$router->post('/api/regiones', [$regionController, 'store']);

// Actualizar datos de una region existente
$router->put('/api/regiones/{id}', [$regionController, 'update']);

// Desactivar / eliminar logicamente una region
$router->delete('/api/regiones/{id}', [$regionController, 'destroy']);


// =============================================================================
// MODULO 2: CATEGORIAS (/api/categorias)
// =============================================================================

// Listar categorias de ecoturismo
$router->get('/api/categorias', [$categoriaController, 'index']);

// Obtener categoria por ID
$router->get('/api/categorias/{id}', [$categoriaController, 'show']);

// Crear categoria
$router->post('/api/categorias', [$categoriaController, 'store']);

// Actualizar categoria
$router->put('/api/categorias/{id}', [$categoriaController, 'update']);

// Desactivar categoria
$router->delete('/api/categorias/{id}', [$categoriaController, 'destroy']);


// =============================================================================
// MODULO 3: RUTAS ECOTURISTICAS (/api/rutas) - MODULO PRINCIPAL
// =============================================================================

// Listar todas las rutas con JOIN a regiones y categorias
$router->get('/api/rutas', [$rutaController, 'index']);

// Endpoint de busqueda y filtro (se registra antes de {id} para evitar colisiones)
$router->get('/api/rutas/buscar', [$rutaController, 'buscar']);

// Obtener ficha tecnica de una ruta por ID
$router->get('/api/rutas/{id}', [$rutaController, 'show']);

// Registrar una nueva ruta ecoturistica
$router->post('/api/rutas', [$rutaController, 'store']);

// Actualizar datos tecnicos de una ruta
$router->put('/api/rutas/{id}', [$rutaController, 'update']);

// Cambiar estado a Cerrado / eliminar ruta
$router->delete('/api/rutas/{id}', [$rutaController, 'destroy']);


// =============================================================================
// MODULO 4: GUIAS Y ARRIEROS (/api/guias)
// =============================================================================

// Listar guias oficiales acreditados por SERNANP
$router->get('/api/guias', [$guiaController, 'index']);

// Obtener guia por ID
$router->get('/api/guias/{id}', [$guiaController, 'show']);

// Registrar nuevo guia acreditado
$router->post('/api/guias', [$guiaController, 'store']);

// Actualizar datos de contacto y disponibilidad
$router->put('/api/guias/{id}', [$guiaController, 'update']);

// Cambiar estado del guia a Inactivo
$router->delete('/api/guias/{id}', [$guiaController, 'destroy']);


// =============================================================================
// MODULO 5: USUARIOS SENDERISTAS (/api/usuarios)
// =============================================================================

// Listar usuarios registrados
$router->get('/api/usuarios', [$usuarioController, 'index']);

// Obtener usuario por ID
$router->get('/api/usuarios/{id}', [$usuarioController, 'show']);

// Registrar nuevo senderista
$router->post('/api/usuarios', [$usuarioController, 'store']);

// Actualizar informacion del senderista
$router->put('/api/usuarios/{id}', [$usuarioController, 'update']);

// Eliminar usuario
$router->delete('/api/usuarios/{id}', [$usuarioController, 'destroy']);


// =============================================================================
// MODULO 6: RESERVAS DE TOURS (/api/reservas) - MODULO TRANSACCIONAL
// =============================================================================

// Listar todas las reservas con datos consolidados
$router->get('/api/reservas', [$reservaController, 'index']);

// Obtener detalle de una reserva por ID
$router->get('/api/reservas/{id}', [$reservaController, 'show']);

// Crear nueva reserva de tour
$router->post('/api/reservas', [$reservaController, 'store']);

// Actualizar estado de reserva o asignar guia
$router->put('/api/reservas/{id}', [$reservaController, 'update']);

// Cancelar una reserva
$router->delete('/api/reservas/{id}', [$reservaController, 'destroy']);
