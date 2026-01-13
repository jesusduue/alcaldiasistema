<?php

namespace App\Models;

use App\Core\Model;

/**
 * Maneja exportacion e importacion de paquetes de sincronizacion.
 */
class SyncModel extends Model
{
    private const VERSION = '1.0';
    private const REQUIRED_TABLES = [
        'rol',
        'usuario',
        'contribuyente',
        'tipo_impuesto',
        'factura',
        'factura_detalle',
    ];

    public function exportPackage(bool $includeLogs = true): array
    {
        $data = [
            'rol' => $this->fetchAll('SELECT id_rol, nom_rol, est_registro FROM rol ORDER BY id_rol ASC'),
            'usuario' => $this->fetchAll(
                'SELECT id_usu, nom_usu, cla_usu, fky_rol, est_registro FROM usuario ORDER BY id_usu ASC'
            ),
            'contribuyente' => $this->fetchAll(
                'SELECT id_con, nom_con, rif_con, tel_con, ema_con, dir_con, fky_usu_registro, est_registro
                FROM contribuyente
                ORDER BY id_con ASC'
            ),
            'tipo_impuesto' => $this->fetchAll(
                'SELECT id_tip, nom_tip, des_tip, est_registro FROM tipo_impuesto ORDER BY id_tip ASC'
            ),
            'factura' => $this->fetchAll(
                'SELECT id_fac, num_fac, fec_fac, fky_usu, fky_con, des_fac, tot_fac, est_pago, fec_anulacion, est_registro
                FROM factura
                ORDER BY id_fac ASC'
            ),
            'factura_detalle' => $this->fetchAll(
                'SELECT id_fde, fky_fac, fky_tip, monto_det, est_registro
                FROM factura_detalle
                ORDER BY id_fde ASC'
            ),
        ];

        if ($includeLogs) {
            $data['log_actividad'] = $this->fetchAll(
                'SELECT id_log, fec_log, fky_usu, nom_usu, modulo, accion, detalle, entidad_tipo,
                        entidad_id, metadata, ip, user_agent, est_log
                FROM log_actividad
                ORDER BY id_log ASC'
            );
        }

        $total = 0;
        $counts = [];
        foreach ($data as $table => $rows) {
            $count = count($rows);
            $counts[$table] = $count;
            $total += $count;
        }

        return [
            'metadata' => [
                'version' => self::VERSION,
                'generado_en' => date('Y-m-d H:i:s'),
                'total_registros' => $total,
                'total_por_tabla' => $counts,
            ],
            'data' => $data,
        ];
    }

