<?php

namespace App\Models;

use App\Core\Model;

/**
 * Gestiona el catálogo de impuestos (tipo_impuesto).
 */
class TipoImpuestoModel extends Model
{
    /**
     * Retorna todos los impuestos activos.
     */
    public function all(): array
    {
        $sql = 'SELECT id_tip, nom_tip, des_tip, est_registro
                FROM tipo_impuesto
                WHERE est_registro <> \'I\'
                ORDER BY nom_tip ASC';

        return $this->fetchAll($sql);
    }

    /**
     * Crea un nuevo tipo de impuesto.
     */
    public function create(string $nombre, ?string $descripcion): int
    {
        $sql = 'INSERT INTO tipo_impuesto (nom_tip, des_tip, est_registro)
                VALUES (?,?,\'A\')';

        return $this->insert($sql, 'ss', [$nombre, $descripcion]);
    }

    /**
     * Obtiene un impuesto por ID.
     */
    public function find(int $id): ?array
    {
        $sql = 'SELECT id_tip, nom_tip, des_tip, est_registro
                FROM tipo_impuesto
                WHERE id_tip = ?
                LIMIT 1';

        return $this->fetchOne($sql, 'i', [$id]);
    }
}

