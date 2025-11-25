<?php

namespace App\Views;

/**
 * Transformaciones de datos de contribuyentes expuestas al frontend.
 */
class ContribuyenteView
{
    /**
     * Formatea una colección de filas SQL.
     */
    public static function collection(array $rows): array
    {
        return array_map([self::class, 'item'], $rows);
    }

    /**
     * Formatea una fila individual.
     */
    public static function item(array $row): array
    {
        return [
            'id_contribuyente' => isset($row['id_con']) ? (int) $row['id_con'] : (int) ($row['id_contribuyente'] ?? 0),
            'cedula_rif' => $row['rif_con'] ?? $row['cedula_rif'] ?? '',
            'razon_social' => $row['nom_con'] ?? $row['razon_social'] ?? '',
            'telefono' => $row['tel_con'] ?? $row['telefono'] ?? '',
            'email' => $row['ema_con'] ?? $row['email'] ?? '',
            'direccion' => $row['dir_con'] ?? $row['direccion'] ?? '',
            'estado_cont' => strtoupper($row['est_registro'] ?? $row['estado_cont'] ?? 'A'),
            'usuario_registro' => isset($row['fky_usu_registro']) ? (int) $row['fky_usu_registro'] : (int) ($row['usuario_registro'] ?? 0),
        ];
    }
}

