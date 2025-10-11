<?php

namespace App\Support;

use DateTimeImmutable;
use RuntimeException;

/**
 * Valida si la licencia del sistema sigue vigente.
 */
class LicenseGuard
{
    private DateTimeImmutable $expiresAt;
    private string $supportMessage;
    private string $supportContact;
    private ?bool $cachedActive = null;

    public function __construct()
    {
        $settings = require __DIR__ . '/../Config/settings.php';
        $license = $settings['license'] ?? [];

        $this->expiresAt = new DateTimeImmutable($license['expires_at'] ?? '2099-12-31');
        $this->supportMessage = $license['support_message'] ?? 'Licencia expirada.';
        $this->supportContact = $license['support_contact'] ?? '';
    }

    /**
     * Lanza una excepción cuando la licencia está vencida.
     */
    public function ensureActive(): void
    {
        $today = new DateTimeImmutable('today');

        if ($today <= $this->expiresAt) {
            $this->cachedActive = true;
            return;
        }

        $this->cachedActive = false;
        $message = $this->supportMessage;

        if ($this->supportContact !== '') {
            $message .= ' Contacto: ' . $this->supportContact;
        }

        throw new RuntimeException($message);
    }

    public function getExpirationDate(): string
    {
        return $this->expiresAt->format('Y-m-d');
    }

    public function getSupportContact(): string
    {
        return $this->supportContact;
    }

    public function getSupportMessage(): string
    {
        return $this->supportMessage;
    }

    public function isActive(): bool
    {
        if ($this->cachedActive !== null) {
            return $this->cachedActive;
        }

        $today = new DateTimeImmutable('today');
        $this->cachedActive = $today <= $this->expiresAt;

        return $this->cachedActive;
    }
}
