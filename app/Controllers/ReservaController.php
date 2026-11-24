<?php

namespace App\Controllers;

use App\Models\Guia;
use App\Models\Reserva;
use App\Models\Ruta;
use App\Models\Usuario;
use Core\Request;
use Core\Response;
use PDO;

/**
 * Controlador para la gestion transaccional de Reservas de Tours Ecoturisticos.
 * Valida la existencia de la ruta, del usuario y del guia, ademas del cupo maximo permitido (1-15).
 */
class ReservaController
{
    private Reserva $reserva;
    private Ruta $ruta;
    private Usuario $usuario;
    private Guia $guia;

    public function __construct(PDO $db)
    {
        $this->reserva = new Reserva($db);
        $this->ruta = new Ruta($db);
        $this->usuario = new Usuario($db);
        $this->guia = new Guia($db);
    }

    public function index(): void
    {
        $reservas = $this->reserva->obtenerTodos();
        Response::success($reservas, 'Lista de reservas de tours obtenida correctamente');
    }

    public function show(int $id): void
    {
        $item = $this->reserva->obtenerPorId($id);

        if (!$item) {
            Response::error('La reserva con ID ' . $id . ' no existe en el sistema', 404);
        }

        Response::success($item, 'Detalle de la reserva obtenido exitosamente');
    }

    public function store(): void
    {
        $data = Request::body();

        $codigo = trim($data['codigo_reserva'] ?? '');
        $idRuta = (int)($data['id_ruta'] ?? 0);
        $idUsuario = (int)($data['id_usuario'] ?? 0);
        $idGuia = !empty($data['id_guia']) ? (int)$data['id_guia'] : null;
        $fechaSalida = trim($data['fecha_salida'] ?? '');
        $personas = (int)($data['numero_personas'] ?? 0);
        $total = isset($data['total_pago']) ? (float)$data['total_pago'] : -1.0;

        if (empty($codigo)) {
            Response::error('El codigo de reserva es obligatorio (ejemplo: RES-2026-007)', 422);
        }

        if ($this->reserva->obtenerPorCodigo($codigo)) {
            Response::error('Ya existe una reserva registrada con el codigo ' . $codigo, 400);
        }

        if ($idRuta <= 0 || !$this->ruta->obtenerPorId($idRuta)) {
            Response::error('Debe seleccionar una ruta ecoturistica valida existente (id_ruta)', 422);
        }

        if ($idUsuario <= 0 || !$this->usuario->obtenerPorId($idUsuario)) {
            Response::error('Debe seleccionar un usuario senderista valido registrado (id_usuario)', 422);
        }

        if ($idGuia !== null && !$this->guia->obtenerPorId($idGuia)) {
            Response::error('El guia asignado no existe en el registro oficial (id_guia)', 422);
        }

        if (empty($fechaSalida)) {
            Response::error('La fecha de salida es obligatoria (formato AAAA-MM-DD)', 422);
        }

        // Regla de negocio SERNANP: grupos de senderismo ecologico entre 1 y 15 personas
        if ($personas < 1 || $personas > 15) {
            Response::error('El numero de senderistas por grupo debe estar entre 1 y 15 personas', 422);
        }

        if ($total < 0) {
            Response::error('El monto total de pago no puede ser negativo', 422);
        }

        $idGenerado = $this->reserva->crear($data);

        Response::success([
            'id_reserva' => $idGenerado,
            'codigo_reserva' => $codigo,
            'total_pago' => $total
        ], 'Reserva de tour registrada con exito', 201);
    }

    public function update(int $id): void
    {
        $existe = $this->reserva->obtenerPorId($id);
        if (!$existe) {
            Response::error('No se encontro la reserva con ID ' . $id, 404);
        }

        $data = Request::body();
        $idRuta = (int)($data['id_ruta'] ?? 0);
        $idUsuario = (int)($data['id_usuario'] ?? 0);
        $idGuia = !empty($data['id_guia']) ? (int)$data['id_guia'] : null;
        $personas = (int)($data['numero_personas'] ?? 0);

        if ($idRuta <= 0 || !$this->ruta->obtenerPorId($idRuta)) {
            Response::error('Ruta ecoturistica invalida', 422);
        }

        if ($idUsuario <= 0 || !$this->usuario->obtenerPorId($idUsuario)) {
            Response::error('Usuario senderista invalido', 422);
        }

        if ($idGuia !== null && !$this->guia->obtenerPorId($idGuia)) {
            Response::error('Guia asignado invalido', 422);
        }

        if ($personas < 1 || $personas > 15) {
            Response::error('Numero de personas debe estar entre 1 y 15', 422);
        }

        $this->reserva->actualizar($id, $data);

        Response::success(null, 'Reserva de tour actualizada correctamente');
    }

    public function destroy(int $id): void
    {
        $existe = $this->reserva->obtenerPorId($id);
        if (!$existe) {
            Response::error('No se encontro la reserva con ID ' . $id, 404);
        }

        $this->reserva->eliminar($id);

        Response::success(null, 'Reserva de tour cancelada exitosamente');
    }
}
