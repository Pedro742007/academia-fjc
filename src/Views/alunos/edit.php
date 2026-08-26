<?php
// src/Views/alunos/edit.php
$title = 'Editar Aluno: ' . htmlspecialchars($aluno['nome_completo']);
$scripts = '
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelector("input[name=\'nome_completo\']")?.focus();
});
</script>
'?>

<div class="page-header mb-4">
    <div>
        <h2><i class="bi bi-pencil-fill me-2"></i>Editar Inscrição</h2>
        <p>Aluno: <strong><?= htmlspecialchars($aluno['nome_completo']) ?></strong> (<?= htmlspecialchars($aluno['numero_aluno']) ?>)</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('/alunos/' . $aluno['id']) ?>" class="btn btn-light"><i class="bi bi-eye me-1"></i> Ver</a>
        <a href="<?= url('/alunos') ?>" class="btn btn-outline-light"><i class="bi bi-arrow-left me-1"></i> Voltar</a>
    </div>
</div>

<form method="POST" action="<?= url('/alunos/' . $aluno['id']) ?>" class="needs-validation" novalidate>
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="PUT">
    
    <!-- Número do Aluno (readonly) -->
    <div class="card mb-4">
        <div class="card-header"><i class="bi bi-card-text me-2"></i>Identificação do Aluno</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Nº do Aluno</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($aluno['numero_aluno']) ?>" readonly>
                    <input type="hidden" name="numero_aluno" value="<?= htmlspecialchars($aluno['numero_aluno']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tipo de Documento <span class="text-danger">*</span></label>
                    <select name="tipo_documento" class="form-select" required>
                        <option value="BI" <?= $aluno['tipo_documento'] === 'BI' ? 'selected' : '' ?>>B.I. (Bilhete de Identidade)</option>
                        <option value="Cedula" <?= $aluno['tipo_documento'] === 'Cedula' ? 'selected' : '' ?>>Cédula de Identidade</option>
                        <option value="Passaporte" <?= $aluno['tipo_documento'] === 'Passaporte' ? 'selected' : '' ?>>Passaporte</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nº do Documento <span class="text-danger">*</span></label>
                    <input type="text" name="numero_documento" class="form-control" value="<?= htmlspecialchars($aluno['numero_documento']) ?>" required maxlength="50">
                </div>
            </div>
        </div>
    </div>

    <!-- Dados do Aluno -->
    <div class="form-section"><i class="bi bi-person-circle me-2"></i><h5 class="mb-0">DADOS DO ALUNO</h5></div>
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nome Completo <span class="text-danger">*</span></label>
                    <input type="text" name="nome_completo" class="form-control" value="<?= htmlspecialchars($aluno['nome_completo']) ?>" required minlength="3" maxlength="200" data-mask="alpha">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Data de Nascimento <span class="text-danger">*</span></label>
                    <input type="date" name="data_nascimento" class="form-control" value="<?= htmlspecialchars($aluno['data_nascimento']) ?>" required max="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Idade</label>
                    <input type="number" name="idade" class="form-control" value="<?= htmlspecialchars($aluno['idade']) ?>" readonly min="0" max="120">
                </div>
                <div class="col-12">
                    <label class="form-label">Morada</label>
                    <textarea name="morada" class="form-control" rows="2"><?= htmlspecialchars($aluno['morada'] ?? '') ?></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Província</label>
                    <input type="text" name="provincia" class="form-control" value="<?= htmlspecialchars($aluno['provincia'] ?? 'Luanda') ?>" readonly>
                    <div class="form-text">Luanda — única província disponível</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Município <span class="text-danger">*</span></label>
                    <input type="text" name="municipio" class="form-control" value="<?= htmlspecialchars($aluno['municipio'] ?? '') ?>" required maxlength="100" placeholder="Ex: Luanda, Viana, Cacuaco...">
                    <div class="invalid-feedback">Informe o município</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Bairro/Comuna <span class="text-danger">*</span></label>
                    <input type="text" name="bairro" class="form-control" value="<?= htmlspecialchars($aluno['bairro'] ?? '') ?>" required maxlength="100" placeholder="Ex: Ingombota, Cazenga, Kilamba...">
                    <div class="invalid-feedback">Informe o bairro/comuna</div>
                </div>
            </div>
            
            <hr class="my-3">
            
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="possui_irmao" id="possui_irmao" value="1" <?= $aluno['possui_irmao'] ? 'checked' : '' ?>>
                        <label class="form-check-label fw-medium" for="possui_irmao">Possui irmão(ã) matriculado(a) na Academia?</label>
                    </div>
                </div>
                <div class="col-md-6" id="irmao_field">
                    <label class="form-label">Nome do Irmão(ã)</label>
                    <input type="text" name="nome_irmao" class="form-control" value="<?= htmlspecialchars($aluno['nome_irmao'] ?? '') ?>" placeholder="Nome do irmão matriculado" data-mask="alpha" <?= !$aluno['possui_irmao'] ? 'disabled' : '' ?>>
                </div>
            </div>
        </div>
    </div>

    <!-- Responsáveis Legais -->
    <div class="form-section"><i class="bi bi-shield-check me-2"></i><h5 class="mb-0">RESPONSÁVEIS LEGAIS</h5></div>
    <div class="card mb-4">
        <div class="card-body">
            <h6 class="text-primary mb-3"><i class="bi bi-person-badge me-1"></i>1º Responsável</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nome <span class="text-danger">*</span></label>
                    <input type="text" name="responsavel1_nome" class="form-control" value="<?= htmlspecialchars($aluno['responsavel1_nome'] ?? '') ?>" required maxlength="200" data-mask="alpha">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Grau de Parentesco <span class="text-danger">*</span></label>
                    <select name="responsavel1_parentesco" class="form-select" required>
                        <option value="">Selecione...</option>
                        <option value="Pai" <?= ($aluno['responsavel1_parentesco'] ?? '') === 'Pai' ? 'selected' : '' ?>>Pai</option>
                        <option value="Mãe" <?= ($aluno['responsavel1_parentesco'] ?? '') === 'Mãe' ? 'selected' : '' ?>>Mãe</option>
                        <option value="Responsável Legal" <?= ($aluno['responsavel1_parentesco'] ?? '') === 'Responsável Legal' ? 'selected' : '' ?>>Responsável Legal</option>
                        <option value="Avô" <?= ($aluno['responsavel1_parentesco'] ?? '') === 'Avô' ? 'selected' : '' ?>>Avô</option>
                        <option value="Avó" <?= ($aluno['responsavel1_parentesco'] ?? '') === 'Avó' ? 'selected' : '' ?>>Avó</option>
                        <option value="Tio" <?= ($aluno['responsavel1_parentesco'] ?? '') === 'Tio' ? 'selected' : '' ?>>Tio</option>
                        <option value="Tia" <?= ($aluno['responsavel1_parentesco'] ?? '') === 'Tia' ? 'selected' : '' ?>>Tia</option>
                        <option value="Outro" <?= ($aluno['responsavel1_parentesco'] ?? '') === 'Outro' ? 'selected' : '' ?>>Outro</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Contacto <span class="text-danger">*</span></label>
                    <input type="tel" name="responsavel1_contacto" class="form-control" value="<?= htmlspecialchars($aluno['responsavel1_contacto'] ?? '') ?>" required maxlength="50" data-mask="telefone">
                </div>
            </div>

            <hr class="my-3">

            <h6 class="text-primary mb-3"><i class="bi bi-person-badge me-1"></i>2º Responsável (Opcional)</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nome</label>
                    <input type="text" name="responsavel2_nome" class="form-control" value="<?= htmlspecialchars($aluno['responsavel2_nome'] ?? '') ?>" maxlength="200" data-mask="alpha">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Grau de Parentesco</label>
                    <select name="responsavel2_parentesco" class="form-select">
                        <option value="">Selecione...</option>
                        <option value="Pai" <?= ($aluno['responsavel2_parentesco'] ?? '') === 'Pai' ? 'selected' : '' ?>>Pai</option>
                        <option value="Mãe" <?= ($aluno['responsavel2_parentesco'] ?? '') === 'Mãe' ? 'selected' : '' ?>>Mãe</option>
                        <option value="Responsável Legal" <?= ($aluno['responsavel2_parentesco'] ?? '') === 'Responsável Legal' ? 'selected' : '' ?>>Responsável Legal</option>
                        <option value="Avô" <?= ($aluno['responsavel2_parentesco'] ?? '') === 'Avô' ? 'selected' : '' ?>>Avô</option>
                        <option value="Avó" <?= ($aluno['responsavel2_parentesco'] ?? '') === 'Avó' ? 'selected' : '' ?>>Avó</option>
                        <option value="Tio" <?= ($aluno['responsavel2_parentesco'] ?? '') === 'Tio' ? 'selected' : '' ?>>Tio</option>
                        <option value="Tia" <?= ($aluno['responsavel2_parentesco'] ?? '') === 'Tia' ? 'selected' : '' ?>>Tia</option>
                        <option value="Outro" <?= ($aluno['responsavel2_parentesco'] ?? '') === 'Outro' ? 'selected' : '' ?>>Outro</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Contacto</label>
                    <input type="tel" name="responsavel2_contacto" class="form-control" value="<?= htmlspecialchars($aluno['responsavel2_contacto'] ?? '') ?>" maxlength="50" data-mask="telefone">
                </div>
            </div>
        </div>
    </div>

    <!-- Contacto de Emergência -->
    <div class="form-section"><i class="bi bi-telephone-inbound me-2"></i><h5 class="mb-0">CONTACTO PARA RECADOS OU EMERGÊNCIAS</h5></div>
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nome <span class="text-danger">*</span></label>
                    <input type="text" name="emergencia_nome" class="form-control" value="<?= htmlspecialchars($aluno['emergencia_nome'] ?? '') ?>" required maxlength="200" data-mask="alpha">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Telefone <span class="text-danger">*</span></label>
                    <input type="tel" name="emergencia_telefone" class="form-control" value="<?= htmlspecialchars($aluno['emergencia_telefone'] ?? '') ?>" required maxlength="50" data-mask="telefone">
                </div>
            </div>
        </div>
    </div>

    <!-- Dados da Inscrição -->
    <div class="form-section"><i class="bi bi-journal-plus me-2"></i><h5 class="mb-0">DADOS DA INSCRIÇÃO</h5></div>
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Curso Inscrito <span class="text-danger">*</span></label>
                    <select name="curso_id" class="form-select" required>
                        <option value="">Selecione o curso...</option>
                        <?php foreach ($cursos as $curso): ?>
                            <option value="<?= $curso['id'] ?>" data-valor="<?= $curso['valor_mensalidade'] ?>" <?= $aluno['curso_id'] == $curso['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($curso['nome']) ?> - <?= number_format($curso['valor_mensalidade'], 2, ',', '.') ?> Kz/mês
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Data da Inscrição <span class="text-danger">*</span></label>
                    <input type="date" name="data_inscricao" class="form-control" value="<?= htmlspecialchars($aluno['data_inscricao']) ?>" required max="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Valor da Inscrição <span class="text-danger">*</span></label>
                    <input type="text" name="valor_inscricao" class="form-control text-end" value="<?= number_format($aluno['valor_inscricao'], 2, ',', '.') ?>" required data-mask="moeda">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Valor Entregue</label>
                    <input type="text" name="valor_entregue" class="form-control text-end" value="<?= number_format($aluno['valor_entregue'], 2, ',', '.') ?>" data-mask="moeda">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Valor Pago Anteriormente</label>
                    <input type="text" name="valor_total_pago" class="form-control text-end" value="<?= number_format($aluno['valor_total_pago'], 2, ',', '.') ?>" data-mask="moeda">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Valor Pendente</label>
                    <input type="text" name="valor_pendente" class="form-control text-end fw-bold" value="<?= number_format($aluno['valor_pendente'], 2, ',', '.') ?>" readonly>
                </div>
            </div>
        </div>
    </div>

    <!-- Observações -->
    <div class="form-section"><i class="bi bi-file-text me-2"></i><h5 class="mb-0">OBSERVAÇÕES / ANEXOS</h5></div>
    <div class="card mb-4">
        <div class="card-body">
            <div class="alert alert-light border">
                <h6 class="mb-2"><i class="bi bi-info-circle me-1"></i>Informações Importantes</h6>
                <ul class="mb-0 small">
                    <li>Se o aluno já estudou ou tocou algum instrumento musical anteriormente</li>
                    <li>Se frequenta outro curso ou escola de música</li>
                    <li>Se possui alguma restrição de saúde, alergia ou necessidade especial</li>
                    <li>Qualquer outra informação relevante para o acompanhamento do aluno</li>
                </ul>
            </div>
            <label class="form-label mt-3">Observações</label>
            <textarea name="observacoes" class="form-control" rows="5"><?= htmlspecialchars($aluno['observacoes'] ?? '') ?></textarea>
        </div>
    </div>

    <!-- Botões -->
    <div class="d-flex justify-content-end gap-2 mb-5">
        <a href="<?= url('/alunos/' . $aluno['id']) ?>" class="btn btn-outline-secondary btn-lg"><i class="bi bi-x-circle me-1"></i> Cancelar</a>
        <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-circle-fill me-1"></i> Atualizar Inscrição</button>
    </div>
</form>