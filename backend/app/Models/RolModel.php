<?php

namespace App\Models;

use App\Core\Model;

/**
 * Acceso CRUD a la tabla de roles.
 */
class RolModel extends Model
{
    public function all(): array
    {
        $sql = 'SELECT id_rol, nom_rol, est_registro
                FROM rol
                WHERE est_registro <> \'I\'
                ORDER BY nom_rol ASC';

        return $this->fetchAll($sql);
    }

    public function create(string $nombre): int
    {
        $sql = 'INSERT INTO rol (nom_rol, est_registro) VALUES (?, \'A\')';

        return $this->insert($sql, 's', [$nombre]);
    }

    public function update(int $idRol, string $nombre, string $estado): bool
    {
        $sql = 'UPDATE rol
                SET nom_rol = ?,
                    est_registro = ?
                WHERE id_rol = ?';

        return $this->execute($sql, 'ssi', [$nombre, $estado, $idRol]);
    }
}

