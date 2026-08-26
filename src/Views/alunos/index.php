<?php
// src/Views/alunos/index.php
$title = 'Lista de Alunos';

// Initialize variables
$cursos = $cursos ?? [];
$alunos = $alunos ?? [];
$stats = $stats ?? [];
$pagination = $pagination ?? [];
$search = $search ?? '';

$scripts = '
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Ordenação de tabela
    document.querySelectorAll("th[data-sort]").forEach(th => {
        th.style.cursor = "pointer";
        th.addEventListener("click", function() {
            const table = this.closest("table");
            const tbody = table.querySelector("tbody");
            const rows = Array.from(tbody.querySelectorAll("tr"));
            const index = Array.from(this.parentNode.children).indexOf(this);
            const isAsc = this.classList.toggle("sort-asc");
            
            rows.sort((a, b) => {
                const aVal = a.children[index].textContent.trim();
                const bVal = b.children[index].textContent.trim();
                return isAsc ? aVal.localeCompare(bVal, undefined, {numeric: true}) : bVal.localeCompare(aVal, undefined, {numeric: true});
            });
            
            rows.forEach(row => tbody.appendChild(row));
            
            // Atualizar ícones
            table.querySelectorAll("th i").forEach(i => i.className = "bi bi-arrow-down-up ms-1");
            this.querySelector("i").className = isAsc ? "bi bi-arrow-up ms-1" : "bi bi-arrow-down ms-1";
        });
    });
});
</script>
'?>

<div class="page-header mb-4">
    <div>
        <h2><i class="bi bi-people-fill me-2"></i>Alunos Cadastrados</h2>
        <p>Gerencie as inscrições da Academia FJC</p>
    </div>
    <a href="<?= url('/inscricao') ?>" class="btn btn-primary btn-lg">
        <i class="bi bi-person-plus-fill me-1"></i> Nova Inscrição
    </a>
</div>

