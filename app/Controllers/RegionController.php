<?php

namespace App\Controllers;

use App\Models\Region;
use Core\Request;
use Core\Response;
use PDO;

/**
 * Controlador para gestionar el recurso de Regiones.
 * Maneja las peticiones HTTP y aplica las validaciones requeridas.
 */
class RegionController
{
    private Region $region;

    public function __construct(PDO $db)
    {
        $this->region = new Region($db);
    }

    public function index(): void
    {
        $regiones = $this->region->obtenerTodos();
        Response::success($regiones, 'Lista de regiones obtenida correctamente');
    }

    public function show(int $id): void
    {
        $item = $this->region->obtenerPorId($id);

        if (!$item) {
            Response::error('La region con ID ' . $id . ' no existe en el sistema', 404);
        }

        Response::success($item, 'Region encontrada correctamente');
    }

    public function store(): void
    {
        $data = Request::body();

        // Validamos campos obligatorios
        $nombre = trim($data['nombre_region'] ?? '');
        $clima = trim($data['clima_predominante'] ?? '');

        if (empty($nombre)) {
            Response::error('El nombre de la region es obligatorio', 422);
        }

        if (empty($clima)) {
            Response::error('El clima predominante es obligatorio', 422);
        }

        if (!isset($data['altitud_promedio_msnm']) || (int)$data['altitud_promedio_msnm'] < 0) {
            Response::error('La altitud promedio debe ser un numero positivo mayor o igual a 0 msnm', 422);
        }

        // Verificamos si ya existe una region con ese nombre
        if ($this->region->obtenerPorNombre($nombre)) {
            Response::error('Ya existe una region registrada con el nombre "' . $nombre . '"', 400);
        }

        $idGenerado = $this->region->crear($data);

        Response::success([
            'id_region' => $idGenerado,
            'nombre_region' => $nombre
        ], 'Region registrada exitosamente', 201);
    }

    public function update(int $id): void
    {
        $existe = $this->region->obtenerPorId($id);
        if (!$existe) {
            Response::error('No se puede actualizar. La region con ID ' . $id . ' no existe', 404);
        }

        $data = Request::body();
        $nombre = trim($data['nombre_region'] ?? '');
        $clima = trim($data['clima_predominante'] ?? '');

        if (empty($nombre)) {
            Response::error('El nombre de la region no puede quedar vacio', 422);
        }

        if (empty($clima)) {
            Response::error('El clima no puede quedar vacio', 422);
        }

        $this->region->actualizar($id, $data);

        Response::success(null, 'Region actualizada correctamente');
    }

    public function destroy(int $id): void
    {
        $existe = $this->region->obtenerPorId($id);
        if (!$existe) {
            Response::error('No se puede eliminar. La region con ID ' . $id . ' no existe', 404);
        }

        $this->region->eliminar($id);

        Response::success(null, 'Region desactivada correctamente del catalogo');
    }
}
