<?php
// src/Views/alunos/show.php
$title = 'Detalhes: ' . htmlspecialchars($aluno['nome_completo']);
$scripts = '
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Botão imprimir ficha
    document.querySelector("[data-print-ficha]")?.addEventListener("click", function() {
        printFicha();
    });
});

function printFicha() {
    const printWindow = window.open("", "_blank");
    const aluno = ' . json_encode($aluno, JSON_UNESCAPED_UNICODE) . ';
    const logoUrl = ' . json_encode(asset('img/logo.jpg')) . ';
    
    const html = `
        <!DOCTYPE html>
        <html lang="pt-AO">
        <head>
            <meta charset="UTF-8">
            <title>Ficha de Inscrição - ${aluno.numero_aluno}</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                @media print {
                    .no-print { display: none !important; }
                    body { padding: 20px; font-size: 12px; }
                    .card { border: 1px solid #ddd; break-inside: avoid; }
                    .card-header { background: #2c3e50 !important; -webkit-print-color-adjust: exact; }
                }
                .detail-row { display: flex; padding: 6px 0; border-bottom: 1px solid #eee; }
                .detail-label { width: 220px; font-weight: 600; color: #333; flex-shrink: 0; font-size: 11px; }
                .detail-value { flex: 1; font-size: 11px; }
                .print-logo { width: 110px; height: auto; margin-bottom: 8px; }
                .section-title { background: #2c3e50; color: white; padding: 8px 12px; margin: 15px -15px 10px -15px; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="text-center mb-4">
                <img class="print-logo" src="${logoUrl}" alt="Academia de Artes e Música FJC">
                <h3 class="mb-1">ACADEMIA DE ARTES</h3>
                <h4 class="mb-0">FICHA DE INSCRIÇÃO DO ALUNO</h4>
                <hr>
                <div class="row">
                    <div class="col-6 text-start"><strong>N.º do Aluno:</strong> ${aluno.numero_aluno}</div>
                    <div class="col-6 text-end"><strong>Data:</strong> ${new Date().toLocaleDateString("pt-AO")}</div>
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-header text-white">DADOS DO ALUNO</div>
                <div class="card-body p-3">
                    <div class="detail-row"><span class="detail-label">Nome Completo:</span><span class="detail-value">${aluno.nome_completo}</span></div>
                    <div class="detail-row"><span class="detail-label">Data de Nascimento:</span><span class="detail-value">${aluno.data_nascimento ? new Date(aluno.data_nascimento + "T00:00:00").toLocaleDateString("pt-AO") : ""}</span></div>
                    <div class="detail-row"><span class="detail-label">Idade:</span><span class="detail-value">${aluno.idade} anos</span></div>
                    <div class="detail-row"><span class="detail-label">Documento:</span><span class="detail-value">${aluno.tipo_documento} - ${aluno.numero_documento}</span></div>
                    <div class="detail-row"><span class="detail-label">Morada:</span><span class="detail-value">${aluno.morada || "Não informada"}</span></div>
                    <div class="detail-row"><span class="detail-label">Irmão(ã) matriculado:</span><span class="detail-value">${aluno.possui_irmao ? "Sim - " + (aluno.nome_irmao || "") : "Não"}</span></div>
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-header text-white">RESPONSÁVEIS LEGAIS</div>
                <div class="card-body p-3">
                    <div class="detail-row"><span class="detail-label">1º Responsável:</span><span class="detail-value">${aluno.responsavel1_nome || "Não informado"}</span></div>
                    <div class="detail-row"><span class="detail-label">Parentesco:</span><span class="detail-value">${aluno.responsavel1_parentesco || ""}</span></div>
                    <div class="detail-row"><span class="detail-label">Contacto:</span><span class="detail-value">${aluno.responsavel1_contacto || ""}</span></div>
                    <div class="detail-row"><span class="detail-label">2º Responsável:</span><span class="detail-value">${aluno.responsavel2_nome || "Não informado"}</span></div>
                    <div class="detail-row"><span class="detail-label">Parentesco:</span><span class="detail-value">${aluno.responsavel2_parentesco || ""}</span></div>
                    <div class="detail-row"><span class="detail-label">Contacto:</span><span class="detail-value">${aluno.responsavel2_contacto || ""}</span></div>
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-header text-white">CONTACTO PARA RECADOS OU EMERGÊNCIAS</div>
                <div class="card-body p-3">
                    <div class="detail-row"><span class="detail-label">Nome:</span><span class="detail-value">${aluno.emergencia_nome || ""}</span></div>
                    <div class="detail-row"><span class="detail-label">Telefone:</span><span class="detail-value">${aluno.emergencia_telefone || ""}</span></div>
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-header text-white">DADOS DA INSCRIÇÃO</div>
                <div class="card-body p-3">
                    <div class="detail-row"><span class="detail-label">Curso:</span><span class="detail-value">${aluno.curso_nome || "Não informado"}</span></div>
                    <div class="detail-row"><span class="detail-label">Data da Inscrição:</span><span class="detail-value">${aluno.data_inscricao ? new Date(aluno.data_inscricao + "T00:00:00").toLocaleDateString("pt-AO") : ""}</span></div>
                    <div class="detail-row"><span class="detail-label">Valor da Inscrição:</span><span class="detail-value">${Number(aluno.valor_inscricao || 0).toLocaleString("pt-AO", {minimumFractionDigits: 2})} Kz</span></div>
                    <div class="detail-row"><span class="detail-label">Valor Entregue:</span><span class="detail-value">${Number(aluno.valor_entregue || 0).toLocaleString("pt-AO", {minimumFractionDigits: 2})} Kz</span></div>
                    <div class="detail-row"><span class="detail-label">Valor Pago:</span><span class="detail-value">${Number(aluno.valor_total_pago || 0).toLocaleString("pt-AO", {minimumFractionDigits: 2})} Kz</span></div>
                    <div class="detail-row"><span class="detail-label">Valor Pendente:</span><span class="detail-value">${Number(aluno.valor_pendente || 0).toLocaleString("pt-AO", {minimumFractionDigits: 2})} Kz</span></div>
                </div>
            </div>
            
            ${aluno.observacoes ? `
            <div class="card mb-3">
                <div class="card-header text-white">OBSERVAÇÕES</div>
                <div class="card-body p-3">
                    <pre style="white-space: pre-wrap; margin: 0; font-family: inherit; font-size: 11px;">${aluno.observacoes}</pre>
                </div>
            </div>` : ""}
            
            <div class="no-print text-center mt-4">
                <button onclick="window.print()" class="btn btn-primary">Imprimir / Salvar PDF</button>
                <button onclick="window.close()" class="btn btn-secondary ms-2">Fechar</button>
            </div>
        </body>
        </html>
    `;
    
    printWindow.document.write(html);
    printWindow.document.close();
}
</script>
'?>

