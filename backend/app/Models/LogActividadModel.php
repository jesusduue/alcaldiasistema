<?php

namespace App\Models;

use App\Core\Model;

/**
 * Persistencia para la tabla de auditoría log_actividad.
 */
class LogActividadModel extends Model
{
    public function record(array $data): int
    {
        $sql = 'INSERT INTO log_actividad (
                    fky_usu,
                    nom_usu,
                    modulo,
                    accion,
                    detalle,
                    entidad_tipo,
                    entidad_id,
                    metadata,
                    ip,
                    user_agent,
                    est_log
                ) VALUES (?,?,?,?,?,?,?,?,?,?,\'A\')';

        return $this->insert($sql, 'isssssisss', [
            $data['fky_usu'],
            $data['nom_usu'],
            $data['modulo'],
            $data['accion'],
            $data['detalle'],
            $data['entidad_tipo'],
            $data['entidad_id'],
            $data['metadata'],
            $data['ip'],
            $data['user_agent'],
        ]);
    }

    public function recent(int $limit = 50): array
    {
        $sql = 'SELECT
                    id_log,
                    fec_log,
                    nom_usu,
                    modulo,
                    accion,
                    detalle,
                    entidad_tipo,
                    entidad_id,
                    metadata
                FROM log_actividad
                WHERE est_log = \'A\'
                ORDER BY fec_log DESC
                LIMIT ?';

        return $this->fetchAll($sql, 'i', [$limit]);
    }
}

