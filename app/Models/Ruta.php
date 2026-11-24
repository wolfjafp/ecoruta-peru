<?php

namespace App\Models;

use PDO;

/**
 * Modelo para la tabla `rutas`.
 * Catalogo principal de destinos y senderos ecoturisticos de EcoRuta Peru.
 * Se relaciona con las tablas `regiones` y `categorias` mediante Foreign Keys.
 */
class Ruta
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function obtenerTodos(): array
    {
        // Tal como explico el docente, usamos INNER JOIN y alias (r, reg, c)
        // para enriquecer la informacion que viaja en el JSON hacia el cliente.
        $sql = "SELECT 
                    r.id_ruta,
                    r.id_region,
                    r.id_categoria,
                    r.nombre_ruta,
                    r.altitud_maxima_msnm,
                    r.distancia_km,
                    r.duracion_dias,
                    r.dificultad,
                    r.precio_referencial,
                    r.requiere_guia_obligatorio,
                    r.estado,
                    r.fecha_registro,
                    reg.nombre_region,
                    reg.clima_predominante,
                    c.nombre_categoria,
                    c.nivel_exigencia
                FROM rutas r
                INNER JOIN regiones reg ON r.id_region = reg.id_region
                INNER JOIN categorias c ON r.id_categoria = c.id_categoria
                ORDER BY r.id_ruta ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $id): array|false
    {
        $sql = "SELECT 
                    r.id_ruta,
                    r.id_region,
                    r.id_categoria,
                    r.nombre_ruta,
                    r.altitud_maxima_msnm,
                    r.distancia_km,
                    r.duracion_dias,
                    r.dificultad,
                    r.precio_referencial,
                    r.requiere_guia_obligatorio,
                    r.estado,
                    r.fecha_registro,
                    reg.nombre_region,
                    reg.clima_predominante,
                    c.nombre_categoria,
                    c.nivel_exigencia
                FROM rutas r
                INNER JOIN regiones reg ON r.id_region = reg.id_region
                INNER JOIN categorias c ON r.id_categoria = c.id_categoria
                WHERE r.id_ruta = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }

    public function obtenerPorNombre(string $nombre): array|false
    {
        $sql = "SELECT id_ruta, nombre_ruta FROM rutas WHERE nombre_ruta = :nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':nombre' => $nombre]);
        return $stmt->fetch();
    }

    public function buscar(string $termino): array
    {
        // Buscador dinamico con LIKE y comodin % tal como mostro el profesor en clase
        $sql = "SELECT 
                    r.id_ruta,
                    r.nombre_ruta,
                    r.altitud_maxima_msnm,
                    r.distancia_km,
                    r.duracion_dias,
                    r.dificultad,
                    r.precio_referencial,
                    r.estado,
                    reg.nombre_region,
                    c.nombre_categoria
                FROM rutas r
                INNER JOIN regiones reg ON r.id_region = reg.id_region
                INNER JOIN categorias c ON r.id_categoria = c.id_categoria
                WHERE r.nombre_ruta LIKE :termino1 
                   OR reg.nombre_region LIKE :termino2 
                   OR c.nombre_categoria LIKE :termino3
                ORDER BY r.nombre_ruta ASC";

        $param = '%' . $termino . '%';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':termino1' => $param,
            ':termino2' => $param,
            ':termino3' => $param
        ]);

        return $stmt->fetchAll();
    }

    public function crear(array $data): int
    {
        $sql = "INSERT INTO rutas (
                    id_region, id_categoria, nombre_ruta, altitud_maxima_msnm, 
                    distancia_km, duracion_dias, dificultad, precio_referencial, 
                    requiere_guia_obligatorio, estado
                ) VALUES (
                    :id_region, :id_categoria, :nombre_ruta, :altitud_maxima_msnm, 
                    :distancia_km, :duracion_dias, :dificultad, :precio_referencial, 
                    :requiere_guia_obligatorio, :estado
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_region'                  => (int)$data['id_region'],
            ':id_categoria'               => (int)$data['id_categoria'],
            ':nombre_ruta'                => $data['nombre_ruta'],
            ':altitud_maxima_msnm'        => (int)$data['altitud_maxima_msnm'],
            ':distancia_km'               => (float)$data['distancia_km'],
            ':duracion_dias'              => (int)($data['duracion_dias'] ?? 1),
            ':dificultad'                 => $data['dificultad'] ?? 'Moderado',
            ':precio_referencial'         => (float)$data['precio_referencial'],
            ':requiere_guia_obligatorio'  => (int)($data['requiere_guia_obligatorio'] ?? 0),
            ':estado'                     => $data['estado'] ?? 'Activo'
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function actualizar(int $id, array $data): bool
    {
        $sql = "UPDATE rutas
                SET id_region = :id_region,
                    id_categoria = :id_categoria,
                    nombre_ruta = :nombre_ruta,
                    altitud_maxima_msnm = :altitud_maxima_msnm,
                    distancia_km = :distancia_km,
                    duracion_dias = :duracion_dias,
                    dificultad = :dificultad,
                    precio_referencial = :precio_referencial,
                    requiere_guia_obligatorio = :requiere_guia_obligatorio,
                    estado = :estado
                WHERE id_ruta = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_region'                  => (int)$data['id_region'],
            ':id_categoria'               => (int)$data['id_categoria'],
            ':nombre_ruta'                => $data['nombre_ruta'],
            ':altitud_maxima_msnm'        => (int)$data['altitud_maxima_msnm'],
            ':distancia_km'               => (float)$data['distancia_km'],
            ':duracion_dias'              => (int)($data['duracion_dias'] ?? 1),
            ':dificultad'                 => $data['dificultad'] ?? 'Moderado',
            ':precio_referencial'         => (float)$data['precio_referencial'],
            ':requiere_guia_obligatorio'  => (int)($data['requiere_guia_obligatorio'] ?? 0),
            ':estado'                     => $data['estado'] ?? 'Activo',
            ':id'                         => $id
        ]);
    }

    public function eliminar(int $id): bool
    {
        // Cambiamos el estado a Cerrado para mantener el historial de reservas
        $sql = "UPDATE rutas SET estado = 'Cerrado' WHERE id_ruta = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