<!-- Cards de Estatísticas -->
<div class="row g-3 mb-4 fade-in-up">
    <div class="col-md-3">
        <div class="stat-card primary lift">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p>Total de Alunos</p>
                    <h3><?= $stats['total_alunos'] ?? 0 ?></h3>
                </div>
                <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card success lift">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p>Novos este Mês</p>
                    <h3><?= $stats['novos_mes'] ?? 0 ?></h3>
                </div>
                <div class="stat-icon"><i class="bi bi-calendar-plus-fill"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card info lift">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p>Total Recebido</p>
                    <h3><?= money($stats['total_recebido'] ?? 0) ?></h3>
                </div>
                <div class="stat-icon"><i class="bi bi-currency-dollar"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card warning lift">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p>Valores Pendentes</p>
                    <h3><?= money($stats['total_pendente'] ?? 0) ?></h3>
                </div>
                <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Busca e Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label">Buscar</label>
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" class="form-control" placeholder="Nome, documento, responsável..." value="<?= htmlspecialchars($search ?? '') ?>">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label">Curso</label>
                <select name="curso_id" class="form-select">
                    <option value="">Todos os cursos</option>
                    <?php foreach ($cursos as $curso): ?>
                        <option value="<?= $curso['id'] ?>"><?= htmlspecialchars($curso['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-search me-1"></i> Filtrar</button>
                <?php if ($search): ?>
                    <a href="<?= url('/alunos') ?>" class="btn btn-outline-secondary"><i class="bi bi-x-circle me-1"></i> Limpar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Tabela de Alunos -->
<div class="card lift">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-table me-2 text-primary"></i>Lista de Alunos</h5>
        <span class="badge bg-light"><?= $pagination['total'] ?? 0 ?> alunos</span>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th data-sort>Nº Aluno <i class="bi bi-arrow-down-up ms-1"></i></th>
                    <th data-sort>Nome <i class="bi bi-arrow-down-up ms-1"></i></th>
                    <th>Documento</th>
                    <th data-sort>Curso <i class="bi bi-arrow-down-up ms-1"></i></th>
                    <th>Inscrição</th>
                    <th>Status Pagamento</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($alunos)): ?>
                    <?php foreach ($alunos as $aluno): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($aluno['numero_aluno']) ?></strong></td>
                            <td>
                                <div class="fw-medium"><?= htmlspecialchars($aluno['nome_completo']) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($aluno['responsavel1_nome'] ?? '') ?></small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark"><?= htmlspecialchars($aluno['tipo_documento']) ?></span>
                                <?= htmlspecialchars($aluno['numero_documento']) ?>
                            </td>
                            <td>
                                <?php if ($aluno['curso_nome']): ?>
                                    <span class="badge bg-primary bg-opacity-10 text-primary"><?= htmlspecialchars($aluno['curso_nome']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">Não informado</span>
                                <?php endif; ?>
                            </td>
                            <td><?= formatDateBR($aluno['data_inscricao']) ?></td>
                            <td>
                                <?php 
                                $pendente = (float)($aluno['valor_pendente'] ?? 0);
                                $pago = (float)($aluno['valor_total_pago'] ?? 0);
                                $inscricao = (float)($aluno['valor_inscricao'] ?? 0);
                                
                                if ($pendente <= 0 && $pago >= $inscricao && $inscricao > 0): ?>
                                    <span class="badge bg-paid"><i class="bi bi-check-circle-fill me-1"></i> Quitado</span>
                                <?php elseif ($pago > 0 && $pendente > 0): ?>
                                    <span class="badge bg-partial"><i class="bi bi-hourglass-split me-1"></i> Parcial</span>
                                <?php else: ?>
                                    <span class="badge bg-pending"><i class="bi bi-exclamation-triangle-fill me-1"></i> Pendente</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="<?= url('/alunos/' . $aluno['id']) ?>" class="btn btn-outline-primary" title="Ver detalhes" data-bs-toggle="tooltip">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= url('/alunos/' . $aluno['id'] . '/edit') ?>" class="btn btn-outline-warning" title="Editar" data-bs-toggle="tooltip">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" 
                                            data-confirm-delete="Excluir aluno <?= htmlspecialchars($aluno['nome_completo']) ?>?"
                                            data-url="<?= url('/alunos/' . $aluno['id']) ?>"
                                            data-method="DELETE"
                                            title="Excluir" data-bs-toggle="tooltip">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="empty-state">
                                <i class="bi bi-people display-1 text-muted"></i>
                                <p class="text-muted mt-3 mb-1">Nenhum aluno encontrado</p>
                                <a href="<?= url('/inscricao') ?>" class="btn btn-primary mt-2">Cadastrar primeiro aluno</a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <?php if (($pagination['last_page'] ?? 1) > 1): ?>
        <div class="card-footer">
            <nav aria-label="Paginação">
                <ul class="pagination justify-content-center mb-0">
                    <?php 
                    $page = $pagination['current_page'] ?? 1;
                    $lastPage = $pagination['last_page'] ?? 1;
                    $searchParam = $search ? '&search=' . urlencode($search) : '';
                    ?>
                    
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page - 1 ?><?= $searchParam ?>"><i class="bi bi-chevron-left"></i></a>
                    </li>
                    
                    <?php 
                    $start = max(1, $page - 2);
                    $end = min($lastPage, $page + 2);
                    
                    if ($start > 1) {
                        echo '<li class="page-item"><a class="page-link" href="?page=1' . $searchParam . '">1</a></li>';
                        if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                    
                    for ($i = $start; $i <= $end; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?><?= $searchParam ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($end < $lastPage) {
                        if ($end < $lastPage - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        echo '<li class="page-item"><a class="page-link" href="?page=' . $lastPage . $searchParam . '">' . $lastPage . '</a></li>';
                    } ?>
                    
                    <li class="page-item <?= $page >= $lastPage ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page + 1 ?><?= $searchParam ?>"><i class="bi bi-chevron-right"></i></a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>