<?php

namespace App\Models;

use App\Core\Model;
use DateTimeImmutable;

/**
 * Maneja la lógica de persistencia de facturas.
 */
class FacturaModel extends Model
{
    private FacturaDetalleModel $detalles;

    public function __construct()
    {
        parent::__construct();
        $this->detalles = new FacturaDetalleModel($this->db);
    }

    /**
     * Lista todas las facturas opcionalmente filtradas por término.
     */
    public function listAll(?string $term = null): array
    {
        $baseSql = 'SELECT
                f.id_fac AS id_factura,
                f.num_fac AS num_factura,
                DATE(f.fec_fac) AS fecha,
                c.rif_con AS cedula_rif,
                c.nom_con AS razon_social,
                f.des_fac AS concepto,
                f.tot_fac AS total_factura,
                f.est_pago AS estado_pago,
                f.fky_con AS cod_contribuyente
            FROM factura f
            INNER JOIN contribuyente c ON f.fky_con = c.id_con
            WHERE f.est_registro <> \'I\'';

        $params = [];
        $types = '';

        if ($term) {
            $baseSql .= ' AND (
                    f.num_fac LIKE ?
                    OR DATE(f.fec_fac) LIKE ?
                    OR c.rif_con LIKE ?
                    OR c.nom_con LIKE ?
                    OR f.des_fac LIKE ?
                    OR f.tot_fac LIKE ?
                )';
            $like = '%' . $term . '%';
            $params = [$like, $like, $like, $like, $like, $like];
            $types = 'ssssss';
        }

        $baseSql .= ' ORDER BY f.fec_fac ASC, f.id_fac ASC';

