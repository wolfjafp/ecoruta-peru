<?php

namespace App\Controllers;

use App\Models\Guia;
use Core\Request;
use Core\Response;
use PDO;

/**
 * Controlador para la gestion de Guias y Arrieros autorizados por SERNANP.
 */
class GuiaController
{
    private Guia $guia;

    public function __construct(PDO $db)
    {
        $this->guia = new Guia($db);
    }

    public function index(): void
    {
        $guias = $this->guia->obtenerTodos();
        Response::success($guias, 'Lista de guias acreditados obtenida correctamente');
    }

    public function show(int $id): void
    {
        $item = $this->guia->obtenerPorId($id);

        if (!$item) {
            Response::error('El guia con ID ' . $id . ' no existe en el registro', 404);
        }

        Response::success($item, 'Guia encontrado correctamente');
    }

    public function store(): void
    {
        $data = Request::body();

        $nombres = trim($data['nombres'] ?? '');
        $apellidos = trim($data['apellidos'] ?? '');
        $dni = trim($data['dni'] ?? '');
        $correo = trim($data['correo'] ?? '');
        $telefono = trim($data['telefono'] ?? '');
        $carnet = trim($data['certificacion_sernanp'] ?? '');

        if (empty($nombres) || empty($apellidos)) {
            Response::error('Los nombres y apellidos del guia son obligatorios', 422);
        }

        // Validamos DNI peruano (exactamente 8 digitos numericos)
        if (!preg_match('/^[0-9]{8}$/', $dni)) {
            Response::error('El DNI debe contener exactamente 8 digitos numericos', 422);
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            Response::error('El correo electronico ingresado no tiene un formato valido', 422);
        }

        if (empty($telefono)) {
            Response::error('El telefono de contacto es obligatorio', 422);
        }

        if (empty($carnet)) {
            Response::error('La certificacion o carnet oficial de SERNANP es obligatoria', 422);
        }

        if ($this->guia->obtenerPorDni($dni)) {
            Response::error('Ya existe un guia registrado con el DNI ' . $dni, 400);
        }

        $idGenerado = $this->guia->crear($data);

        Response::success([
            'id_guia' => $idGenerado,
            'nombres_completos' => $nombres . ' ' . $apellidos,
            'dni' => $dni
        ], 'Guia ecoturistico registrado exitosamente', 201);
    }

    public function update(int $id): void
    {
        $existe = $this->guia->obtenerPorId($id);
        if (!$existe) {
            Response::error('No se encontro el guia con ID ' . $id, 404);
        }

        $data = Request::body();
        $nombres = trim($data['nombres'] ?? '');
        $apellidos = trim($data['apellidos'] ?? '');
        $dni = trim($data['dni'] ?? '');
        $correo = trim($data['correo'] ?? '');

        if (empty($nombres) || empty($apellidos)) {
            Response::error('Nombres y apellidos son requeridos', 422);
        }

        if (!preg_match('/^[0-9]{8}$/', $dni)) {
            Response::error('El DNI debe tener 8 digitos numericos', 422);
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            Response::error('Formato de correo electronico invalido', 422);
        }

        $this->guia->actualizar($id, $data);

        Response::success(null, 'Datos del guia actualizados correctamente');
    }

    public function destroy(int $id): void
    {
        $existe = $this->guia->obtenerPorId($id);
        if (!$existe) {
            Response::error('No se encontro el guia con ID ' . $id, 404);
        }

        $this->guia->eliminar($id);

        Response::success(null, 'Guia pasado al estado Inactivo');
    }
}
