<?php

declare(strict_types=1);

return [
    // Basit kurulum: SQLite (container içinde hazır).
    // Oracle'a geçmek için: DB_DRIVER=oracle (ve PHP'de OCI8/PDO_OCI kurulmuş olmalı)
    'default' => getenv('DB_DRIVER') ?: 'sqlite',

    'sqlite' => [
        'path' => __DIR__ . '/../storage/app.sqlite',
    ],

    // İstersen sonraki adımda Oracle'a da bağlayabiliriz (OCI8/PDO_OCI gerekir).
    'oracle' => [
        'host' => getenv('ORACLE_HOST') ?: 'oracle-db',
        'port' => (int)(getenv('ORACLE_PORT') ?: 1521),
        'service' => getenv('ORACLE_SERVICE') ?: 'XEPDB1',
        'username' => getenv('ORACLE_USERNAME') ?: 'system',
        'password' => getenv('ORACLE_PASSWORD') ?: 'oracle123',
    ],
];
