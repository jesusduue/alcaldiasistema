<?php

namespace App\Models;

use App\Core\Model;

/**
 * Gestiona la tabla factura_detalle.
 */
class FacturaDetalleModel extends Model
{
    /**
     * Inserta múltiples registros asociados a una factura.
     */
    public function insertItems(int $facturaId, array $items): void
    {
        if (empty($items)) {
            return;
        }

        $sql = 'INSERT INTO factura_detalle (fky_fac, fky_tip, monto_det, est_registro)
                VALUES (?,?,?,?)';

        foreach ($items as $item) {
            if (!isset($item['fky_tip'], $item['monto'])) {
                continue;
            }

            $this->insert($sql, 'iids', [
                $facturaId,
                (int) $item['fky_tip'],
                (float) $item['monto'],
                $item['estado'] ?? 'A',
            ]);
        }
    }

    /**
     * Marca todos los detalles de una factura con un estado específico.
     */
    public function markByFactura(int $facturaId, string $estado): bool
    {
        $sql = 'UPDATE factura_detalle
                SET est_registro = ?
                WHERE fky_fac = ?';

        return $this->execute($sql, 'si', [$estado, $facturaId]);
    }

    /**
     * Marca un registro individual.
     */
    public function markById(int $detalleId, string $estado): bool
    {
        $sql = 'UPDATE factura_detalle
                SET est_registro = ?
                WHERE id_fde = ?';

        return $this->execute($sql, 'si', [$estado, $detalleId]);
    }

    public function deleteByFactura(int $facturaId): bool
    {
        return $this->markByFactura($facturaId, 'I');
    }

    public function deleteById(int $detalleId): bool
    {
        return $this->markById($detalleId, 'I');
    }

    /**
     * Lista los detalles de una factura.
     */
    public function listByFactura(int $facturaId): array
    {
        $sql = 'SELECT
                    fd.id_fde,
                    fd.fky_fac,
                    fd.fky_tip,
                    fd.monto_det,
                    fd.est_registro,
                    ti.nom_tip AS nombre_impuesto
                FROM factura_detalle fd
                INNER JOIN tipo_impuesto ti ON ti.id_tip = fd.fky_tip
                WHERE fd.fky_fac = ?
                  AND fd.est_registro = \'A\'
                ORDER BY fd.id_fde ASC';

        return $this->fetchAll($sql, 'i', [$facturaId]);
    }

    /**
     * Suma el monto por impuesto en una fecha específica.
     */
    public function listByFecha(string $fecha): array
    {
        $sql = 'SELECT
                    ti.nom_tip AS nombre_impuesto,
                    SUM(fd.monto_det) AS total_monto
                FROM factura_detalle fd
                INNER JOIN factura f ON f.id_fac = fd.fky_fac
                INNER JOIN tipo_impuesto ti ON ti.id_tip = fd.fky_tip
                WHERE DATE(f.fec_fac) = ?
                  AND fd.est_registro = \'A\'
                  AND f.est_registro = \'A\'
                  AND f.est_pago <> \'N\'
                GROUP BY ti.nom_tip
                ORDER BY ti.nom_tip ASC';

        return $this->fetchAll($sql, 's', [$fecha]);
    }
}

