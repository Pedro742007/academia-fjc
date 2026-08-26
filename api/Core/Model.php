<?php
// api/Core/Model.php
namespace Core;

use PDO;

abstract class Model
{
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $dates = ['created_at', 'updated_at'];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? AND ativo = 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findBy(string $column, $value): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = ? AND ativo = 1");
        $stmt->execute([$value]);
        return $stmt->fetch() ?: null;
    }

    public function all(array $conditions = [], string $orderBy = 'id', string $orderDir = 'DESC'): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE ativo = 1";
        $params = [];

        foreach ($conditions as $column => $value) {
            $sql .= " AND {$column} = ?";
            $params[] = $value;
        }

        $sql .= " ORDER BY {$orderBy} {$orderDir}";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function paginate(int $page = 1, int $perPage = 15, array $conditions = [], string $orderBy = 'id', string $orderDir = 'DESC'): array
    {
        $offset = ($page - 1) * $perPage;
        
        $where = 'WHERE ativo = 1';
        $params = [];
        
        foreach ($conditions as $column => $value) {
            $where .= " AND {$column} = ?";
            $params[] = $value;
        }

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} {$where}");
        $countStmt->execute($params);
        $total = $countStmt->fetchColumn();

        $sql = "SELECT * FROM {$this->table} {$where} ORDER BY {$orderBy} {$orderDir} LIMIT {$perPage} OFFSET {$offset}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();

        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
        ];
    }

    public function create(array $data): int|false
    {
        $data = $this->filterFillable($data);
        $this->formatDates($data);

        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }

        if ($stmt->execute()) {
            return (int)$this->db->lastInsertId();
        }
        
        return false;
    }

    public function update(int $id, array $data): bool
    {
        $data = $this->filterFillable($data);
        $this->formatDates($data);

        $sets = [];
        foreach (array_keys($data) as $column) {
            $sets[] = "{$column} = :{$column}";
        }
        $sets[] = "updated_at = NOW()";

        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets) . " WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;

        foreach ($data as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET ativo = 0 WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }

    public function forceDelete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }

    protected function filterFillable(array $data): array
    {
        return array_intersect_key($data, array_flip($this->fillable));
    }

    protected function formatDates(array &$data): void
    {
        foreach ($this->dates as $dateField) {
            if (isset($data[$dateField]) && $data[$dateField] instanceof \DateTime) {
                $data[$dateField] = $data[$dateField]->format('Y-m-d H:i:s');
            }
        }
    }

    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function count(array $conditions = []): int
    {
        $where = 'WHERE ativo = 1';
        $params = [];
        
        foreach ($conditions as $column => $value) {
            $where .= " AND {$column} = ?";
            $params[] = $value;
        }

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} {$where}");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }
}
