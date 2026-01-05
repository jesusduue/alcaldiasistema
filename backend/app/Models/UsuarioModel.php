<?php

namespace App\Models;

use App\Core\Model;

/**
 * Administra los usuarios del sistema.
 */
class UsuarioModel extends Model
{
    public function list(bool $includeInactive = false): array
    {
        $sql = 'SELECT
                    u.id_usu,
                    u.nom_usu,
                    u.fky_rol,
                    u.est_registro,
                    r.nom_rol
                FROM usuario u
                INNER JOIN rol r ON r.id_rol = u.fky_rol';

        if (!$includeInactive) {
            $sql .= ' WHERE u.est_registro <> \'I\'';
        }

        return $this->fetchAll($sql);
    }

    public function find(int $idUsuario): ?array
    {
        $sql = 'SELECT
                    u.id_usu,
                    u.nom_usu,
                    u.fky_rol,
                    u.est_registro
                FROM usuario u
                WHERE u.id_usu = ?
                LIMIT 1';

        return $this->fetchOne($sql, 'i', [$idUsuario]);
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO usuario (
                    nom_usu,
                    cla_usu,
                    fky_rol,
                    est_registro
                ) VALUES (?,?,?,?)';

        return $this->insert($sql, 'ssis', [
            $data['nom_usu'],
            $this->hashPassword($data['cla_usu']),
            $data['fky_rol'],
            $data['est_registro'] ?? 'A',
        ]);
    }

    public function update(int $idUsuario, array $data): bool
    {
        $fields = [
            'nom_usu = ?',
            'fky_rol = ?',
            'est_registro = ?',
        ];
        $types = 'sis';
        $params = [
            $data['nom_usu'],
            $data['fky_rol'],
            $data['est_registro'] ?? 'A',
        ];

        if (!empty($data['cla_usu'])) {
            $fields[] = 'cla_usu = ?';
            $types .= 's';
            $params[] = $this->hashPassword($data['cla_usu']);
        }

        $types .= 'i';
        $params[] = $idUsuario;

        $sql = 'UPDATE usuario SET ' . implode(', ', $fields) . ' WHERE id_usu = ?';

        return $this->execute($sql, $types, $params);
    }

    public function findByNombre(string $nombre, ?int $ignoreId = null): ?array
    {
        $sql = 'SELECT id_usu
                FROM usuario
                WHERE nom_usu = ?';
        $types = 's';
        $params = [$nombre];

        if ($ignoreId) {
            $sql .= ' AND id_usu <> ?';
            $types .= 'i';
            $params[] = $ignoreId;
        }

        $sql .= ' LIMIT 1';

        return $this->fetchOne($sql, $types, $params);
    }

    public function findByNombreWithRol(string $nombre): ?array
    {
        $sql = 'SELECT
                    u.id_usu,
                    u.nom_usu,
                    u.cla_usu,
                    u.fky_rol,
                    u.est_registro,
                    r.nom_rol
                FROM usuario u
                INNER JOIN rol r ON r.id_rol = u.fky_rol
                WHERE u.nom_usu = ?
                LIMIT 1';

        return $this->fetchOne($sql, 's', [$nombre]);
    }

    public function updatePassword(int $idUsuario, string $plain): bool
    {
        $sql = 'UPDATE usuario SET cla_usu = ? WHERE id_usu = ?';

        return $this->execute($sql, 'si', [$this->hashPassword($plain), $idUsuario]);
    }

    private function hashPassword(string $plain): string
    {
        return password_hash($plain, PASSWORD_BCRYPT);
    }
}
