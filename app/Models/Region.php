<?php

namespace App\Models;

use PDO;

/**
 * Modelo para la tabla `regiones`.
 * Representa las zonas geograficas de Peru (Costa, Sierra, Selva).
 */
class Region
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function obtenerTodos(): array
    {
        $sql = "SELECT id_region, nombre_region, clima_predominante, altitud_promedio_msnm, descripcion, estado
                FROM regiones
                ORDER BY id_region ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $id): array|false
    {
        $sql = "SELECT id_region, nombre_region, clima_predominante, altitud_promedio_msnm, descripcion, estado
                FROM regiones
                WHERE id_region = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }

    public function obtenerPorNombre(string $nombre): array|false
    {
        $sql = "SELECT id_region FROM regiones WHERE LOWER(nombre_region) = LOWER(:nombre)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':nombre' => $nombre]);
        return $stmt->fetch();
    }

    public function crear(array $data): int
    {
        $sql = "INSERT INTO regiones (nombre_region, clima_predominante, altitud_promedio_msnm, descripcion, estado)
                VALUES (:nombre_region, :clima_predominante, :altitud_promedio_msnm, :descripcion, 1)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nombre_region'          => $data['nombre_region'],
            ':clima_predominante'     => $data['clima_predominante'],
            ':altitud_promedio_msnm'  => (int)$data['altitud_promedio_msnm'],
            ':descripcion'            => $data['descripcion'] ?? null
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function actualizar(int $id, array $data): bool
    {
        $sql = "UPDATE regiones
                SET nombre_region = :nombre_region,
                    clima_predominante = :clima_predominante,
                    altitud_promedio_msnm = :altitud_promedio_msnm,
                    descripcion = :descripcion
                WHERE id_region = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre_region'          => $data['nombre_region'],
            ':clima_predominante'     => $data['clima_predominante'],
            ':altitud_promedio_msnm'  => (int)$data['altitud_promedio_msnm'],
            ':descripcion'            => $data['descripcion'] ?? null,
            ':id'                     => $id
        ]);
    }

    public function eliminar(int $id): bool
    {
        // Borrado logico para no romper la integridad con rutas existentes
        $sql = "UPDATE regiones SET estado = 0 WHERE id_region = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
