<?php
// src/Views/auth/login.php
$title = 'Entrar';
$errors = $errors ?? [];
$old_email = $old_email ?? '';
?>

<div>
    <h3 class="text-center mb-1"><i class="bi bi-box-arrow-in-right me-2 text-primary"></i>Entrar</h3>
    <p class="text-center text-muted mb-4">Acesso restrito à equipa da Academia FJC</p>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

        <form method="POST" action="<?= url('/admin') ?>" novalidate>
        <?= csrf_field() ?>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" class="form-control form-control-lg"
                       value="<?= htmlspecialchars($old_email) ?>" required autofocus placeholder="seu@email.com">
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Senha</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" name="password" class="form-control form-control-lg" required placeholder="••••••••">
            </div>
        </div>

        <div class="form-check mb-4">
            <input type="checkbox" name="remember" id="remember" class="form-check-input">
            <label class="form-check-label" for="remember">Manter sessão aberta</label>
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100">
            <i class="bi bi-box-arrow-in-right me-1"></i> Entrar
        </button>
    </form>

    <hr class="my-4">
    <p class="text-center text-muted small mb-0">
            <a href="<?= url('/inscricao') ?>" class="text-decoration-none"><i class="bi bi-person-plus me-1"></i>Fazer inscrição pública</a>
    </p>
</div>
