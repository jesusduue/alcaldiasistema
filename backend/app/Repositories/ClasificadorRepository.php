<?php

namespace App\Repositories;

use App\Config\Database;
use PDO;

class ClasificadorRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(string $nombre): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO clasificador (nombre) VALUES (:nombre)'
        );
        $stmt->execute([':nombre' => $nombre]);

        return (int) $this->db->lastInsertId();
    }

    public function all(): array
    {
        $stmt = $this->db->query(
            'SELECT id_clasificador, nombre
             FROM clasificador
             ORDER BY nombre ASC'
        );

        return $stmt->fetchAll();
    }
}

