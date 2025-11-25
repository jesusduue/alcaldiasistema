<?php

namespace App\Core;

use Closure;
use mysqli;
use mysqli_stmt;
use Throwable;

/**
 * Proporciona utilidades comunes para los modelos basados en mysqli.
 */
abstract class Model
{
    protected mysqli $db;

    public function __construct(?mysqli $connection = null)
    {
        $this->db = $connection ?? Database::connection();
    }

    /**
     * Ejecuta una consulta que retorna múltiples registros.
     */
    protected function fetchAll(string $sql, string $types = '', array $params = []): array
    {
        $stmt = $this->prepare($sql, $types, $params);
        $result = $stmt->get_result();

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Ejecuta una consulta que retorna un solo registro.
     */
    protected function fetchOne(string $sql, string $types = '', array $params = []): ?array
    {
        $stmt = $this->prepare($sql, $types, $params);
        $result = $stmt->get_result();

        return $result ? $result->fetch_assoc() ?: null : null;
    }

    /**
     * Ejecuta una operación de escritura (INSERT/UPDATE/DELETE).
     */
    protected function execute(string $sql, string $types = '', array $params = []): bool
    {
        $stmt = $this->prepare($sql, $types, $params);

        return $stmt->affected_rows >= 0;
    }

    /**
     * Ejecuta un INSERT y retorna el ID generado.
     */
    protected function insert(string $sql, string $types = '', array $params = []): int
    {
        $stmt = $this->prepare($sql, $types, $params);

        return $stmt->insert_id ?: $this->db->insert_id;
    }

    /**
     * Ejecuta una transacción envolviendo commit/rollback.
     */
    protected function transaction(Closure $callback): mixed
    {
        $this->db->begin_transaction();

        try {
            $result = $callback($this->db);
            $this->db->commit();
        } catch (Throwable $throwable) {
            $this->db->rollback();
            throw $throwable;
        }

        return $result;
    }

    /**
     * Prepara el statement y realiza el enlace de parámetros cuando aplica.
     */
    private function prepare(string $sql, string $types = '', array $params = []): mysqli_stmt
    {
        $stmt = $this->db->prepare($sql);

        if ($types !== '' && $params !== []) {
            $refs = [];
            foreach ($params as $key => $value) {
                $refs[$key] = &$params[$key];
            }
            $stmt->bind_param($types, ...$refs);
        }

        $stmt->execute();

        return $stmt;
    }
}