    public function validatePackage(array $payload): array
    {
        $errors = [];

        if (!isset($payload['metadata']) || !is_array($payload['metadata'])) {
            $errors[] = 'El bloque metadata es requerido.';
        } else {
            $metadata = $payload['metadata'];
            if (empty($metadata['version'])) {
                $errors[] = 'metadata.version es requerido.';
            }
            if (empty($metadata['generado_en'])) {
                $errors[] = 'metadata.generado_en es requerido.';
            }
            if (!array_key_exists('total_registros', $metadata)) {
                $errors[] = 'metadata.total_registros es requerido.';
            } elseif (!is_numeric($metadata['total_registros']) && !is_array($metadata['total_registros'])) {
                $errors[] = 'metadata.total_registros debe ser numerico o un arreglo.';
            }
        }

        if (!isset($payload['data']) || !is_array($payload['data'])) {
            $errors[] = 'El bloque data es requerido.';
        } else {
            foreach (self::REQUIRED_TABLES as $table) {
                if (!array_key_exists($table, $payload['data'])) {
                    $errors[] = "Falta la tabla {$table} en data.";
                    continue;
                }
                if (!is_array($payload['data'][$table])) {
                    $errors[] = "La tabla {$table} debe ser un arreglo.";
                    continue;
                }
                $this->validateRows($table, $payload['data'][$table], $errors);
            }

            if (isset($payload['data']['log_actividad'])) {
                if (!is_array($payload['data']['log_actividad'])) {
                    $errors[] = 'log_actividad debe ser un arreglo.';
                } else {
                    $this->validateRows('log_actividad', $payload['data']['log_actividad'], $errors);
                }
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    public function importPackage(array $payload, bool $replace): array
    {
        $data = $payload['data'] ?? [];
        $includeLogs = array_key_exists('log_actividad', $data);

        return $this->transaction(function () use ($data, $replace, $includeLogs): array {
            if ($replace) {
                $this->execute('SET FOREIGN_KEY_CHECKS = 0');
                try {
                    $this->truncateTables($includeLogs);
                } finally {
                    $this->execute('SET FOREIGN_KEY_CHECKS = 1');
                }
            }

            $result = [
                'rol' => $this->upsertRoles($data['rol'] ?? []),
                'usuario' => $this->upsertUsuarios($data['usuario'] ?? []),
                'contribuyente' => $this->upsertContribuyentes($data['contribuyente'] ?? []),
                'tipo_impuesto' => $this->upsertTipoImpuesto($data['tipo_impuesto'] ?? []),
                'factura' => $this->upsertFacturas($data['factura'] ?? []),
                'factura_detalle' => $this->upsertFacturaDetalle($data['factura_detalle'] ?? []),
            ];

            if ($includeLogs) {
                $result['log_actividad'] = $this->upsertLogs($data['log_actividad'] ?? []);
            }

            return $result;
        });
    }

    public function status(): array
    {
        $tables = array_merge(self::REQUIRED_TABLES, ['log_actividad']);
        $counts = [];

        foreach ($tables as $table) {
            $counts[$table] = $this->countTable($table);
        }

        $last = $this->fetchOne(
            'SELECT fec_log, accion, nom_usu, detalle
            FROM log_actividad
            WHERE modulo = ?
            ORDER BY fec_log DESC
            LIMIT 1',
            's',
            ['Sincronizacion']
        );

        return [
            'counts' => $counts,
            'total' => array_sum($counts),
            'last_sync' => $last ? [
                'fecha' => $last['fec_log'] ?? '',
                'accion' => $last['accion'] ?? '',
                'usuario' => $last['nom_usu'] ?? '',
                'detalle' => $last['detalle'] ?? '',
            ] : null,
        ];
    }

    private function truncateTables(bool $includeLogs): void
    {
        $tables = [
            'factura_detalle',
            'factura',
            'contribuyente',
            'tipo_impuesto',
            'usuario',
            'rol',
        ];

        if ($includeLogs) {
            array_splice($tables, 3, 0, 'log_actividad');
        }

        foreach ($tables as $table) {
            $this->execute('TRUNCATE TABLE ' . $table);
        }
    }

    private function upsertRoles(array $rows): int
    {
        $sql = 'INSERT INTO rol (id_rol, nom_rol, est_registro)
                VALUES (?,?,?)
                ON DUPLICATE KEY UPDATE nom_rol = VALUES(nom_rol), est_registro = VALUES(est_registro)';
        $count = 0;

        foreach ($rows as $row) {
            if (!isset($row['id_rol'], $row['nom_rol'], $row['est_registro'])) {
                continue;
            }
            $this->execute($sql, 'iss', [
                (int) $row['id_rol'],
                (string) $row['nom_rol'],
                $this->normalizeEstado($row['est_registro']),
            ]);
            $count++;
        }

        return $count;
    }

    private function upsertUsuarios(array $rows): int
    {
        $sql = 'INSERT INTO usuario (id_usu, nom_usu, cla_usu, fky_rol, est_registro)
                VALUES (?,?,?,?,?)
                ON DUPLICATE KEY UPDATE nom_usu = VALUES(nom_usu),
                    cla_usu = VALUES(cla_usu),
                    fky_rol = VALUES(fky_rol),
                    est_registro = VALUES(est_registro)';
        $count = 0;

        foreach ($rows as $row) {
            if (!isset($row['id_usu'], $row['nom_usu'], $row['cla_usu'], $row['fky_rol'], $row['est_registro'])) {
                continue;
            }
            $this->execute($sql, 'issis', [
                (int) $row['id_usu'],
                (string) $row['nom_usu'],
                (string) $row['cla_usu'],
                (int) $row['fky_rol'],
                $this->normalizeEstado($row['est_registro']),
            ]);
            $count++;
        }

        return $count;
    }

    private function upsertContribuyentes(array $rows): int
    {
        $sql = 'INSERT INTO contribuyente (
                    id_con,
                    nom_con,
                    rif_con,
                    tel_con,
                    ema_con,
                    dir_con,
                    fky_usu_registro,
                    est_registro
                ) VALUES (?,?,?,?,?,?,?,?)
                ON DUPLICATE KEY UPDATE nom_con = VALUES(nom_con),
                    rif_con = VALUES(rif_con),
                    tel_con = VALUES(tel_con),
                    ema_con = VALUES(ema_con),
                    dir_con = VALUES(dir_con),
                    fky_usu_registro = VALUES(fky_usu_registro),
                    est_registro = VALUES(est_registro)';
        $count = 0;

        foreach ($rows as $row) {
            if (!isset(
                $row['id_con'],
                $row['nom_con'],
                $row['rif_con'],
                $row['tel_con'],
                $row['ema_con'],
                $row['dir_con'],
                $row['fky_usu_registro'],
                $row['est_registro']
            )) {
                continue;
            }

            $this->execute($sql, 'isssssis', [
                (int) $row['id_con'],
                (string) $row['nom_con'],
                (string) $row['rif_con'],
                (string) $row['tel_con'],
                (string) $row['ema_con'],
                (string) $row['dir_con'],
                (int) $row['fky_usu_registro'],
                $this->normalizeEstado($row['est_registro']),
            ]);
            $count++;
        }

        return $count;
    }

    private function upsertTipoImpuesto(array $rows): int
    {
        $sql = 'INSERT INTO tipo_impuesto (id_tip, nom_tip, des_tip, est_registro)
                VALUES (?,?,?,?)
                ON DUPLICATE KEY UPDATE nom_tip = VALUES(nom_tip),
                    des_tip = VALUES(des_tip),
                    est_registro = VALUES(est_registro)';
        $count = 0;

        foreach ($rows as $row) {
            if (!isset($row['id_tip'], $row['nom_tip'], $row['est_registro'])) {
                continue;
            }
            $this->execute($sql, 'isss', [
                (int) $row['id_tip'],
                (string) $row['nom_tip'],
                $this->normalizeNullableString($row['des_tip'] ?? null),
                $this->normalizeEstado($row['est_registro']),
            ]);
            $count++;
        }

        return $count;
    }

    private function upsertFacturas(array $rows): int
    {
        $sql = 'INSERT INTO factura (
                    id_fac,
                    num_fac,
                    fec_fac,
                    fky_usu,
                    fky_con,
                    des_fac,
                    tot_fac,
                    est_pago,
                    fec_anulacion,
                    est_registro
                ) VALUES (?,?,?,?,?,?,?,?,?,?)
                ON DUPLICATE KEY UPDATE num_fac = VALUES(num_fac),
                    fec_fac = VALUES(fec_fac),
                    fky_usu = VALUES(fky_usu),
                    fky_con = VALUES(fky_con),
                    des_fac = VALUES(des_fac),
                    tot_fac = VALUES(tot_fac),
                    est_pago = VALUES(est_pago),
                    fec_anulacion = VALUES(fec_anulacion),
                    est_registro = VALUES(est_registro)';
        $count = 0;

        foreach ($rows as $row) {
            if (!isset(
                $row['id_fac'],
                $row['num_fac'],
                $row['fec_fac'],
                $row['fky_usu'],
                $row['fky_con'],
                $row['des_fac'],
                $row['tot_fac'],
                $row['est_pago'],
                $row['est_registro']
            )) {
                continue;
            }

            $this->execute($sql, 'issiisdsss', [
                (int) $row['id_fac'],
                (string) $row['num_fac'],
                (string) $row['fec_fac'],
                (int) $row['fky_usu'],
                (int) $row['fky_con'],
                (string) $row['des_fac'],
                (float) $row['tot_fac'],
                $this->normalizePago($row['est_pago']),
                $this->normalizeNullableString($row['fec_anulacion'] ?? null),
                $this->normalizeEstado($row['est_registro']),
            ]);
            $count++;
        }

        return $count;
    }

    private function upsertFacturaDetalle(array $rows): int
    {
        $sql = 'INSERT INTO factura_detalle (id_fde, fky_fac, fky_tip, monto_det, est_registro)
                VALUES (?,?,?,?,?)
                ON DUPLICATE KEY UPDATE fky_fac = VALUES(fky_fac),
                    fky_tip = VALUES(fky_tip),
                    monto_det = VALUES(monto_det),
                    est_registro = VALUES(est_registro)';
        $count = 0;

        foreach ($rows as $row) {
            if (!isset($row['id_fde'], $row['fky_fac'], $row['fky_tip'], $row['monto_det'], $row['est_registro'])) {
                continue;
            }
            $this->execute($sql, 'iiids', [
                (int) $row['id_fde'],
                (int) $row['fky_fac'],
                (int) $row['fky_tip'],
                (float) $row['monto_det'],
                $this->normalizeEstado($row['est_registro']),
            ]);
            $count++;
        }

        return $count;
    }

    private function upsertLogs(array $rows): int
    {
        $sql = 'INSERT INTO log_actividad (
                    id_log,
                    fec_log,
                    fky_usu,
                    nom_usu,
                    modulo,
                    accion,
                    detalle,
                    entidad_tipo,
                    entidad_id,
                    metadata,
                    ip,
                    user_agent,
                    est_log
                ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)
                ON DUPLICATE KEY UPDATE fec_log = VALUES(fec_log),
                    fky_usu = VALUES(fky_usu),
                    nom_usu = VALUES(nom_usu),
                    modulo = VALUES(modulo),
                    accion = VALUES(accion),
                    detalle = VALUES(detalle),
                    entidad_tipo = VALUES(entidad_tipo),
                    entidad_id = VALUES(entidad_id),
                    metadata = VALUES(metadata),
                    ip = VALUES(ip),
                    user_agent = VALUES(user_agent),
                    est_log = VALUES(est_log)';
        $count = 0;

        foreach ($rows as $row) {
            if (!isset(
                $row['id_log'],
                $row['fec_log'],
                $row['fky_usu'],
                $row['nom_usu'],
                $row['modulo'],
                $row['accion'],
                $row['detalle'],
                $row['est_log']
            )) {
                continue;
            }

            $metadata = $row['metadata'] ?? null;
            if (is_array($metadata)) {
                $metadata = json_encode($metadata, JSON_UNESCAPED_UNICODE);
            }

            $this->execute($sql, 'isisssssissss', [
                (int) $row['id_log'],
                (string) $row['fec_log'],
                (int) $row['fky_usu'],
                (string) $row['nom_usu'],
                (string) $row['modulo'],
                (string) $row['accion'],
                (string) $row['detalle'],
                $this->normalizeNullableString($row['entidad_tipo'] ?? null),
                $this->normalizeNullableInt($row['entidad_id'] ?? null),
                $this->normalizeNullableString($metadata),
                $this->normalizeNullableString($row['ip'] ?? null),
                $this->normalizeNullableString($row['user_agent'] ?? null),
                $this->normalizeEstado($row['est_log']),
            ]);
            $count++;
        }

        return $count;
    }

