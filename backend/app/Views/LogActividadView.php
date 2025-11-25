<?php

namespace App\Views;

/**
 * Serializa los registros de auditoría.
 */
class LogActividadView
{
    public static function collection(array $rows): array
    {
        return array_map([self::class, 'item'], $rows);
    }

    public static function item(array $row): array
    {
        return [
            'id_log' => (int) ($row['id_log'] ?? 0),
            'fecha' => $row['fec_log'] ?? '',
            'usuario' => $row['nom_usu'] ?? '',
            'modulo' => $row['modulo'] ?? '',
            'accion' => $row['accion'] ?? '',
            'detalle' => $row['detalle'] ?? '',
            'entidad_tipo' => $row['entidad_tipo'] ?? null,
            'entidad_id' => isset($row['entidad_id']) ? (int) $row['entidad_id'] : null,
            'metadata' => $row['metadata'] ?? null,
        ];
    }
}