        return $this->fetchAll($baseSql, $types, $params);
    }

    /**
     * Lista facturas por contribuyente.
     */
    public function listByContribuyente(int $contribuyenteId): array
    {
        $sql = 'SELECT
                    f.id_fac AS id_factura,
                    f.num_fac AS num_factura,
                    DATE(f.fec_fac) AS fecha,
                    c.rif_con AS cedula_rif,
                    c.nom_con AS razon_social,
                    f.des_fac AS concepto,
                    f.tot_fac AS total_factura,
                    f.est_pago AS estado_pago,
                    f.fky_con AS cod_contribuyente
                FROM factura f
                INNER JOIN contribuyente c ON f.fky_con = c.id_con
                WHERE f.fky_con = ?
                  AND f.est_registro <> \'I\'
                ORDER BY f.fec_fac ASC, f.id_fac ASC';

        return $this->fetchAll($sql, 'i', [$contribuyenteId]);
    }

    /**
     * Lista facturas por fecha exacta.
     */
    public function listByFecha(string $fecha): array
    {
        $sql = 'SELECT
                    f.id_fac AS id_factura,
                    f.num_fac AS num_factura,
                    DATE(f.fec_fac) AS fecha,
                    c.rif_con AS cedula_rif,
                    c.nom_con AS razon_social,
                    f.des_fac AS concepto,
                    f.tot_fac AS total_factura,
                    f.est_pago AS estado_pago,
                    f.fky_con AS cod_contribuyente
                FROM factura f
                INNER JOIN contribuyente c ON f.fky_con = c.id_con
                WHERE DATE(f.fec_fac) = ?
                  AND f.est_registro <> \'I\'
                ORDER BY f.id_fac ASC';

        return $this->fetchAll($sql, 's', [$fecha]);
    }

    /**
     * Obtiene una factura junto a sus detalles.
     */
    public function findWithDetalle(int $facturaId): ?array
    {
        $sql = 'SELECT
                    f.id_fac,
                    f.num_fac,
                    DATE(f.fec_fac) AS fecha,
                    f.fky_con,
                    f.fky_usu,
                    f.des_fac,
                    f.tot_fac,
                    f.est_pago,
                    f.est_registro,
                    c.id_con AS id_contribuyente,
                    c.rif_con AS cedula_rif,
                    c.nom_con AS razon_social
                FROM factura f
                INNER JOIN contribuyente c ON f.fky_con = c.id_con
                WHERE f.id_fac = ?
                LIMIT 1';

        $factura = $this->fetchOne($sql, 'i', [$facturaId]);

        if (!$factura) {
            return null;
        }

        return [
            'factura' => $factura,
            'detalles' => $this->detalles->listByFactura($facturaId),
        ];
    }

    /**
     * Inserta una factura y sus detalles en una transacción.
     */
    public function create(array $facturaData, array $detalleItems): int
    {
        return $this->transaction(function () use ($facturaData, $detalleItems): int {
            $numero = $facturaData['num_fac'] ?? $this->generateNumero();
            $fecha = $this->normalizeDate($facturaData['fec_fac'] ?? null);

            $sql = 'INSERT INTO factura (
                        num_fac,
                        fec_fac,
                        fky_usu,
                        fky_con,
                        des_fac,
                        tot_fac,
                        est_pago,
                        est_registro
                    ) VALUES (?,?,?,?,?,?,?,?)';

            $facturaId = $this->insert($sql, 'ssiisdss', [
                $numero,
                $fecha,
                $facturaData['fky_usu'],
                $facturaData['fky_con'],
                $facturaData['des_fac'],
                $facturaData['tot_fac'],
                $facturaData['est_pago'] ?? 'A',
                $facturaData['est_registro'] ?? 'A',
            ]);

            $this->detalles->insertItems($facturaId, $detalleItems);

            return $facturaId;
        });
    }

    /**
     * Actualiza la factura básica.
     */
    public function update(int $facturaId, array $data): bool
    {
        $fecha = $this->normalizeDate($data['fec_fac'] ?? null);
        $estado = $data['est_pago'] ?? 'A';
        $fechaAnulacion = null;

        if ($estado === 'N') {
            $fechaAnulacion = (new DateTimeImmutable())->format('Y-m-d H:i:s');
        }

        $sql = 'UPDATE factura
                SET fec_fac = ?,
                    fky_usu = ?,
                    fky_con = ?,
                    des_fac = ?,
                    tot_fac = ?,
                    est_pago = ?,
                    fec_anulacion = ?
                WHERE id_fac = ?';

        $updated = $this->execute($sql, 'siisdssi', [
            $fecha,
            $data['fky_usu'],
            $data['fky_con'],
            $data['des_fac'],
            $data['tot_fac'],
            $estado,
            $fechaAnulacion,
            $facturaId,
        ]);

        if ($updated) {
            $this->detalles->markByFactura($facturaId, $estado === 'N' ? 'I' : 'A');
        }

        return $updated;
    }

    /**
     * Reemplaza completamente el detalle asociado.
     */
    public function updateDetalle(int $facturaId, array $detalleItems): void
    {
        $this->transaction(function () use ($facturaId, $detalleItems): void {
            $this->detalles->deleteByFactura($facturaId);
            $this->detalles->insertItems($facturaId, $detalleItems);
        });
    }

    /**
     * Marca una factura como anulada/inactiva.
     */
    public function delete(int $facturaId): void
    {
        $this->transaction(function () use ($facturaId): void {
            $sql = 'UPDATE factura
                    SET est_registro = \'I\',
                        est_pago = \'N\',
                        fec_anulacion = ?
                    WHERE id_fac = ?';

            $this->execute($sql, 'si', [
                (new DateTimeImmutable())->format('Y-m-d H:i:s'),
                $facturaId,
            ]);

            $this->detalles->markByFactura($facturaId, 'I');
        });
    }

    /**
     * Obtiene el ID a partir del número de control.
     */
    public function findIdByNumero(string $numero, bool $includeInactive = false): ?int
    {
        $numero = trim($numero);
        if ($numero === '') {
            return null;
        }

        $sql = 'SELECT id_fac
                FROM factura
                WHERE num_fac = ?';

        $types = 's';
        $params = [$numero];

        if (!$includeInactive) {
            $sql .= ' AND est_registro <> \'I\'';
        }

        $sql .= ' LIMIT 1';

        $row = $this->fetchOne($sql, $types, $params);

        return $row ? (int) $row['id_fac'] : null;
    }

    /**
     * Verifica si existe el número de factura.
     */
    public function existsByNumero(string $numero): bool
    {
        $sql = 'SELECT COUNT(*) AS total
                FROM factura
                WHERE num_fac = ?
                  AND est_registro <> \'I\'';

        $row = $this->fetchOne($sql, 's', [trim($numero)]);

        return $row ? (int) $row['total'] > 0 : false;
    }

    /**
     * Verifica si existe el ID indicado.
     */
    public function exists(int $facturaId): bool
    {
        $sql = 'SELECT COUNT(*) AS total
                FROM factura
                WHERE id_fac = ?
                  AND est_registro <> \'I\'';

        $row = $this->fetchOne($sql, 'i', [$facturaId]);

        return $row ? (int) $row['total'] > 0 : false;
    }

    /**
     * Genera el siguiente número correlativo.
     */
    public function nextNumber(): string
    {
        $sql = 'SELECT num_fac
                FROM factura
                ORDER BY id_fac DESC
                LIMIT 1';

        $last = $this->fetchOne($sql);

        if (!$last || empty($last['num_fac'])) {
            return $this->formatNumero(1);
        }

        $digits = preg_replace('/\D/', '', $last['num_fac']);
        $next = ((int) $digits) + 1;

        return $this->formatNumero($next);
    }

    private function generateNumero(): string
    {
        return $this->nextNumber();
    }

    private function formatNumero(int $numero): string
    {
        return 'FAC-' . str_pad((string) $numero, 6, '0', STR_PAD_LEFT);
    }

    private function normalizeDate(?string $fecha): string
    {
        if (!$fecha) {
            return (new DateTimeImmutable())->format('Y-m-d H:i:s');
        }

        if (strlen($fecha) === 10) {
            return $fecha . ' 00:00:00';
        }

        return $fecha;
    }
}
