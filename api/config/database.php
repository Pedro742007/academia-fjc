<?php
// api/config/database.php
// Configuração de banco de dados - usa env vars (Vercel) ou fallback local

$isLocal = (getenv('VERCEL') === false || getenv('VERCEL') === '');

// Carrega config local se existir (para desenvolvimento)
if ($isLocal && file_exists(__DIR__ . '/database.local.php')) {
    $config = require __DIR__ . '/database.local.php';
} else {
    $config = [
        'host'     => getenv('DB_HOST') ?: 'sql202.infinityfree.com',
        'port'     => (int)(getenv('DB_PORT') ?: '3306'),
        'dbname'   => getenv('DB_NAME') ?: 'if0_42750170_Academiafjc',
        'username' => getenv('DB_USER') ?: 'if0_42750170',
        'password' => getenv('DB_PASS') ?: '',
        'charset'  => 'utf8mb4',
        'options'  => [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_PERSISTENT         => false,
        ],
    ];
}

return $config;
