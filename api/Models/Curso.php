<?php
// api/Models/Curso.php
namespace Models;

use Core\Model;

class Curso extends Model
{
    protected string $table = 'cursos';
    protected string $primaryKey = 'id';
    
    protected array $fillable = [
        'nome',
        'descricao',
        'valor_mensalidade',
        'ativo',
    ];

    public function getAtivos(): array
    {
        return $this->all(['ativo' => 1], 'nome', 'ASC');
    }

    public function getComAlunos(): array
    {
        $stmt = $this->db->query("
            SELECT c.*, COUNT(a.id) as total_alunos 
            FROM {$this->table} c 
            LEFT JOIN alunos a ON c.id = a.curso_id AND a.ativo = 1 
            WHERE c.ativo = 1 
            GROUP BY c.id 
            ORDER BY c.nome
        ");
        return $stmt->fetchAll();
    }
}
