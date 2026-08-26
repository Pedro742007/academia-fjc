<!DOCTYPE html>
<html lang="pt-AO">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title><?= $app_name ?? 'Academia FJC' ?> - <?= $title ?? 'Login' ?></title>
    <link rel="icon" type="image/jpeg" href="<?= asset('img/logo.jpg') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= asset('css/app.css') ?>" rel="stylesheet">
</head>
<body>
    <div class="auth-page">
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100 py-5">
                <div class="col-md-6 col-lg-5">
                    <div class="auth-brand">
                        <img class="auth-logo" src="<?= asset('img/logo.jpg') ?>" alt="Academia de Artes e Música FJC" width="180" height="264">
                        <h1>Academia FJC</h1>
                        <p>Sistema de Gestão de Alunos · Academia de Artes</p>
                    </div>

                    <div class="auth-card">
                        <div class="p-4 p-md-5">
                            <?php if (isset($_SESSION['flash'])): ?>
                                <?php 
                                $flashes = $_SESSION['flash'] ?? [];
                                foreach ($flashes as $type => $message): 
                                    unset($_SESSION['flash'][$type]);
                                    $alertClass = $type === 'success' ? 'alert-success' : ($type === 'error' ? 'alert-danger' : 'alert-info');
                                ?>
                                <div class="alert <?= $alertClass ?> alert-dismissible fade show" role="alert">
                                    <?= htmlspecialchars($message) ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <?= $content ?? '' ?>
                        </div>
                    </div>

                    <div class="text-center mt-4 position-relative" style="z-index: 1;">
                        <small class="text-muted">&copy; <?= date('Y') ?> Academia FJC</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('js/app.js') ?>"></script>
    <?= $scripts ?? '' ?>
</body>
</html>
