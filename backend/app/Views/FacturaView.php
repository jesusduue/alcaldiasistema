<?php

namespace App\Views;

/**
 * Normaliza la respuesta JSON de las facturas y sus detalles.
 */
class FacturaView
{
    private const SLOTS = ['A', 'B', 'C', 'D', 'E', 'F'];

    /**
     * Formatea una colección de facturas.
     */
    public static function collection(array $rows): array
    {
        return array_map([self::class, 'item'], $rows);
    }

    /**
     * Formatea una fila de factura sin detalle.
     */
    public static function item(array $row): array
    {
        $estado = strtoupper($row['est_pago'] ?? $row['estado_pago'] ?? 'A');
        $estadoDescripcion = self::describeEstado($estado);

        return [
            'id_factura' => isset($row['id_fac']) ? (int) $row['id_fac'] : (int) ($row['id_factura'] ?? 0),
            'num_factura' => $row['num_fac'] ?? $row['num_factura'] ?? '',
            'numero_control' => $row['num_fac'] ?? $row['numero_control'] ?? '',
            'fecha' => $row['fec_fac'] ?? $row['fecha'] ?? '',
            'cod_contribuyente' => isset($row['fky_con']) ? (int) $row['fky_con'] : (int) ($row['cod_contribuyente'] ?? 0),
            'cedula_rif' => $row['cedula_rif'] ?? $row['rif_con'] ?? '',
            'razon_social' => $row['razon_social'] ?? $row['nom_con'] ?? '',
            'concepto' => $row['des_fac'] ?? $row['concepto'] ?? '',
            'total_factura' => isset($row['tot_fac']) ? (float) $row['tot_fac'] : (float) ($row['total_factura'] ?? 0),
            'estado_pago' => $estado,
            'ESTADO_FACT' => $estadoDescripcion,
            'estado_descripcion' => $estadoDescripcion,
        ];
    }

    /**
     * Formatea una factura con listado de detalles.
     */
    public static function detail(array $factura, array $detalles): array
    {
        $base = self::item($factura);
        $base['est_registro'] = strtoupper($factura['est_registro'] ?? 'A');

        foreach (self::SLOTS as $slot) {
            $base['impuesto_' . $slot] = null;
            $base['monto_impuesto_' . $slot] = null;
            $base['nombre_impuesto_' . $slot] = null;
        }

        foreach ($detalles as $index => $detalle) {
            if ($index >= count(self::SLOTS)) {
                break;
            }

            $slot = self::SLOTS[$index];
            $base['impuesto_' . $slot] = (int) ($detalle['fky_tip'] ?? 0);
            $base['monto_impuesto_' . $slot] = (float) ($detalle['monto_det'] ?? 0);
            $base['nombre_impuesto_' . $slot] = $detalle['nombre_impuesto'] ?? null;
        }

        return $base;
    }

    private static function describeEstado(string $estado): string
    {
        return $estado === 'N' ? 'nulo' : 'activo';
    }
}

