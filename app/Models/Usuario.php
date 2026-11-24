<?php

namespace App\Models;

use PDO;

/**
 * Modelo para la tabla `usuarios_senderistas`.
 * Clientes senderistas registrados en la plataforma web.
 */
class Usuario
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function obtenerTodos(): array
    {
        $sql = "SELECT id_usuario, nombres, apellidos, correo, telefono, tipo_documento, numero_documento, condicion_fisica, fecha_registro
                FROM usuarios_senderistas
                ORDER BY apellidos ASC, nombres ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $id): array|false
    {
        $sql = "SELECT id_usuario, nombres, apellidos, correo, telefono, tipo_documento, numero_documento, condicion_fisica, fecha_registro
                FROM usuarios_senderistas
                WHERE id_usuario = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }

    public function obtenerPorCorreo(string $correo): array|false
    {
        $sql = "SELECT id_usuario FROM usuarios_senderistas WHERE LOWER(correo) = LOWER(:correo)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':correo' => $correo]);
        return $stmt->fetch();
    }

    public function obtenerPorDocumento(string $documento): array|false
    {
        $sql = "SELECT id_usuario FROM usuarios_senderistas WHERE numero_documento = :documento";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':documento' => $documento]);
        return $stmt->fetch();
    }

    public function crear(array $data): int
    {
        $sql = "INSERT INTO usuarios_senderistas (
                    nombres, apellidos, correo, telefono, 
                    tipo_documento, numero_documento, condicion_fisica
                ) VALUES (
                    :nombres, :apellidos, :correo, :telefono, 
                    :tipo_documento, :numero_documento, :condicion_fisica
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nombres'          => $data['nombres'],
            ':apellidos'        => $data['apellidos'],
            ':correo'           => $data['correo'],
            ':telefono'         => $data['telefono'] ?? null,
            ':tipo_documento'   => $data['tipo_documento'] ?? 'DNI',
            ':numero_documento' => $data['numero_documento'],
            ':condicion_fisica' => $data['condicion_fisica'] ?? 'Principiante'
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function actualizar(int $id, array $data): bool
    {
        $sql = "UPDATE usuarios_senderistas
                SET nombres = :nombres,
                    apellidos = :apellidos,
                    correo = :correo,
                    telefono = :telefono,
                    tipo_documento = :tipo_documento,
                    numero_documento = :numero_documento,
                    condicion_fisica = :condicion_fisica
                WHERE id_usuario = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombres'          => $data['nombres'],
            ':apellidos'        => $data['apellidos'],
            ':correo'           => $data['correo'],
            ':telefono'         => $data['telefono'] ?? null,
            ':tipo_documento'   => $data['tipo_documento'] ?? 'DNI',
            ':numero_documento' => $data['numero_documento'],
            ':condicion_fisica' => $data['condicion_fisica'] ?? 'Principiante',
            ':id'               => $id
        ]);
    }

    public function eliminar(int $id): bool
    {
        $sql = "DELETE FROM usuarios_senderistas WHERE id_usuario = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
