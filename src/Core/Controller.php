<?php
// src/Core/Controller.php
namespace Core;

abstract class Controller
{
    protected array $data = [];
    protected string $layout = 'main';

    public function __construct()
    {
        $this->data['app_name'] = 'Academia FJC';
        $this->data['current_year'] = date('Y');
    }

    protected function isLoggedIn(): bool
    {
        return isset($_SESSION['usuario_id']);
    }

    protected function isAdmin(): bool
    {
        return ($_SESSION['usuario_nivel'] ?? '') === 'admin';
    }

    protected function requireAuth(): void
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('/inscricao');
        }
    }

    protected function requireAdmin(): void
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('/inscricao');
        }

        if (!$this->isAdmin()) {
            $this->flash('error', 'Acesso restrito ao administrador.');
            $this->redirect('/inscricao');
        }
    }

    protected function view(string $view, array $data = [], string $layout = null): void
    {
        $this->data = array_merge($this->data, $data);
        $layout = $layout ?? $this->layout;
        
        $viewFile = __DIR__ . "/../Views/{$view}.php";
        $layoutFile = __DIR__ . "/../Views/layout/{$layout}.php";

        if (!file_exists($viewFile)) {
            die("View não encontrada: {$view}");
        }

        ob_start();
        extract($this->data);
        include $viewFile;
        $content = ob_get_clean();

        if (file_exists($layoutFile)) {
            include $layoutFile;
        } else {
            echo $content;
        }
    }

    protected function redirect(string $url): void
    {
        header("Location: " . \url($url));
        exit;
    }

    protected function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        header("Location: {$referer}");
        exit;
    }

    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function validate(array $data, array $rules): array
    {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            $rulesList = explode('|', $rule);
            
            foreach ($rulesList as $r) {
                if ($r === 'required' && empty($value)) {
                    $errors[$field] = "O campo {$field} é obrigatório.";
                    break;
                }
                
                if ($value !== null && $value !== '') {
                    if (str_starts_with($r, 'min:')) {
                        $min = (int)substr($r, 4);
                        if (strlen($value) < $min) {
                            $errors[$field] = "O campo {$field} deve ter no mínimo {$min} caracteres.";
                        }
                    }
                    
                    if (str_starts_with($r, 'max:')) {
                        $max = (int)substr($r, 4);
                        if (strlen($value) > $max) {
                            $errors[$field] = "O campo {$field} deve ter no máximo {$max} caracteres.";
                        }
                    }
                    
                    if ($r === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errors[$field] = "O campo {$field} deve ser um email válido.";
                    }
                    
                    if ($r === 'numeric' && !is_numeric($value)) {
                        $errors[$field] = "O campo {$field} deve ser numérico.";
                    }
                    
                    if ($r === 'date' && !$this->isValidDate($value)) {
                        $errors[$field] = "O campo {$field} deve ser uma data válida (YYYY-MM-DD).";
                    }
                }
            }
        }
        
        return $errors;
    }

    private function isValidDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    protected function old(string $field, $default = ''): string
    {
        return $_SESSION['old'][$field] ?? $default;
    }

    protected function flash(string $key, string $message): void
    {
        $_SESSION['flash'][$key] = $message;
    }

    protected function getFlash(string $key): ?string
    {
        $msg = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $msg;
    }

    protected function verifyCsrf(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
                http_response_code(403);
                die('Token CSRF inválido. Volte e tente novamente.');
            }
        }
    }
}