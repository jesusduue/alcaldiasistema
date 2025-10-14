<?php

namespace App\Repositories;

use App\Config\Database;
use PDO;
use PDOException;

class FacturaRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(array $facturaData, array $detalleData): int
    {
        try {
            $this->db->beginTransaction();

            $stmtFactura = $this->db->prepare(
                'INSERT INTO factura (
                    fecha,
                    id_usuario,
                    cod_contribuyente,
                    concepto,
                    total_factura,
                    ESTADO_FACT
                ) VALUES (
                    :fecha,
                    :id_usuario,
                    :cod_contribuyente,
                    :concepto,
                    :total_factura,
                    :estado
                )'
            );

            $stmtFactura->execute([
                ':fecha' => $facturaData['fecha'],
                ':id_usuario' => $facturaData['id_usuario'],
                ':cod_contribuyente' => $facturaData['cod_contribuyente'],
                ':concepto' => $facturaData['concepto'],
                ':total_factura' => $facturaData['total_factura'],
                ':estado' => $facturaData['estado'],
            ]);

            $facturaId = (int) $this->db->lastInsertId();

            $stmtDetalle = $this->db->prepare(
                'INSERT INTO detalle_recibo (
                    fecha_det_recibo,
                    cod_factura,
                    impuesto_A,
                    monto_impuesto_A,
                    impuesto_B,
                    monto_impuesto_B,
                    impuesto_C,
                    monto_impuesto_C,
                    impuesto_D,
                    monto_impuesto_D,
                    impuesto_E,
                    monto_impuesto_E,
                    impuesto_F,
                    monto_impuesto_F,
                    est_registro
                ) VALUES (
                    :fecha_det_recibo,
                    :cod_factura,
                    :impuesto_A,
                    :monto_impuesto_A,
                    :impuesto_B,
                    :monto_impuesto_B,
                    :impuesto_C,
                    :monto_impuesto_C,
                    :impuesto_D,
                    :monto_impuesto_D,
                    :impuesto_E,
                    :monto_impuesto_E,
                    :impuesto_F,
                    :monto_impuesto_F,
                    :est_registro
                )'
            );

            $stmtDetalle->execute([
                ':fecha_det_recibo' => $detalleData['fecha_det_recibo'],
                ':cod_factura' => $facturaId,
                ':impuesto_A' => $detalleData['impuesto_A'],
                ':monto_impuesto_A' => $detalleData['monto_impuesto_A'],
                ':impuesto_B' => $detalleData['impuesto_B'],
                ':monto_impuesto_B' => $detalleData['monto_impuesto_B'],
                ':impuesto_C' => $detalleData['impuesto_C'],
                ':monto_impuesto_C' => $detalleData['monto_impuesto_C'],
                ':impuesto_D' => $detalleData['impuesto_D'],
                ':monto_impuesto_D' => $detalleData['monto_impuesto_D'],
                ':impuesto_E' => $detalleData['impuesto_E'],
                ':monto_impuesto_E' => $detalleData['monto_impuesto_E'],
                ':impuesto_F' => $detalleData['impuesto_F'],
                ':monto_impuesto_F' => $detalleData['monto_impuesto_F'],
                ':est_registro' => $detalleData['est_registro'] ?? 'activo',
            ]);

            $this->db->commit();

            return $facturaId;
        } catch (PDOException $exception) {
            $this->db->rollBack();
            throw $exception;
        }
    }

    public function update(int $numFactura, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE factura
             SET fecha = :fecha,
                 id_usuario = :id_usuario,
                 cod_contribuyente = :cod_contribuyente,
                 concepto = :concepto,
                 total_factura = :total_factura,
                 ESTADO_FACT = :estado
             WHERE num_factura = :num_factura'
        );

        return $stmt->execute([
            ':fecha' => $data['fecha'],
            ':id_usuario' => $data['id_usuario'],
            ':cod_contribuyente' => $data['cod_contribuyente'],
            ':concepto' => $data['concepto'],
            ':total_factura' => $data['total_factura'],
            ':estado' => $data['estado'],
            ':num_factura' => $numFactura,
        ]);
    }

    public function updateDetalle(int $numFactura, array $detalleData): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE detalle_recibo
             SET impuesto_A = :impuesto_A,
                 monto_impuesto_A = :monto_impuesto_A,
                 impuesto_B = :impuesto_B,
                 monto_impuesto_B = :monto_impuesto_B,
                 impuesto_C = :impuesto_C,
                 monto_impuesto_C = :monto_impuesto_C,
                 impuesto_D = :impuesto_D,
                 monto_impuesto_D = :monto_impuesto_D,
                 impuesto_E = :impuesto_E,
                 monto_impuesto_E = :monto_impuesto_E,
                 impuesto_F = :impuesto_F,
                 monto_impuesto_F = :monto_impuesto_F,
                 est_registro = :est_registro
             WHERE cod_factura = :cod_factura'
        );

        return $stmt->execute([
            ':impuesto_A' => $detalleData['impuesto_A'],
            ':monto_impuesto_A' => $detalleData['monto_impuesto_A'],
            ':impuesto_B' => $detalleData['impuesto_B'],
            ':monto_impuesto_B' => $detalleData['monto_impuesto_B'],
            ':impuesto_C' => $detalleData['impuesto_C'],
            ':monto_impuesto_C' => $detalleData['monto_impuesto_C'],
            ':impuesto_D' => $detalleData['impuesto_D'],
            ':monto_impuesto_D' => $detalleData['monto_impuesto_D'],
            ':impuesto_E' => $detalleData['impuesto_E'],
            ':monto_impuesto_E' => $detalleData['monto_impuesto_E'],
            ':impuesto_F' => $detalleData['impuesto_F'],
            ':monto_impuesto_F' => $detalleData['monto_impuesto_F'],
            ':est_registro' => $detalleData['est_registro'] ?? 'activo',
            ':cod_factura' => $numFactura,
        ]);
    }

    public function find(int $numFactura): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT num_factura, fecha, id_usuario, cod_contribuyente, concepto, total_factura, ESTADO_FACT
             FROM factura
             WHERE num_factura = :num_factura'
        );
        $stmt->execute([':num_factura' => $numFactura]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function findWithDetalle(int $numFactura): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT
                f.num_factura,
                f.fecha,
                f.cod_contribuyente,
                c.id_contribuyente,
                c.cedula_rif,
                c.razon_social,
                f.concepto,
                f.total_factura,
                f.ESTADO_FACT,
                d.impuesto_A,
                cA.nombre AS nombre_impuesto_A,
                d.monto_impuesto_A,
                d.impuesto_B,
                cB.nombre AS nombre_impuesto_B,
                d.monto_impuesto_B,
                d.impuesto_C,
                cC.nombre AS nombre_impuesto_C,
                d.monto_impuesto_C,
                d.impuesto_D,
                cD.nombre AS nombre_impuesto_D,
                d.monto_impuesto_D,
                d.impuesto_E,
                cE.nombre AS nombre_impuesto_E,
                d.monto_impuesto_E,
                d.impuesto_F,
                cF.nombre AS nombre_impuesto_F,
                d.monto_impuesto_F
            FROM factura f
            INNER JOIN contribuyente c ON f.cod_contribuyente = c.id_contribuyente
            INNER JOIN detalle_recibo d ON f.num_factura = d.cod_factura
            LEFT JOIN clasificador cA ON d.impuesto_A = cA.id_clasificador
            LEFT JOIN clasificador cB ON d.impuesto_B = cB.id_clasificador
            LEFT JOIN clasificador cC ON d.impuesto_C = cC.id_clasificador
            LEFT JOIN clasificador cD ON d.impuesto_D = cD.id_clasificador
              LEFT JOIN clasificador cE ON d.impuesto_E = cE.id_clasificador
              LEFT JOIN clasificador cF ON d.impuesto_F = cF.id_clasificador
              WHERE f.num_factura = :num_factura
                AND d.est_registro = \'activo\''
        );

        $stmt->execute([':num_factura' => $numFactura]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function listAll(?string $term = null): array
    {
        if ($term === null || $term === '') {
            $stmt = $this->db->query(
                'SELECT
                    f.num_factura,
                    f.fecha,
                    c.cedula_rif,
                    c.razon_social,
                    f.concepto,
                    f.total_factura,
                    f.ESTADO_FACT
                 FROM factura f
                 INNER JOIN contribuyente c ON f.cod_contribuyente = c.id_contribuyente
                 WHERE f.ESTADO_FACT <> \'eliminado\'
                 ORDER BY f.num_factura ASC'
            );

            return $stmt->fetchAll();
        }

        $likeTerm = '%' . $term . '%';
        $stmt = $this->db->prepare(
            'SELECT
                f.num_factura,
                f.fecha,
                c.cedula_rif,
                c.razon_social,
                f.concepto,
                f.total_factura,
                f.ESTADO_FACT
             FROM factura f
             INNER JOIN contribuyente c ON f.cod_contribuyente = c.id_contribuyente
             WHERE f.ESTADO_FACT <> \'eliminado\'
               AND (
                    f.fecha LIKE :term
                    OR c.cedula_rif LIKE :term
                    OR c.razon_social LIKE :term
                    OR f.concepto LIKE :term
                    OR f.total_factura LIKE :term
                    OR f.ESTADO_FACT LIKE :term
               )
             ORDER BY f.num_factura ASC'
        );
        $stmt->execute([':term' => $likeTerm]);

        return $stmt->fetchAll();
    }

    public function listByFecha(string $fecha): array
    {
        $stmt = $this->db->prepare(
            'SELECT
                f.num_factura,
                f.fecha,
                c.cedula_rif,
                c.razon_social,
                f.concepto,
                f.total_factura,
                f.ESTADO_FACT
             FROM factura f
             INNER JOIN contribuyente c ON f.cod_contribuyente = c.id_contribuyente
             WHERE f.fecha = :fecha
               AND f.ESTADO_FACT <> \'eliminado\'
             ORDER BY f.num_factura ASC'
        );
        $stmt->execute([':fecha' => $fecha]);

        return $stmt->fetchAll();
    }

    public function listByContribuyente(int $idContribuyente): array
    {
        $stmt = $this->db->prepare(
            'SELECT
                f.num_factura,
                f.fecha,
                c.cedula_rif,
                c.razon_social,
                f.concepto,
                f.total_factura,
                f.ESTADO_FACT
             FROM factura f
             INNER JOIN contribuyente c ON f.cod_contribuyente = c.id_contribuyente
             WHERE f.cod_contribuyente = :id
               AND f.ESTADO_FACT <> \'eliminado\'
             ORDER BY f.fecha DESC, f.num_factura DESC'
        );
        $stmt->execute([':id' => $idContribuyente]);

        return $stmt->fetchAll();
    }

    public function exists(int $numFactura): bool
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) as total
             FROM factura
             WHERE num_factura = :num_factura'
        );
        $stmt->execute([':num_factura' => $numFactura]);
        $result = $stmt->fetch();

        return isset($result['total']) && (int) $result['total'] > 0;
    }

    public function delete(int $numFactura): bool
    {
        $stmtFactura = $this->db->prepare(
            'UPDATE factura
             SET ESTADO_FACT = :estado
             WHERE num_factura = :num_factura'
        );
        $stmtDetalle = $this->db->prepare(
            'UPDATE detalle_recibo
             SET est_registro = :estado
             WHERE cod_factura = :num_factura'
        );

        try {
            $this->db->beginTransaction();
            $stmtFactura->execute([
                ':estado' => 'eliminado',
                ':num_factura' => $numFactura,
            ]);
            $stmtDetalle->execute([
                ':estado' => 'inactivo',
                ':num_factura' => $numFactura,
            ]);
            $this->db->commit();
        } catch (PDOException $exception) {
            $this->db->rollBack();
            throw $exception;
        }

        return true;
    }

    public function nextNumber(): int
    {
        $stmt = $this->db->query(
            'SELECT COALESCE(MAX(num_factura) + 1, 1) AS siguiente FROM factura'
        );
        $row = $stmt->fetch();

        return isset($row['siguiente']) ? (int) $row['siguiente'] : 1;
    }
}
