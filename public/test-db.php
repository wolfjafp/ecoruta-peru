<?php

// =============================================================================
// SCRIPT DE PRUEBA DE CONEXION A LA BASE DE DATOS
// Permite comprobar que XAMPP, MySQL y las variables de entorno estan OK.
// =============================================================================

require_once __DIR__ . '/../vendor/autoload.php';

use Config\Database;
use Config\Env;
use Core\Response;

Env::load(__DIR__ . '/../.env');

try {
    $db = (new Database())->connect();

    // Consultamos la version de MySQL y la cantidad de tablas activas
    $stmt = $db->query("SELECT VERSION() AS version, DATABASE() AS base_datos");
    $info = $stmt->fetch();

    $stmtTablas = $db->query("SHOW TABLES");
    $tablas = $stmtTablas->fetchAll(PDO::FETCH_COLUMN);

    Response::success([
        'conexion'   => 'Exitosa',
        'motor'      => 'MySQL / MariaDB',
        'version'    => $info['version'],
        'base_datos' => $info['base_datos'],
        'tablas_encontradas' => $tablas
    ], 'Conexion a la base de datos de EcoRuta Peru verificada correctamente');

} catch (Exception $e) {
    Response::error('Fallo la conexion con la base de datos: ' . $e->getMessage(), 500);
}
