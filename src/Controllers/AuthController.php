<?php
// src/Controllers/AuthController.php
namespace Controllers;

use Core\Controller;
use Models\Usuario;

class AuthController extends Controller
{
    private Usuario $usuarioModel;

    public function __construct()
    {
        parent::__construct();
        $this->usuarioModel = new Usuario();
    }

    public function login(): void
    {
        if (isset($_SESSION['usuario_id'])) {
            $this->redirect($this->isAdmin() ? '/alunos' : '/inscricao');
            return;
        }

        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']);

            if (empty($email) || empty($password)) {
                $errors[] = 'Preencha todos os campos.';
            } else {
                $usuario = $this->usuarioModel->verifyPassword($email, $password);
                
                if ($usuario) {
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['usuario_nome'] = $usuario['nome'];
                    $_SESSION['usuario_email'] = $usuario['email'];
                    $_SESSION['usuario_nivel'] = $usuario['nivel'];
                    
                    $this->usuarioModel->updateLastLogin($usuario['id']);
                    
                    if ($remember) {
                        setcookie('remember_token', $usuario['id'] . '|' . bin2hex(random_bytes(32)), time() + (86400 * 30), '/', '', false, true);
                    }
                    
                    $this->flash('success', 'Bem-vindo, ' . $usuario['nome'] . '!');
                    $this->redirect($this->isAdmin() ? '/alunos' : '/inscricao');
                    return;
                } else {
                    $errors[] = 'Email ou senha inválidos.';
                }
            }
        }

        $this->view('auth/login', [
            'errors' => $errors,
            'old_email' => $_POST['email'] ?? '',
        ], 'auth');
    }

    public function logout(): void
    {
        session_destroy();
        setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        $this->redirect('/inscricao');
    }
}
