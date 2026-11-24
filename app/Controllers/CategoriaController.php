<?php

namespace App\Controllers;

use App\Models\Categoria;
use Core\Request;
use Core\Response;
use PDO;

/**
 * Controlador para gestionar el recurso de Categorias de turismo.
 */
class CategoriaController
{
    private Categoria $categoria;

    public function __construct(PDO $db)
    {
        $this->categoria = new Categoria($db);
    }

    public function index(): void
    {
        $categorias = $this->categoria->obtenerTodos();
        Response::success($categorias, 'Lista de categorias obtenida correctamente');
    }

    public function show(int $id): void
    {
        $item = $this->categoria->obtenerPorId($id);

        if (!$item) {
            Response::error('La categoria con ID ' . $id . ' no fue encontrada', 404);
        }

        Response::success($item, 'Categoria obtenida exitosamente');
    }

    public function store(): void
    {
        $data = Request::body();

        $nombre = trim($data['nombre_categoria'] ?? '');
        $nivel = trim($data['nivel_exigencia'] ?? '');

        if (empty($nombre)) {
            Response::error('El nombre de la categoria es obligatorio', 422);
        }

        if (empty($nivel)) {
            Response::error('El nivel de exigencia es obligatorio (Bajo, Moderado, Alto o Extremo)', 422);
        }

        if ($this->categoria->obtenerPorNombre($nombre)) {
            Response::error('Ya existe una categoria registrada con el nombre "' . $nombre . '"', 400);
        }

        $idGenerado = $this->categoria->crear($data);

        Response::success([
            'id_categoria' => $idGenerado,
            'nombre_categoria' => $nombre
        ], 'Categoria registrada exitosamente', 201);
    }

    public function update(int $id): void
    {
        $existe = $this->categoria->obtenerPorId($id);
        if (!$existe) {
            Response::error('No existe la categoria con ID ' . $id, 404);
        }

        $data = Request::body();
        $nombre = trim($data['nombre_categoria'] ?? '');
        $nivel = trim($data['nivel_exigencia'] ?? '');

        if (empty($nombre)) {
            Response::error('El nombre de la categoria no puede estar vacio', 422);
        }

        if (empty($nivel)) {
            Response::error('El nivel de exigencia no puede estar vacio', 422);
        }

        $this->categoria->actualizar($id, $data);

        Response::success(null, 'Categoria actualizada correctamente');
    }

    public function destroy(int $id): void
    {
        $existe = $this->categoria->obtenerPorId($id);
        if (!$existe) {
            Response::error('No existe la categoria con ID ' . $id, 404);
        }

        $this->categoria->eliminar($id);

        Response::success(null, 'Categoria desactivada correctamente');
    }
}