<div class="page-header mb-4">
    <div>
        <h2><i class="bi bi-person-circle me-2"></i><?= htmlspecialchars($aluno['nome_completo']) ?></h2>
        <p><?= htmlspecialchars($aluno['numero_aluno']) ?> • <?= htmlspecialchars($aluno['curso_nome'] ?? 'Sem curso') ?></p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <button class="btn btn-light" data-print-ficha><i class="bi bi-printer-fill me-1"></i> Imprimir Ficha</button>
        <a href="<?= url('/alunos/' . $aluno['id'] . '/edit') ?>" class="btn btn-outline-light"><i class="bi bi-pencil me-1"></i> Editar</a>
        <a href="<?= url('/alunos') ?>" class="btn btn-outline-light"><i class="bi bi-arrow-left me-1"></i> Voltar</a>
    </div>
</div>

<div class="row g-4">
    <!-- Coluna Principal -->
    <div class="col-lg-8">
        <!-- Identificação -->
        <div class="card mb-4 lift">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-card-text me-2"></i>Identificação</h5>
                <span class="badge bg-primary"><?= htmlspecialchars($aluno['numero_aluno']) ?></span>
            </div>
            <div class="card-body">
                <div class="detail-row">
                    <span class="detail-label">Tipo de Documento</span>
                    <span class="detail-value">
                        <span class="badge bg-light text-dark"><?= htmlspecialchars($aluno['tipo_documento']) ?></span>
                        <?= htmlspecialchars($aluno['numero_documento']) ?>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Data de Nascimento</span>
                    <span class="detail-value"><?= formatDateBR($aluno['data_nascimento']) ?> (<?= $aluno['idade'] ?> anos)</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Morada</span>
                    <span class="detail-value"><?= nl2br(htmlspecialchars($aluno['morada'] ?? 'Não informada')) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Irmão(ã) matriculado(a)</span>
                    <span class="detail-value">
                        <?php if ($aluno['possui_irmao']): ?>
                            <span class="badge bg-primary">Sim</span> - <?= htmlspecialchars($aluno['nome_irmao'] ?? '') ?>
                        <?php else: ?>
                            <span class="badge bg-secondary">Não</span>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Cadastro</span>
                    <span class="detail-value"><?= formatDateTimeBR($aluno['created_at']) ?></span>
                </div>
            </div>
        </div>

        <!-- Responsáveis -->
        <div class="card mb-4 lift">
            <div class="card-header"><i class="bi bi-shield-check me-2"></i>Responsáveis Legais</div>
            <div class="card-body">
                <h6 class="text-primary mb-3"><i class="bi bi-person-badge me-1"></i>1º Responsável</h6>
                <div class="detail-row"><span class="detail-label">Nome</span><span class="detail-value"><?= htmlspecialchars($aluno['responsavel1_nome'] ?? 'Não informado') ?></span></div>
                <div class="detail-row"><span class="detail-label">Grau de Parentesco</span><span class="detail-value"><?= htmlspecialchars($aluno['responsavel1_parentesco'] ?? '') ?></span></div>
                <div class="detail-row"><span class="detail-label">Contacto</span><span class="detail-value"><?= htmlspecialchars($aluno['responsavel1_contacto'] ?? '') ?></span></div>

                <hr class="my-3">
                
                <h6 class="text-primary mb-3"><i class="bi bi-person-badge me-1"></i>2º Responsável</h6>
                <div class="detail-row"><span class="detail-label">Nome</span><span class="detail-value"><?= htmlspecialchars($aluno['responsavel2_nome'] ?? 'Não informado') ?></span></div>
                <div class="detail-row"><span class="detail-label">Grau de Parentesco</span><span class="detail-value"><?= htmlspecialchars($aluno['responsavel2_parentesco'] ?? '') ?></span></div>
                <div class="detail-row"><span class="detail-label">Contacto</span><span class="detail-value"><?= htmlspecialchars($aluno['responsavel2_contacto'] ?? '') ?></span></div>
            </div>
        </div>

        <!-- Emergência -->
        <div class="card mb-4 lift">
            <div class="card-header"><i class="bi bi-telephone-inbound me-2"></i>Contacto de Emergência</div>
            <div class="card-body">
                <div class="detail-row"><span class="detail-label">Nome</span><span class="detail-value"><?= htmlspecialchars($aluno['emergencia_nome'] ?? '') ?></span></div>
                <div class="detail-row"><span class="detail-label">Telefone</span><span class="detail-value"><?= htmlspecialchars($aluno['emergencia_telefone'] ?? '') ?></span></div>
            </div>
        </div>

        <!-- Inscrição -->
        <div class="card mb-4 lift">
            <div class="card-header"><i class="bi bi-journal-plus me-2"></i>Dados da Inscrição</div>
            <div class="card-body">
                <div class="detail-row"><span class="detail-label">Curso</span><span class="detail-value"><span class="badge bg-primary bg-opacity-10 text-primary"><?= htmlspecialchars($aluno['curso_nome'] ?? 'Não informado') ?></span></span></div>
                <div class="detail-row"><span class="detail-label">Data da Inscrição</span><span class="detail-value"><?= formatDateBR($aluno['data_inscricao']) ?></span></div>
                <div class="detail-row"><span class="detail-label">Valor da Inscrição</span><span class="detail-value fw-bold"><?= money($aluno['valor_inscricao']) ?></span></div>
                <div class="detail-row"><span class="detail-label">Valor Entregue</span><span class="detail-value"><?= money($aluno['valor_entregue']) ?></span></div>
                <div class="detail-row"><span class="detail-label">Valor Pago Anteriormente</span><span class="detail-value"><?= money($aluno['valor_total_pago']) ?></span></div>
                <div class="detail-row">
                    <span class="detail-label">Valor Pendente</span>
                    <span class="detail-value fw-bold text-<?= ($aluno['valor_pendente'] > 0) ? 'danger' : 'success' ?>">
                        <?= money($aluno['valor_pendente']) ?>
                        <?= ($aluno['valor_pendente'] > 0) ? '<span class="badge bg-pending ms-2">Pendente</span>' : '<span class="badge bg-paid ms-2">Quitado</span>' ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Observações -->
        <?php if (!empty($aluno['observacoes'])): ?>
        <div class="card mb-4 lift">
            <div class="card-header"><i class="bi bi-file-text me-2"></i>Observações</div>
            <div class="card-body">
                <pre style="white-space: pre-wrap; margin: 0; font-family: inherit;"><?= htmlspecialchars($aluno['observacoes']) ?></pre>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Status Card -->
        <div class="card mb-4 lift">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>Status</div>
            <div class="card-body">
                <div class="detail-row"><span class="detail-label">Situação</span><span class="detail-value"><span class="badge bg-primary">Ativo</span></span></div>
                <div class="detail-row"><span class="detail-label">Curso</span><span class="detail-value"><?= htmlspecialchars($aluno['curso_nome'] ?? 'Não matriculado') ?></span></div>
                <div class="detail-row"><span class="detail-label">Mensalidade</span><span class="detail-value"><?= money($aluno['valor_mensalidade'] ?? 0) ?></span></div>
                <div class="detail-row"><span class="detail-label">Última Atualização</span><span class="detail-value"><?= formatDateTimeBR($aluno['updated_at']) ?></span></div>
            </div>
        </div>

        <!-- Resumo Financeiro -->
        <div class="card mb-4 lift">
            <div class="card-header"><i class="bi bi-cash-stack me-2"></i>Resumo Financeiro</div>
            <div class="card-body">
                <div class="detail-row"><span class="detail-label">Total Inscrição</span><span class="detail-value fw-bold"><?= money($aluno['valor_inscricao']) ?></span></div>
                <div class="detail-row"><span class="detail-label">Total Pago</span><span class="detail-value text-primary fw-bold"><?= money(($aluno['valor_entregue'] + $aluno['valor_total_pago'])) ?></span></div>
                <div class="detail-row"><span class="detail-label">Saldo Devedor</span><span class="detail-value fw-bold text-<?= ($aluno['valor_pendente'] > 0) ? 'danger' : 'success' ?>"><?= money($aluno['valor_pendente']) ?></span></div>
                
                <hr>
                
                <div class="progress mb-2" style="height: 8px;">
                    <?php 
                    $total = $aluno['valor_inscricao'];
                    $pago = $aluno['valor_entregue'] + $aluno['valor_total_pago'];
                    $percent = $total > 0 ? min(100, ($pago / $total) * 100) : 0;
                    ?>
                    <div class="progress-bar bg-<?= $percent >= 100 ? 'success' : 'warning' ?>" style="width: <?= $percent ?>%"></div>
                </div>
                <small class="text-muted"><?= number_format($percent, 1) ?>% pago</small>
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="card mb-4 lift">
            <div class="card-header"><i class="bi bi-lightning me-2"></i>Ações Rápidas</div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= url('/alunos/' . $aluno['id'] . '/edit') ?>" class="btn btn-outline-primary"><i class="bi bi-pencil me-2"></i>Editar Inscrição</a>
                    <button class="btn btn-outline-success" data-print-ficha><i class="bi bi-printer me-2"></i>Imprimir Ficha Completa</button>
                    <a href="tel:<?= preg_replace('/\D/', '', $aluno['responsavel1_contacto'] ?? '') ?>" class="btn btn-outline-info"><i class="bi bi-telephone me-2"></i>Ligar para Responsável 1</a>
                    <a href="tel:<?= preg_replace('/\D/', '', $aluno['emergencia_telefone'] ?? '') ?>" class="btn btn-outline-secondary"><i class="bi bi-telephone-plus me-2"></i>Ligar para Emergência</a>
                </div>
            </div>
        </div>
    </div>
</div>
