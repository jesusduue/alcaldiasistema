<?php

namespace App\Repositories;

use App\Config\Database;
use PDO;

class ContribuyenteRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO contribuyente (cedula_rif, razon_social, estado_cont)
             VALUES (:cedula_rif, :razon_social, :estado_cont)'
        );

        $stmt->execute([
            ':cedula_rif' => $data['cedula_rif'],
            ':razon_social' => $data['razon_social'],
            ':estado_cont' => $data['estado_cont'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE contribuyente
             SET cedula_rif = :cedula_rif,
                 razon_social = :razon_social,
                 estado_cont = :estado_cont
             WHERE id_contribuyente = :id'
        );

        return $stmt->execute([
            ':cedula_rif' => $data['cedula_rif'],
            ':razon_social' => $data['razon_social'],
            ':estado_cont' => $data['estado_cont'],
            ':id' => $id,
        ]);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id_contribuyente, cedula_rif, razon_social, estado_cont
             FROM contribuyente
             WHERE id_contribuyente = :id'
        );
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function all(): array
    {
        $stmt = $this->db->query(
            'SELECT id_contribuyente, cedula_rif, razon_social, estado_cont
             FROM contribuyente
             ORDER BY id_contribuyente ASC'
        );

        return $stmt->fetchAll();
    }

    public function search(?string $term = null): array
    {
        if ($term === null || $term === '') {
            return $this->all();
        }

        $likeTerm = '%' . $term . '%';
        $stmt = $this->db->prepare(
            'SELECT id_contribuyente, cedula_rif, razon_social, estado_cont
             FROM contribuyente
             WHERE cedula_rif LIKE :term
                OR razon_social LIKE :term
                OR estado_cont LIKE :term
             ORDER BY id_contribuyente ASC'
        );
        $stmt->execute([':term' => $likeTerm]);

        return $stmt->fetchAll();
    }
}

