<?php
// api/Controllers/AuthController.php
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

            // Rate limiting: max 5 tentativas por IP a cada 15 min
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $rateKey = 'rate_login_' . md5($ip);
            $attempts = $_SESSION[$rateKey]['count'] ?? 0;
            $lastAttempt = $_SESSION[$rateKey]['last'] ?? 0;

            if ($attempts >= 5 && (time() - $lastAttempt) < 900) {
                $remaining = 900 - (time() - $lastAttempt);
                $errors[] = "Muitas tentativas. Tente novamente em " . ceil($remaining / 60) . " minutos.";
            } else {
                $email = trim($_POST['email'] ?? '');
                $password = $_POST['password'] ?? '';
                $remember = isset($_POST['remember']);

                if (empty($email) || empty($password)) {
                    $errors[] = 'Preencha todos os campos.';
                } else {
                    // Sanitizar email
                    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

                    $usuario = $this->usuarioModel->verifyPassword($email, $password);
                    
                    if ($usuario) {
                        // Login bem-sucedido: limpar rate limit
                        unset($_SESSION[$rateKey]);

                        // Regenerar sessão para prevenir fixation
                        session_regenerate_id(true);

                        $_SESSION['usuario_id'] = $usuario['id'];
                        $_SESSION['usuario_nome'] = $usuario['nome'];
                        $_SESSION['usuario_email'] = $usuario['email'];
                        $_SESSION['usuario_nivel'] = $usuario['nivel'];
                        $_SESSION['login_time'] = time();
                        
                        $this->usuarioModel->updateLastLogin($usuario['id']);
                        
                        if ($remember) {
                            $token = bin2hex(random_bytes(32));
                            setcookie('remember_token', $usuario['id'] . '|' . $token, [
                                'expires'  => time() + (86400 * 30),
                                'path'     => '/',
                                'httponly'  => true,
                                'samesite' => 'Lax',
                            ]);
                        }
                        
                        $this->flash('success', 'Bem-vindo, ' . $this->sanitize($usuario['nome']) . '!');
                        $this->redirect($this->isAdmin() ? '/alunos' : '/inscricao');
                        return;
                    } else {
                        // Login falhou: incrementar rate limit
                        $_SESSION[$rateKey]['count'] = $attempts + 1;
                        $_SESSION[$rateKey]['last'] = time();
                        $errors[] = 'Email ou senha inválidos.';
                    }
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
        // Limpar rate limit
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        unset($_SESSION['rate_login_' . md5($ip)]);
        
        // Limpar cookie remember
        setcookie('remember_token', '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'httponly'  => true,
            'samesite' => 'Lax',
        ]);
        
        $_SESSION = [];
        session_destroy();
        
        // Forçar limpeza do cookie de sessão
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        $this->redirect('/inscricao');
    }
}
