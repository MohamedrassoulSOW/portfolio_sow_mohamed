<?php
/**
 * Configuration base de données.
 * En local (WAMP) : valeurs locales utilisées automatiquement.
 * En production : renseigne les constantes PROD_* ci-dessous (Hostinger, etc.).
 */

function estEnvironnementLocal(): bool {
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return str_contains($host, 'localhost')
        || $host === '127.0.0.1'
        || str_starts_with($host, '192.168.');
}

function getDbConfig(): array {
    if (estEnvironnementLocal()) {
        return [
            'host' => 'localhost',
            'dbname' => 'portfolio',
            'user' => 'root',
            'pass' => '',
        ];
    }

    // ========== PRODUCTION — à remplir avec tes infos hébergeur ==========
    return [
        'host' => 'localhost',              // souvent localhost chez Hostinger
        'dbname' => 'U_CHANGE_DB_NAME',     // ex: u452929230_portfolio
        'user' => 'U_CHANGE_DB_USER',       // ex: u452929230_mohamed
        'pass' => 'U_CHANGE_DB_PASSWORD',   // ton mot de passe MySQL
    ];
}
