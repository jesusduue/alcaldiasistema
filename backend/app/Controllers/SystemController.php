<?php

namespace App\Controllers;

use App\Support\LicenseGuard;
use App\Support\Response;

class SystemController
{
    private LicenseGuard $license;

    public function __construct()
    {
        $this->license = new LicenseGuard();
    }

    public function licenseStatus(): void
    {
        $active = $this->license->isActive();

        Response::json([
            'success' => true,
            'data' => [
                'active' => $active,
                'expires_at' => $this->license->getExpirationDate(),
                'message' => $active ? null : $this->license->getSupportMessage(),
                'support_contact' => $this->license->getSupportContact(),
            ],
        ]);
    }
}

