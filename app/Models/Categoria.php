<?php

namespace App\Models;

use PDO;

/**
 * Modelo para la tabla `categorias`.
 * Clasifica las rutas ecoturisticas (Trekking de Altura, Senderismo Ligero, etc.).
 */
class Categoria
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function obtenerTodos(): array
    {
        $sql = "SELECT id_categoria, nombre_categoria, nivel_exigencia, descripcion, estado
                FROM categorias
                ORDER BY id_categoria ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $id): array|false
    {
        $sql = "SELECT id_categoria, nombre_categoria, nivel_exigencia, descripcion, estado
                FROM categorias
                WHERE id_categoria = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }

    public function obtenerPorNombre(string $nombre): array|false
    {
        $sql = "SELECT id_categoria FROM categorias WHERE LOWER(nombre_categoria) = LOWER(:nombre)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':nombre' => $nombre]);
        return $stmt->fetch();
    }

    public function crear(array $data): int
    {
        $sql = "INSERT INTO categorias (nombre_categoria, nivel_exigencia, descripcion, estado)
                VALUES (:nombre_categoria, :nivel_exigencia, :descripcion, 1)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nombre_categoria' => $data['nombre_categoria'],
            ':nivel_exigencia'  => $data['nivel_exigencia'],
            ':descripcion'      => $data['descripcion'] ?? null
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function actualizar(int $id, array $data): bool
    {
        $sql = "UPDATE categorias
                SET nombre_categoria = :nombre_categoria,
                    nivel_exigencia = :nivel_exigencia,
                    descripcion = :descripcion
                WHERE id_categoria = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre_categoria' => $data['nombre_categoria'],
            ':nivel_exigencia'  => $data['nivel_exigencia'],
            ':descripcion'      => $data['descripcion'] ?? null,
            ':id'               => $id
        ]);
    }

    public function eliminar(int $id): bool
    {
        // Borrado logico para preservar la integridad referencial con las rutas
        $sql = "UPDATE categorias SET estado = 0 WHERE id_categoria = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
