<?php

namespace App\Views;

/**
 * Adapta los datos de tipo_impuesto al contrato del frontend.
 */
class TipoImpuestoView
{
    /**
     * Formatea una colección completa.
     */
    public static function collection(array $rows): array
    {
        return array_map([self::class, 'item'], $rows);
    }

    /**
     * Formatea un registro.
     */
    public static function item(array $row): array
    {
        return [
            'id_clasificador' => isset($row['id_tip']) ? (int) $row['id_tip'] : (int) ($row['id_clasificador'] ?? 0),
            'nombre' => $row['nom_tip'] ?? $row['nombre'] ?? '',
            'descripcion' => $row['des_tip'] ?? $row['descripcion'] ?? null,
            'estado' => strtoupper($row['est_registro'] ?? $row['estado'] ?? 'A'),
        ];
    }
}

