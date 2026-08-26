<?php
// api/index.php - Vercel Serverless Entry Point
// Academia FJC - Todas as rotas passam por aqui

// ── Base paths ──────────────────────────────────────────────
$base = dirname(__DIR__);

// ── Autoloaders ─────────────────────────────────────────────
spl_autoload_register(function ($class) use ($base) {
    $map = [
        'Core\\'       => $base . '/api/Core/',
        'Models\\'     => $base . '/api/Models/',
        'Controllers\\'=> $base . '/api/Controllers/',
    ];
    foreach ($map as $prefix => $dir) {
        if (strncmp($prefix, $class, strlen($prefix)) === 0) {
            $rel = substr($class, strlen($prefix));
            $file = $dir . str_replace('\\', '/', $rel) . '.php';
            if (file_exists($file)) { require_once $file; return; }
        }
    }
});

// ── Helpers & Config ────────────────────────────────────────
require_once $base . '/api/config/helpers.php';

$config = require $base . '/api/config/database.php';

// ── Security headers (Vercel adiciona os do vercel.json, mas reforça) ──
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

// ── Session via banco de dados (compatível com serverless) ──
$pdo = \Core\Database::getInstance($config);
session_set_save_handler(
    new \Core\DbSessionHandler($pdo),
    true
);

// Cookie de sessão seguro
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', '1');
ini_set('session.gc_maxlifetime', '1800');

session_start();

// Regenera ID a cada 5 min para prevenir session fixation
if (!isset($_SESSION['_last_regeneration'])) {
    $_SESSION['_last_regeneration'] = time();
} elseif (time() - $_SESSION['_last_regeneration'] > 300) {
    session_regenerate_id(true);
    $_SESSION['_last_regeneration'] = time();
}

// ── CSRF token ──────────────────────────────────────────────
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ── Base path ───────────────────────────────────────────────
$basePath = '';
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rawurldecode($uri);
$uri = rtrim($uri, '/');

if ($uri === '' || $uri === '/') {
    $uri = '/inscricao';
}

// ── Rotas ───────────────────────────────────────────────────
$routes = [
    'GET /inscricao'                => ['Controllers\\AlunoController', 'create'],
    'POST /inscricao'               => ['Controllers\\AlunoController', 'store'],
    'GET /alunos'                   => ['Controllers\\AlunoController', 'index'],
    'GET /alunos/{id:\d+}'          => ['Controllers\\AlunoController', 'show'],
    'GET /alunos/{id:\d+}/edit'     => ['Controllers\\AlunoController', 'edit'],
    'POST /alunos/{id:\d+}'         => ['Controllers\\AlunoController', 'update'],
    'DELETE /alunos/{id:\d+}'       => ['Controllers\\AlunoController', 'destroy'],
    'GET /api/alunos/search'        => ['Controllers\\AlunoController', 'apiSearch'],

    'GET /cursos'                   => ['Controllers\\CursoController', 'index'],
    'GET /cursos/criar'             => ['Controllers\\CursoController', 'create'],
    'POST /cursos'                  => ['Controllers\\CursoController', 'store'],
    'GET /cursos/{id:\d+}'          => ['Controllers\\CursoController', 'edit'],
    'GET /cursos/{id:\d+}/editar'   => ['Controllers\\CursoController', 'edit'],
    'POST /cursos/{id:\d+}'         => ['Controllers\\CursoController', 'update'],
    'DELETE /cursos/{id:\d+}'       => ['Controllers\\CursoController', 'destroy'],

    'GET /admin'                    => ['Controllers\\AuthController', 'login'],
    'POST /admin'                   => ['Controllers\\AuthController', 'login'],
    'GET /logout'                   => ['Controllers\\AuthController', 'logout'],
];

// ── HTTP method override ────────────────────────────────────
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST' && isset($_POST['_method'])) {
    $method = strtoupper($_POST['_method']);
}

// ── Resolver rota ───────────────────────────────────────────
$matched = false;
foreach ($routes as $route => $handler) {
    [$routeMethod, $routePath] = explode(' ', $route, 2);
    if ($routeMethod !== $method) continue;

    $pattern = '@^' . preg_replace('/\{(\w+)(?::([^\}]+))?\}/', '(?<$1>$2)', $routePath) . '$@';

    if (preg_match($pattern, $uri, $matches)) {
        $matched = true;
        $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

        [$controllerClass, $action] = $handler;
        $controller = new $controllerClass();
        $controller->$action(...array_values($params));
        break;
    }
}

if (!$matched) {
    http_response_code(404);
    echo '<!DOCTYPE html><html><head><title>404</title></head><body><h1>Página não encontrada</h1><p>A rota <code>' . htmlspecialchars($uri) . '</code> não existe.</p></body></html>';
}
