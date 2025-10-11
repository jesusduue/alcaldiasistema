<?php

namespace App\Support;

class Validator
{
    public static function requireInt(mixed $value, string $field): int
    {
        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            throw new \InvalidArgumentException("El campo {$field} debe ser un entero.");
        }

        return (int) $value;
    }

    public static function optionalInt(mixed $value): ?int
    {
        if ($value === null || $value === '' || $value === 'null') {
            return null;
        }

        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            throw new \InvalidArgumentException('El valor debe ser un entero.');
        }

        return (int) $value;
    }

    public static function optionalDecimal(mixed $value): ?float
    {
        if ($value === null || $value === '' || $value === 'null') {
            return null;
        }

        if (!is_numeric($value)) {
            throw new \InvalidArgumentException('El valor debe ser numérico.');
        }

        return (float) $value;
    }

    public static function optionalString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    public static function requireDate(mixed $value, string $field): string
    {
        $date = \DateTime::createFromFormat('Y-m-d', (string) $value);

        if (!$date || $date->format('Y-m-d') !== $value) {
            throw new \InvalidArgumentException("El campo {$field} debe tener el formato YYYY-MM-DD.");
        }

        return $value;
    }
}

