<?php

namespace App\Core;

use DateTime;
use InvalidArgumentException;

/**
 * Validaciones reutilizables para los controladores.
 */
class Validator
{
    /**
     * Valida que el valor sea un entero obligatorio.
     */
    public static function requireInt(mixed $value, string $field): int
    {
        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            throw new InvalidArgumentException("El campo {$field} debe ser un entero.");
        }

        return (int) $value;
    }

    /**
     * Valida un entero opcional.
     */
    public static function optionalInt(mixed $value): ?int
    {
        if ($value === null || $value === '' || $value === 'null') {
            return null;
        }

        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            throw new InvalidArgumentException('El valor debe ser un entero.');
        }

        return (int) $value;
    }

    /**
     * Valida un decimal opcional.
     */
    public static function optionalDecimal(mixed $value): ?float
    {
        if ($value === null || $value === '' || $value === 'null') {
            return null;
        }

        if (!is_numeric($value)) {
            throw new InvalidArgumentException('El valor debe ser numérico.');
        }

        return (float) $value;
    }

    /**
     * Valida una cadena opcional.
     */
    public static function optionalString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    /**
     * Valida una cadena obligatoria.
     */
    public static function requireString(mixed $value, string $field): string
    {
        $result = self::optionalString($value);

        if ($result === null) {
            throw new InvalidArgumentException("El campo {$field} es obligatorio.");
        }

        return $result;
    }

    /**
     * Valida un correo electrónico obligatorio.
     */
    public static function requireEmail(mixed $value, string $field): string
    {
        $email = self::requireString($value, $field);

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException("El campo {$field} debe ser un correo electrónico válido.");
        }

        return $email;
    }

    /**
     * Valida una fecha con formato YYYY-MM-DD.
     */
    public static function requireDate(mixed $value, string $field): string
    {
        $date = DateTime::createFromFormat('Y-m-d', (string) $value);

        if (!$date || $date->format('Y-m-d') !== $value) {
            throw new InvalidArgumentException("El campo {$field} debe tener el formato YYYY-MM-DD.");
        }

        return $value;
    }

    /**
     * Garantiza que el texto pertenezca a los valores permitidos.
     */
    public static function requireIn(mixed $value, string $field, array $allowed): string
    {
        $normalized = strtoupper(self::requireString($value, $field));

        if (!in_array($normalized, $allowed, true)) {
            throw new InvalidArgumentException("El campo {$field} contiene un valor no permitido.");
        }

        return $normalized;
    }
}

