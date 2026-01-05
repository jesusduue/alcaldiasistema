<?php

return [
    'db' => [
        'host' => 'localhost',
        'database' => 'sist_alcaldia_2026',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ],
    'license' => [
        // Fecha límite para generación de nuevas facturas.
        'expires_at' => '2026-12-31',
        // Texto que se mostrará cuando la licencia haya expirado.
        'support_message' => 'La licencia ha expirado. Contacte al desarrollador para renovarla.',
        // Nombre de contacto o medio sugerido.
        'support_contact' => 'jesusduqueq@gmail.com',
    ],
];
