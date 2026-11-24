<?php

namespace App\Controllers;

use App\Models\Categoria;
use App\Models\Region;
use App\Models\Ruta;
use Core\Request;
use Core\Response;
use PDO;

/**
 * Controlador principal para el catalogo de Rutas Ecoturisticas.
 * Valida llaves foraneas contra `regiones` y `categorias`, y gestiona el buscador.
 */
class RutaController
{
    private Ruta $ruta;
    private Region $region;
    private Categoria $categoria;

    public function __construct(PDO $db)
    {
        $this->ruta = new Ruta($db);
        $this->region = new Region($db);
        $this->categoria = new Categoria($db);
    }

    public function index(): void
    {
        $rutas = $this->ruta->obtenerTodos();
        Response::success($rutas, 'Catalogo de rutas obtenido correctamente');
    }

    public function show(int $id): void
    {
        $item = $this->ruta->obtenerPorId($id);

        if (!$item) {
            Response::error('La ruta ecoturistica con ID ' . $id . ' no fue encontrada', 404);
        }

        Response::success($item, 'Ruta encontrada exitosamente');
    }

    public function buscar(): void
    {
        // Soporta termino por Query String (?q=...) o en el JSON { "termino": "..." }
        $query = Request::query();
        $body = Request::body();
        $termino = trim($query['q'] ?? $body['termino'] ?? '');

        if (empty($termino)) {
            Response::error('Debe ingresar un termino de busqueda util (parametro "q")', 422);
        }

        $resultados = $this->ruta->buscar($termino);

        Response::success($resultados, 'Busqueda completada para: "' . $termino . '"');
    }

    public function store(): void
    {
        $data = Request::body();

        // Validamos campos obligatorios
        $nombre = trim($data['nombre_ruta'] ?? '');
        $idRegion = (int)($data['id_region'] ?? 0);
        $idCategoria = (int)($data['id_categoria'] ?? 0);
        $altitud = isset($data['altitud_maxima_msnm']) ? (int)$data['altitud_maxima_msnm'] : -1;
        $distancia = isset($data['distancia_km']) ? (float)$data['distancia_km'] : 0.0;
        $precio = isset($data['precio_referencial']) ? (float)$data['precio_referencial'] : -1.0;

        if (empty($nombre)) {
            Response::error('El nombre de la ruta es obligatorio', 422);
        }

        // Validamos que no exista otra ruta con el mismo nombre
        if ($this->ruta->obtenerPorNombre($nombre)) {
            Response::error('Ya existe una ruta ecoturistica registrada con el nombre "' . $nombre . '"', 400);
        }

        if ($idRegion <= 0 || !$this->region->obtenerPorId($idRegion)) {
            Response::error('Debe seleccionar una region valida existente (id_region)', 422);
        }

        if ($idCategoria <= 0 || !$this->categoria->obtenerPorId($idCategoria)) {
            Response::error('Debe seleccionar una categoria valida existente (id_categoria)', 422);
        }

        if ($altitud < 0) {
            Response::error('La altitud maxima debe ser un numero mayor o igual a 0 msnm', 422);
        }

        if ($distancia <= 0) {
            Response::error('La distancia en kilometros debe ser mayor a 0', 422);
        }

        if ($precio < 0) {
            Response::error('El precio referencial no puede ser negativo', 422);
        }

        $idGenerado = $this->ruta->crear($data);

        Response::success([
            'id_ruta' => $idGenerado,
            'nombre_ruta' => $nombre
        ], 'Ruta ecoturistica registrada correctamente', 201);
    }

    public function update(int $id): void
    {
        $existe = $this->ruta->obtenerPorId($id);
        if (!$existe) {
            Response::error('No se encontro la ruta con ID ' . $id, 404);
        }

        $data = Request::body();
        $nombre = trim($data['nombre_ruta'] ?? '');
        $idRegion = (int)($data['id_region'] ?? 0);
        $idCategoria = (int)($data['id_categoria'] ?? 0);

        if (empty($nombre)) {
            Response::error('El nombre de la ruta no puede estar vacio', 422);
        }

        if ($idRegion <= 0 || !$this->region->obtenerPorId($idRegion)) {
            Response::error('La region especificada no existe', 422);
        }

        if ($idCategoria <= 0 || !$this->categoria->obtenerPorId($idCategoria)) {
            Response::error('La categoria especificada no existe', 422);
        }

        $this->ruta->actualizar($id, $data);

        Response::success(null, 'Ruta ecoturistica actualizada correctamente');
    }

    public function destroy(int $id): void
    {
        $existe = $this->ruta->obtenerPorId($id);
        if (!$existe) {
            Response::error('No se encontro la ruta con ID ' . $id, 404);
        }

        $this->ruta->eliminar($id);

        Response::success(null, 'Ruta marcada como Cerrada en el sistema');
    }
}
