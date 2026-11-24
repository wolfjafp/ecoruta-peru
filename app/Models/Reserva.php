<?php

namespace App\Models;

use PDO;

/**
 * Modelo para la tabla `reservas_tours`.
 * Representa el modulo transaccional central del sistema EcoRuta Peru.
 * Asocia una ruta ecoturistica con un usuario senderista y un guia oficial acreditado.
 */
class Reserva
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function obtenerTodos(): array
    {
        $sql = "SELECT 
                    res.id_reserva,
                    res.codigo_reserva,
                    res.id_ruta,
                    res.id_usuario,
                    res.id_guia,
                    res.fecha_salida,
                    res.numero_personas,
                    res.total_pago,
                    res.estado_reserva,
                    res.observaciones_medicas,
                    res.fecha_creacion,
                    r.nombre_ruta,
                    r.dificultad AS ruta_dificultad,
                    r.precio_referencial AS ruta_precio,
                    u.nombres AS usuario_nombres,
                    u.apellidos AS usuario_apellidos,
                    u.correo AS usuario_correo,
                    u.numero_documento AS usuario_documento,
                    g.nombres AS guia_nombres,
                    g.apellidos AS guia_apellidos,
                    g.certificacion_sernanp
                FROM reservas_tours res
                INNER JOIN rutas r ON res.id_ruta = r.id_ruta
                INNER JOIN usuarios_senderistas u ON res.id_usuario = u.id_usuario
                LEFT JOIN guias_arrieros g ON res.id_guia = g.id_guia
                ORDER BY res.id_reserva DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $id): array|false
    {
        $sql = "SELECT 
                    res.id_reserva,
                    res.codigo_reserva,
                    res.id_ruta,
                    res.id_usuario,
                    res.id_guia,
                    res.fecha_salida,
                    res.numero_personas,
                    res.total_pago,
                    res.estado_reserva,
                    res.observaciones_medicas,
                    res.fecha_creacion,
                    r.nombre_ruta,
                    r.dificultad AS ruta_dificultad,
                    r.precio_referencial AS ruta_precio,
                    u.nombres AS usuario_nombres,
                    u.apellidos AS usuario_apellidos,
                    u.correo AS usuario_correo,
                    u.numero_documento AS usuario_documento,
                    g.nombres AS guia_nombres,
                    g.apellidos AS guia_apellidos,
                    g.certificacion_sernanp
                FROM reservas_tours res
                INNER JOIN rutas r ON res.id_ruta = r.id_ruta
                INNER JOIN usuarios_senderistas u ON res.id_usuario = u.id_usuario
                LEFT JOIN guias_arrieros g ON res.id_guia = g.id_guia
                WHERE res.id_reserva = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }

    public function obtenerPorCodigo(string $codigo): array|false
    {
        $sql = "SELECT id_reserva FROM reservas_tours WHERE codigo_reserva = :codigo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':codigo' => $codigo]);
        return $stmt->fetch();
    }

    public function crear(array $data): int
    {
        $sql = "INSERT INTO reservas_tours (
                    codigo_reserva, id_ruta, id_usuario, id_guia, 
                    fecha_salida, numero_personas, total_pago, 
                    estado_reserva, observaciones_medicas
                ) VALUES (
                    :codigo_reserva, :id_ruta, :id_usuario, :id_guia, 
                    :fecha_salida, :numero_personas, :total_pago, 
                    :estado_reserva, :observaciones_medicas
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':codigo_reserva'       => $data['codigo_reserva'],
            ':id_ruta'              => (int)$data['id_ruta'],
            ':id_usuario'           => (int)$data['id_usuario'],
            ':id_guia'              => !empty($data['id_guia']) ? (int)$data['id_guia'] : null,
            ':fecha_salida'         => $data['fecha_salida'],
            ':numero_personas'      => (int)$data['numero_personas'],
            ':total_pago'           => (float)$data['total_pago'],
            ':estado_reserva'       => $data['estado_reserva'] ?? 'Pendiente',
            ':observaciones_medicas'=> $data['observaciones_medicas'] ?? null
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function actualizar(int $id, array $data): bool
    {
        $sql = "UPDATE reservas_tours
                SET id_ruta = :id_ruta,
                    id_usuario = :id_usuario,
                    id_guia = :id_guia,
                    fecha_salida = :fecha_salida,
                    numero_personas = :numero_personas,
                    total_pago = :total_pago,
                    estado_reserva = :estado_reserva,
                    observaciones_medicas = :observaciones_medicas
                WHERE id_reserva = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_ruta'              => (int)$data['id_ruta'],
            ':id_usuario'           => (int)$data['id_usuario'],
            ':id_guia'              => !empty($data['id_guia']) ? (int)$data['id_guia'] : null,
            ':fecha_salida'         => $data['fecha_salida'],
            ':numero_personas'      => (int)$data['numero_personas'],
            ':total_pago'           => (float)$data['total_pago'],
            ':estado_reserva'       => $data['estado_reserva'] ?? 'Pendiente',
            ':observaciones_medicas'=> $data['observaciones_medicas'] ?? null,
            ':id'                   => $id
        ]);
    }

    public function eliminar(int $id): bool
    {
        // Cancelamos la reserva de forma logica
        $sql = "UPDATE reservas_tours SET estado_reserva = 'Cancelada' WHERE id_reserva = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
