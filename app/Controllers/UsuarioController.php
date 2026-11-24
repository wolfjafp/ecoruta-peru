<?php

namespace App\Controllers;

use App\Models\Usuario;
use Core\Request;
use Core\Response;
use PDO;
use PDOException;

/**
 * Controlador para la gestion de Usuarios Senderistas (clientes).
 */
class UsuarioController
{
    private Usuario $usuario;

    public function __construct(PDO $db)
    {
        $this->usuario = new Usuario($db);
    }

    public function index(): void
    {
        $usuarios = $this->usuario->obtenerTodos();
        Response::success($usuarios, 'Lista de senderistas registrada obtenida correctamente');
    }

    public function show(int $id): void
    {
        $item = $this->usuario->obtenerPorId($id);

        if (!$item) {
            Response::error('El usuario senderista con ID ' . $id . ' no existe', 404);
        }

        Response::success($item, 'Usuario senderista obtenido exitosamente');
    }

    public function store(): void
    {
        $data = Request::body();

        $nombres = trim($data['nombres'] ?? '');
        $apellidos = trim($data['apellidos'] ?? '');
        $correo = trim($data['correo'] ?? '');
        $documento = trim($data['numero_documento'] ?? '');

        if (empty($nombres) || empty($apellidos)) {
            Response::error('Los nombres y apellidos son campos obligatorios', 422);
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            Response::error('Debe ingresar un correo electronico valido', 422);
        }

        if (empty($documento)) {
            Response::error('El numero de documento es obligatorio', 422);
        }

        if ($this->usuario->obtenerPorCorreo($correo)) {
            Response::error('Ya existe un usuario registrado con el correo ' . $correo, 400);
        }

        if ($this->usuario->obtenerPorDocumento($documento)) {
            Response::error('Ya existe un usuario registrado con el documento ' . $documento, 400);
        }

        $idGenerado = $this->usuario->crear($data);

        Response::success([
            'id_usuario' => $idGenerado,
            'nombre_completo' => $nombres . ' ' . $apellidos,
            'correo' => $correo
        ], 'Usuario senderista registrado con exito', 201);
    }

    public function update(int $id): void
    {
        $existe = $this->usuario->obtenerPorId($id);
        if (!$existe) {
            Response::error('No se encontro el usuario con ID ' . $id, 404);
        }

        $data = Request::body();
        $nombres = trim($data['nombres'] ?? '');
        $apellidos = trim($data['apellidos'] ?? '');
        $correo = trim($data['correo'] ?? '');
        $documento = trim($data['numero_documento'] ?? '');

        if (empty($nombres) || empty($apellidos)) {
            Response::error('Nombres y apellidos son obligatorios', 422);
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            Response::error('Correo electronico invalido', 422);
        }

        if (empty($documento)) {
            Response::error('Numero de documento obligatorio', 422);
        }

        $this->usuario->actualizar($id, $data);

        Response::success(null, 'Datos del senderista actualizados correctamente');
    }

    public function destroy(int $id): void
    {
        $existe = $this->usuario->obtenerPorId($id);
        if (!$existe) {
            Response::error('No se encontro el usuario con ID ' . $id, 404);
        }

        try {
            $this->usuario->eliminar($id);
            Response::success(null, 'Usuario senderista eliminado correctamente');
        } catch (PDOException $e) {
            // Manejamos la restriccion de llave foranea si el usuario ya tiene reservas asociadas
            Response::error('No se puede eliminar el usuario porque tiene reservas activas asociadas. Anule primero sus reservas.', 400);
        }
    }
}
