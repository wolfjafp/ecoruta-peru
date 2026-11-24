<?php

namespace Config;

use PDO;
use PDOException;

/**
 * Gestion de la conexion a la base de datos con PDO.
 * Las credenciales se leen desde las variables de entorno para cumplir
 * con la seguridad solicitada en la rubrica de la evaluacion.
 */
class Database
{
    private string $host;
    private string $database;
    private string $username;
    private string $password;
    private int $port;

    public function __construct()
    {
        // Leemos las variables cargadas desde el archivo .env
        $this->host = (string) Env::get('DB_HOST', 'localhost');
        $this->database = (string) Env::get('DB_NAME', 'ecoruta_peru_db');
        $this->username = (string) Env::get('DB_USER', 'root');
        $this->password = (string) Env::get('DB_PASS', '');
        $this->port = (int) Env::get('DB_PORT', 3306);
    }

    public function connect(): PDO
    {
        try {
            // Cadena de conexion (DSN) para MySQL en XAMPP
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->database};charset=utf8mb4";

            $connection = new PDO(
                $dsn,
                $this->username,
                $this->password
            );

            // Configuramos atributos recomendados para PDO
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            return $connection;

        } catch (PDOException $e) {
            // No mostramos contraseñas ni stacktraces sensibles en el error
            throw new PDOException('Error al conectar con la base de datos de EcoRuta Peru: ' . $e->getMessage());
        }
    }
}
