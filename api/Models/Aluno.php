<?php
// api/Models/Aluno.php
namespace Models;

use Core\Model;

class Aluno extends Model
{
    protected string $table = 'alunos';
    protected string $primaryKey = 'id';
    
    protected array $fillable = [
        'numero_aluno',
        'tipo_documento',
        'numero_documento',
        'nome_completo',
        'data_nascimento',
        'idade',
        'morada',
        'provincia',
        'municipio',
        'bairro',
        'possui_irmao',
        'nome_irmao',
        'responsavel1_nome',
        'responsavel1_parentesco',
        'responsavel1_contacto',
        'responsavel2_nome',
        'responsavel2_parentesco',
        'responsavel2_contacto',
        'emergencia_nome',
        'emergencia_telefone',
        'curso_id',
        'data_inscricao',
        'valor_inscricao',
        'valor_entregue',
        'valor_pendente',
        'valor_total_pago',
        'observacoes',
        'ativo',
    ];

    protected array $dates = ['data_nascimento', 'data_inscricao', 'created_at', 'updated_at'];

    public function generateNumeroAluno(): string
    {
        $year = date('Y');
        $prefix = "FJC-{$year}-";
        $stmt = $this->db->prepare("SELECT COALESCE(MAX(CAST(SUBSTRING_INDEX(numero_aluno, '-', -1) AS UNSIGNED)), 0) FROM {$this->table} WHERE numero_aluno LIKE ?");
        $stmt->execute(["{$prefix}%"]);
        $next = (int)$stmt->fetchColumn() + 1;

        return sprintf('%s%04d', $prefix, $next);
    }

    public function paginate(int $page = 1, int $perPage = 15, array $conditions = [], string $orderBy = 'id', string $orderDir = 'DESC'): array
    {
        $offset = ($page - 1) * $perPage;

        $where = 'WHERE a.ativo = 1';
        $params = [];

        foreach ($conditions as $column => $value) {
            $where .= " AND a.{$column} = ?";
            $params[] = $value;
        }

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} a {$where}");
        $countStmt->execute($params);
        $total = $countStmt->fetchColumn();

        $sql = "SELECT a.*, c.nome as curso_nome FROM {$this->table} a 
                LEFT JOIN cursos c ON a.curso_id = c.id 
                {$where} ORDER BY a.{$orderBy} {$orderDir} LIMIT {$perPage} OFFSET {$offset}";
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

    public function search(string $term, int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;
        $searchTerm = "%{$term}%";

        $where = "WHERE ativo = 1 AND (
            numero_aluno LIKE ? OR 
            nome_completo LIKE ? OR 
            numero_documento LIKE ? OR 
            responsavel1_nome LIKE ? OR
            emergencia_nome LIKE ?
        )";

        $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm];

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} {$where}");
        $countStmt->execute($params);
        $total = $countStmt->fetchColumn();

        $sql = "SELECT a.*, c.nome as curso_nome FROM {$this->table} a 
                LEFT JOIN cursos c ON a.curso_id = c.id 
                {$where} 
                ORDER BY a.created_at DESC 
                LIMIT {$perPage} OFFSET {$offset}";
        
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

    public function getWithCurso(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT a.*, c.nome as curso_nome, c.valor_mensalidade 
            FROM {$this->table} a 
            LEFT JOIN cursos c ON a.curso_id = c.id 
            WHERE a.id = ? AND a.ativo = 1
        ");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function getAllWithCurso(array $conditions = [], string $orderBy = 'created_at', string $orderDir = 'DESC'): array
    {
        $where = 'WHERE a.ativo = 1';
        $params = [];

        foreach ($conditions as $column => $value) {
            $where .= " AND a.{$column} = ?";
            $params[] = $value;
        }

        $sql = "SELECT a.*, c.nome as curso_nome FROM {$this->table} a 
                LEFT JOIN cursos c ON a.curso_id = c.id 
                {$where} 
                ORDER BY a.{$orderBy} {$orderDir}";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getEstatisticas(): array
    {
        $stats = [];
        
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM {$this->table} WHERE ativo = 1");
        $stats['total_alunos'] = (int)$stmt->fetch()['total'];

        $stmt = $this->db->query("SELECT COUNT(*) as total FROM {$this->table} WHERE ativo = 1 AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
        $stats['novos_mes'] = (int)$stmt->fetch()['total'];

        $stmt = $this->db->query("SELECT c.nome, COUNT(a.id) as total FROM {$this->table} a LEFT JOIN cursos c ON a.curso_id = c.id WHERE a.ativo = 1 GROUP BY c.id ORDER BY total DESC");
        $stats['por_curso'] = $stmt->fetchAll();

        $stmt = $this->db->query("SELECT SUM(valor_total_pago) as total FROM {$this->table} WHERE ativo = 1");
        $stats['total_recebido'] = (float)($stmt->fetch()['total'] ?? 0);

        $stmt = $this->db->query("SELECT SUM(valor_pendente) as total FROM {$this->table} WHERE ativo = 1");
        $stats['total_pendente'] = (float)($stmt->fetch()['total'] ?? 0);

        return $stats;
    }

    public function calcularIdade(string $dataNascimento): int
    {
        $nascimento = new \DateTime($dataNascimento);
        $hoje = new \DateTime();
        return $nascimento->diff($hoje)->y;
    }

    public function calcularPendentes(array $data): array
    {
        $valorInscricao = (float)($data['valor_inscricao'] ?? 0);
        $valorEntregue = (float)($data['valor_entregue'] ?? 0);
        $valorTotalPago = (float)($data['valor_total_pago'] ?? 0);
        
        $totalPago = $valorEntregue + $valorTotalPago;
        $pendente = max(0, $valorInscricao - $totalPago);

        return [
            'valor_entregue' => $valorEntregue,
            'valor_total_pago' => $totalPago,
            'valor_pendente' => $pendente,
        ];
    }

    public function existsDocumento(string $numeroDocumento, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE numero_documento = ? AND ativo = 1";
        $params = [$numeroDocumento];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }
}
