<?php
// src/Models/Usuario.php
namespace Models;

use Core\Model;

class Usuario extends Model
{
    protected string $table = 'usuarios';
    protected string $primaryKey = 'id';
    
    protected array $fillable = [
        'nome',
        'email',
        'senha',
        'nivel',
        'ativo',
    ];

    protected array $dates = ['created_at', 'updated_at', 'ultimo_login'];

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ? AND ativo = 1");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function verifyPassword(string $email, string $password): ?array
    {
        $usuario = $this->findByEmail($email);
        
        if (!$usuario) {
            return null;
        }
        
        if (password_verify($password, $usuario['senha'])) {
            return $usuario;
        }
        
        return null;
    }

    public function updateLastLogin(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET ultimo_login = NOW() WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }

    public function getAllWithStats(): array
    {
        return $this->db->query("
            SELECT u.*, 
                   (SELECT COUNT(*) FROM alunos a WHERE a.ativo = 1) as total_alunos
            FROM {$this->table} u 
            WHERE u.ativo = 1 
            ORDER BY u.nome
        ");
    }
}