<?php
// src/Views/cursos/create.php
$title = 'Novo Curso';
?>

<div class="page-header mb-4">
    <div>
        <h2><i class="bi bi-plus-circle me-2"></i>Novo Curso</h2>
        <p>Cadastrar um novo curso na Academia FJC</p>
    </div>
    <a href="<?= url('/cursos') ?>" class="btn btn-light btn-lg"><i class="bi bi-arrow-left me-1"></i> Voltar</a>
</div>

<div class="row justify-content-center fade-in-up">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-music-note-list me-2 text-primary"></i>Dados do Curso</div>
            <div class="card-body">
                <?php if (!empty($_SESSION['errors'])): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($_SESSION['errors'] as $field => $msg): ?>
                                <li><?= htmlspecialchars($msg) ?></li>
                            <?php endforeach; ?>
                            <?php unset($_SESSION['errors']); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= url('/cursos') ?>" class="needs-validation" novalidate>
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nome do Curso <span class="text-danger">*</span></label>
                        <input type="text" name="nome" class="form-control form-control-lg" value="<?= htmlspecialchars($_SESSION['old']['nome'] ?? '') ?>" required minlength="3" maxlength="150" placeholder="Ex: Guitarra, Piano, Canto...">
                        <div class="invalid-feedback">Informe o nome do curso (mín. 3 caracteres)</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Descrição</label>
                        <textarea name="descricao" class="form-control" rows="3" placeholder="Descrição detalhada do curso..."><?= htmlspecialchars($_SESSION['old']['descricao'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-medium">Valor da Mensalidade (Kz) <span class="text-danger">*</span></label>
                        <input type="text" name="valor_mensalidade" class="form-control form-control-lg text-end" value="<?= htmlspecialchars($_SESSION['old']['valor_mensalidade'] ?? '') ?>" required data-mask="moeda" placeholder="0,00">
                        <div class="invalid-feedback">Informe o valor da mensalidade</div>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= url('/cursos') ?>" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-circle-fill me-1"></i> Salvar Curso</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
