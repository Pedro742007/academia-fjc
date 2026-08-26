<?php
// src/Core/Database.php
namespace Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;
    private array $config;

    private function __construct(array $config)
    {
        $this->config = $config;
    }

    public static function getInstance(array $config = []): PDO
    {
        if (self::$instance === null) {
            if (empty($config)) {
                $config = require __DIR__ . '/../config/database.php';
            }
            
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
            
            try {
                self::$instance = new PDO($dsn, $config['username'], $config['password'], $config['options']);
            } catch (PDOException $e) {
                http_response_code(500);
                die('Erro de conexão com o banco de dados. Verifique as configurações.');
            }
        }
        
        return self::$instance;
    }

    public static function close(): void
    {
        self::$instance = null;
    }
}
