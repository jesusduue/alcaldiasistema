<?php

namespace App\Repositories;

use App\Config\Database;
use PDO;

class DetalleReciboRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
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
                monto_impuesto_F
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
                :monto_impuesto_F
            )'
        );

        $stmt->execute([
            ':fecha_det_recibo' => $data['fecha_det_recibo'],
            ':cod_factura' => $data['cod_factura'],
            ':impuesto_A' => $data['impuesto_A'],
            ':monto_impuesto_A' => $data['monto_impuesto_A'],
            ':impuesto_B' => $data['impuesto_B'],
            ':monto_impuesto_B' => $data['monto_impuesto_B'],
            ':impuesto_C' => $data['impuesto_C'],
            ':monto_impuesto_C' => $data['monto_impuesto_C'],
            ':impuesto_D' => $data['impuesto_D'],
            ':monto_impuesto_D' => $data['monto_impuesto_D'],
            ':impuesto_E' => $data['impuesto_E'],
            ':monto_impuesto_E' => $data['monto_impuesto_E'],
            ':impuesto_F' => $data['impuesto_F'],
            ':monto_impuesto_F' => $data['monto_impuesto_F'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function deleteByFactura(int $numFactura): bool
    {
        $stmt = $this->db->prepare(
            'DELETE FROM detalle_recibo WHERE cod_factura = :cod_factura'
        );

        return $stmt->execute([':cod_factura' => $numFactura]);
    }

    public function deleteById(int $idDetalle): bool
    {
        $stmt = $this->db->prepare(
            'DELETE FROM detalle_recibo WHERE id_detalle_recibo = :id'
        );

        return $stmt->execute([':id' => $idDetalle]);
    }

    public function listByFecha(string $fecha): array
    {
        $stmt = $this->db->prepare(
            'SELECT
                d.id_detalle_recibo,
                d.fecha_det_recibo,
                d.cod_factura,
                cA.nombre AS nombre_impuesto_A,
                d.monto_impuesto_A,
                cB.nombre AS nombre_impuesto_B,
                d.monto_impuesto_B,
                cC.nombre AS nombre_impuesto_C,
                d.monto_impuesto_C,
                cD.nombre AS nombre_impuesto_D,
                d.monto_impuesto_D,
                cE.nombre AS nombre_impuesto_E,
                d.monto_impuesto_E,
                cF.nombre AS nombre_impuesto_F,
                d.monto_impuesto_F
            FROM detalle_recibo d
            LEFT JOIN clasificador cA ON d.impuesto_A = cA.id_clasificador
            LEFT JOIN clasificador cB ON d.impuesto_B = cB.id_clasificador
            LEFT JOIN clasificador cC ON d.impuesto_C = cC.id_clasificador
            LEFT JOIN clasificador cD ON d.impuesto_D = cD.id_clasificador
            LEFT JOIN clasificador cE ON d.impuesto_E = cE.id_clasificador
            LEFT JOIN clasificador cF ON d.impuesto_F = cF.id_clasificador
            WHERE d.fecha_det_recibo = :fecha
            ORDER BY d.cod_factura ASC'
        );

        $stmt->execute([':fecha' => $fecha]);

        return $stmt->fetchAll();
    }
}

