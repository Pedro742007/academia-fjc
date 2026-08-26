<!DOCTYPE html>
<html lang="pt-AO">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title><?= $app_name ?? 'Academia FJC' ?> - <?= $title ?? 'Sistema de Gestão' ?></title>
    <link rel="icon" type="image/jpeg" href="<?= asset('img/logo.jpg') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= asset('css/app.css') ?>" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg mb-4">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="<?= url('/inscricao') ?>">
                <img class="brand-logo" src="<?= asset('img/logo.jpg') ?>" alt="Academia de Artes e Música FJC" width="34" height="50">
                <span>Academia FJC</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto ms-2">
                    <?php if (($_SESSION['usuario_nivel'] ?? '') === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/alunos') ?>"><i class="bi bi-people me-1"></i> Listar Alunos</a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/inscricao') ?>"><i class="bi bi-person-plus me-1"></i> Nova Inscrição</a>
                    </li>
                    <?php if (($_SESSION['usuario_nivel'] ?? '') === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/cursos') ?>"><i class="bi bi-book me-1"></i> Cursos</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <div class="d-flex align-items-center gap-2">
                <span class="navbar-text me-2 d-none d-md-inline">
                    <i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y') ?>
                </span>
                <?php if (isset($_SESSION['usuario_nome'])): ?>
                    <div class="dropdown">
                        <button class="btn btn-user btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['usuario_nome']) ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text text-muted small"><?= htmlspecialchars($_SESSION['usuario_nivel'] ?? '') ?></span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= url('/logout') ?>"><i class="bi bi-box-arrow-right me-1"></i> Sair</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <div class="container-fluid px-4 main-content">
        <?php if (isset($flash) || isset($_SESSION['flash'])): ?>
            <?php 
            $flashes = $_SESSION['flash'] ?? [];
            foreach ($flashes as $type => $message): 
                unset($_SESSION['flash'][$type]);
                $alertClass = $type === 'success' ? 'alert-success' : ($type === 'error' ? 'alert-danger' : 'alert-info');
            ?>
            <div class="alert <?= $alertClass ?> alert-dismissible fade show mb-3" role="alert">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </div>

    <footer class="footer-app">
        <div class="container-fluid px-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <small>&copy; <?= $current_year ?? date('Y') ?> <strong>Academia FJC</strong> — Sistema de Gestão de Alunos</small>
            <small><i class="bi bi-music-note-beamed me-1"></i>Academia de Artes · <?= date('Y') ?></small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('js/app.js') ?>"></script>
    <?= $scripts ?? '' ?>
</body>
</html>
