<?php

namespace App\Views;

/**
 * Representa un rol del sistema.
 */
class RolView
{
    public static function collection(array $rows): array
    {
        return array_map([self::class, 'item'], $rows);
    }

    public static function item(array $row): array
    {
        return [
            'id_rol' => isset($row['id_rol']) ? (int) $row['id_rol'] : (int) ($row['id'] ?? 0),
            'nombre' => $row['nom_rol'] ?? $row['nombre'] ?? '',
            'estado' => strtoupper($row['est_registro'] ?? $row['estado'] ?? 'A'),
        ];
    }
}