    private function validateRows(string $table, array $rows, array &$errors): void
    {
        $requiredFields = $this->requiredFieldsForTable($table);

        foreach ($rows as $index => $row) {
            if (!is_array($row)) {
                $errors[] = "Fila {$index} de {$table} no es valida.";
                continue;
            }

            foreach ($requiredFields as $field) {
                if (!array_key_exists($field, $row)) {
                    $errors[] = "Falta {$field} en {$table} (fila {$index}).";
                    break;
                }
            }
        }
    }

    private function requiredFieldsForTable(string $table): array
    {
        return match ($table) {
            'rol' => ['id_rol', 'nom_rol', 'est_registro'],
            'usuario' => ['id_usu', 'nom_usu', 'cla_usu', 'fky_rol', 'est_registro'],
            'contribuyente' => ['id_con', 'nom_con', 'rif_con', 'tel_con', 'ema_con', 'dir_con', 'fky_usu_registro', 'est_registro'],
            'tipo_impuesto' => ['id_tip', 'nom_tip', 'est_registro'],
            'factura' => ['id_fac', 'num_fac', 'fec_fac', 'fky_usu', 'fky_con', 'des_fac', 'tot_fac', 'est_pago', 'est_registro'],
            'factura_detalle' => ['id_fde', 'fky_fac', 'fky_tip', 'monto_det', 'est_registro'],
            'log_actividad' => ['id_log', 'fec_log', 'fky_usu', 'nom_usu', 'modulo', 'accion', 'detalle', 'est_log'],
            default => [],
        };
    }

    private function countTable(string $table): int
    {
        $row = $this->fetchOne('SELECT COUNT(*) AS total FROM ' . $table);

        return $row ? (int) $row['total'] : 0;
    }

    private function normalizeEstado(mixed $value): string
    {
        $normalized = strtoupper(trim((string) ($value ?? 'A')));

        return $normalized === 'I' ? 'I' : 'A';
    }

    private function normalizePago(mixed $value): string
    {
        $normalized = strtoupper(trim((string) ($value ?? 'A')));

        return $normalized === 'N' ? 'N' : 'A';
    }

    private function normalizeNullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function normalizeNullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            return null;
        }

        return (int) $value;
    }
}
