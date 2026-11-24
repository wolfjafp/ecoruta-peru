<?php

namespace App\Models;

use PDO;

/**
 * Modelo para la tabla `guias_arrieros`.
 * Personal acreditado por SERNANP para dirigir senderistas en zonas protegidas.
 */
class Guia
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function obtenerTodos(): array
    {
        $sql = "SELECT id_guia, nombres, apellidos, dni, telefono, correo, certificacion_sernanp, experiencia_anios, estado
                FROM guias_arrieros
                ORDER BY apellidos ASC, nombres ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $id): array|false
    {
        $sql = "SELECT id_guia, nombres, apellidos, dni, telefono, correo, certificacion_sernanp, experiencia_anios, estado
                FROM guias_arrieros
                WHERE id_guia = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }

    public function obtenerPorDni(string $dni): array|false
    {
        $sql = "SELECT id_guia FROM guias_arrieros WHERE dni = :dni";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':dni' => $dni]);
        return $stmt->fetch();
    }

    public function crear(array $data): int
    {
        $sql = "INSERT INTO guias_arrieros (
                    nombres, apellidos, dni, telefono, correo, 
                    certificacion_sernanp, experiencia_anios, estado
                ) VALUES (
                    :nombres, :apellidos, :dni, :telefono, :correo, 
                    :certificacion_sernanp, :experiencia_anios, :estado
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nombres'               => $data['nombres'],
            ':apellidos'             => $data['apellidos'],
            ':dni'                   => $data['dni'],
            ':telefono'              => $data['telefono'],
            ':correo'                => $data['correo'],
            ':certificacion_sernanp' => $data['certificacion_sernanp'],
            ':experiencia_anios'     => (int)($data['experiencia_anios'] ?? 0),
            ':estado'                => $data['estado'] ?? 'Disponible'
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function actualizar(int $id, array $data): bool
    {
        $sql = "UPDATE guias_arrieros
                SET nombres = :nombres,
                    apellidos = :apellidos,
                    dni = :dni,
                    telefono = :telefono,
                    correo = :correo,
                    certificacion_sernanp = :certificacion_sernanp,
                    experiencia_anios = :experiencia_anios,
                    estado = :estado
                WHERE id_guia = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombres'               => $data['nombres'],
            ':apellidos'             => $data['apellidos'],
            ':dni'                   => $data['dni'],
            ':telefono'              => $data['telefono'],
            ':correo'                => $data['correo'],
            ':certificacion_sernanp' => $data['certificacion_sernanp'],
            ':experiencia_anios'     => (int)($data['experiencia_anios'] ?? 0),
            ':estado'                => $data['estado'] ?? 'Disponible',
            ':id'                    => $id
        ]);
    }

    public function eliminar(int $id): bool
    {
        // En lugar de borrar fisicamente y romper contratos con reservas, lo pasamos a Inactivo
        $sql = "UPDATE guias_arrieros SET estado = 'Inactivo' WHERE id_guia = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
