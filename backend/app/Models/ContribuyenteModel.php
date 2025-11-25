<?php

namespace App\Models;

use App\Core\Model;

/**
 * Acceso a la entidad contribuyente.
 */
class ContribuyenteModel extends Model
{
    /**
     * Busca contribuyentes por término opcional.
     */
    public function search(?string $term = null): array
    {
        $sql = 'SELECT
                    id_con,
                    nom_con,
                    rif_con,
                    tel_con,
                    ema_con,
                    dir_con,
                    fky_usu_registro,
                    est_registro
                FROM contribuyente
                WHERE est_registro <> \'I\'';

        $types = '';
        $params = [];

        if ($term) {
            $sql .= ' AND (LOWER(nom_con) LIKE ? OR LOWER(rif_con) LIKE ?)';
            $types .= 'ss';
            $like = '%' . strtolower($term) . '%';
            $params[] = $like;
            $params[] = $like;
        }

        $sql .= ' ORDER BY id_con ASC';

        return $this->fetchAll($sql, $types, $params);
    }

    /**
     * Registra un nuevo contribuyente y retorna su ID.
     */
    public function create(array $data): int
    {
        $sql = 'INSERT INTO contribuyente (
                    nom_con,
                    rif_con,
                    tel_con,
                    ema_con,
                    dir_con,
                    fky_usu_registro,
                    est_registro
                ) VALUES (?,?,?,?,?,?,?)';

        return $this->insert($sql, 'sssssis', [
            $data['nom_con'],
            $data['rif_con'],
            $data['tel_con'],
            $data['ema_con'],
            $data['dir_con'],
            $data['fky_usu_registro'],
            $data['est_registro'] ?? 'A',
        ]);
    }

    /**
     * Actualiza un contribuyente existente.
     */
    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE contribuyente
                SET nom_con = ?,
                    rif_con = ?,
                    tel_con = ?,
                    ema_con = ?,
                    dir_con = ?,
                    est_registro = ?
                WHERE id_con = ?';

        return $this->execute($sql, 'ssssssi', [
            $data['nom_con'],
            $data['rif_con'],
            $data['tel_con'],
            $data['ema_con'],
            $data['dir_con'],
            $data['est_registro'] ?? 'A',
            $id,
        ]);
    }

    /**
     * Obtiene un contribuyente por ID.
     */
    public function find(int $id): ?array
    {
        $sql = 'SELECT
                    id_con,
                    nom_con,
                    rif_con,
                    tel_con,
                    ema_con,
                    dir_con,
                    fky_usu_registro,
                    est_registro
                FROM contribuyente
                WHERE id_con = ?
                LIMIT 1';

        return $this->fetchOne($sql, 'i', [$id]);
    }

    /**
     * Busca por RIF para evitar duplicados.
     */
    public function findByRif(string $rif, ?int $ignoreId = null): ?array
    {
        $sql = 'SELECT id_con, rif_con
                FROM contribuyente
                WHERE rif_con = ?';
        $types = 's';
        $params = [$rif];

        if ($ignoreId) {
            $sql .= ' AND id_con <> ?';
            $types .= 'i';
            $params[] = $ignoreId;
        }

        $sql .= ' LIMIT 1';

        return $this->fetchOne($sql, $types, $params);
    }
}

