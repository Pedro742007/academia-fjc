<?php
// api/Core/DbSessionHandler.php
// Session handler via MySQL — funciona em serverless (Vercel)
namespace Core;

use SessionHandler;
use PDO;

class DbSessionHandler extends SessionHandler
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->createTable();
    }

    private function createTable(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS `sessions` (
                `id` VARCHAR(128) NOT NULL PRIMARY KEY,
                `data` MEDIUMBLOB NOT NULL,
                `timestamp` INT UNSIGNED NOT NULL,
                `ip` VARCHAR(45) DEFAULT NULL,
                `user_agent` VARCHAR(512) DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    public function read(string $id): string|false
    {
        $stmt = $this->db->prepare("SELECT data, timestamp FROM sessions WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) return '';

        // Expirar sessões antigas (> 30 min)
        if (time() - (int)$row['timestamp'] > 1800) {
            $this->destroy($id);
            return '';
        }

        return $row['data'];
    }

    public function write(string $id, string $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO sessions (id, data, timestamp, ip, user_agent) 
            VALUES (?, ?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE data = VALUES(data), timestamp = VALUES(timestamp)
        ");
        return $stmt->execute([
            $id,
            $data,
            time(),
            $_SERVER['REMOTE_ADDR'] ?? null,
            substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 512),
        ]);
    }

    public function destroy(string $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM sessions WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function gc(int $max_lifetime): int|false
    {
        $stmt = $this->db->prepare("DELETE FROM sessions WHERE timestamp < ?");
        $stmt->execute([time() - $max_lifetime]);
        return $stmt->rowCount();
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }
}
