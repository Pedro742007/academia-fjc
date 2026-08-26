<?php
// src/Views/cursos/index.php
$title = 'Gestão de Cursos';
$cursos = $cursos ?? [];

$cursoIcons = ['bi-music-note-beamed', 'bi-disc', 'bi-soundwave', 'bi-boombox', 'bi-mic', 'bi-headphones'];
?>

<div class="page-header mb-4">
    <div>
        <h2><i class="bi bi-book me-2"></i>Gestão de Cursos</h2>
        <p>Gerencie os cursos da Academia FJC</p>
    </div>
    <a href="<?= url('/cursos/criar') ?>" class="btn btn-primary btn-lg">
        <i class="bi bi-plus-circle me-1"></i> Novo Curso
    </a>
</div>

<?php if (!empty($cursos)): ?>
<div class="row g-4 fade-in-up">
    <?php foreach ($cursos as $i => $curso): ?>
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 lift d-flex flex-column">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon primary" style="width:56px;height:56px;font-size:1.7rem;">
                            <i class="bi <?= $cursoIcons[$i % count($cursoIcons)] ?>"></i>
                        </div>
                        <span class="badge bg-light"><?= $curso['total_alunos'] ?? 0 ?> alunos</span>
                    </div>
                    <h5 class="mb-1"><?= htmlspecialchars($curso['nome']) ?></h5>
                    <p class="text-muted small mb-3 flex-grow-1">
                        <?= htmlspecialchars($curso['descricao'] ?: 'Sem descrição disponível.') ?>
                    </p>
                    <div class="d-flex justify-content-between align-items-center border-top pt-3">
                        <div>
                            <small class="text-muted d-block">Mensalidade</small>
                            <strong class="text-primary"><?= money($curso['valor_mensalidade']) ?></strong>
                        </div>
                        <div class="btn-group" role="group">
                            <a href="<?= url('/cursos/' . $curso['id'] . '/editar') ?>" class="btn btn-outline-warning btn-sm" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-outline-danger btn-sm"
                                    data-confirm-delete="Excluir curso <?= htmlspecialchars($curso['nome']) ?>?"
                                    data-url="<?= url('/cursos/' . $curso['id']) ?>"
                                    data-method="DELETE"
                                    title="Excluir">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
<div class="card">
    <div class="card-body text-center py-5">
        <div class="empty-state">
            <i class="bi bi-book display-1 text-muted"></i>
            <p class="text-muted mt-3 mb-1">Nenhum curso cadastrado</p>
            <a href="<?= url('/cursos/criar') ?>" class="btn btn-primary mt-2">Cadastrar primeiro curso</a>
        </div>
    </div>
</div>
<?php endif; ?>
