<?php

namespace App\Views;

/**
 * Proyección estándar de usuarios.
 */
class UsuarioView
{
    public static function collection(array $rows): array
    {
        return array_map([self::class, 'item'], $rows);
    }

    public static function item(array $row): array
    {
        return [
            'id_usuario' => isset($row['id_usu']) ? (int) $row['id_usu'] : (int) ($row['id_usuario'] ?? 0),
            'nombre' => $row['nom_usu'] ?? $row['nombre'] ?? '',
            'rol' => isset($row['fky_rol']) ? (int) $row['fky_rol'] : (int) ($row['rol'] ?? 0),
            'estado' => strtoupper($row['est_registro'] ?? $row['estado'] ?? 'A'),
        ];
    }
}

