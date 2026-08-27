<?php
// src/Views/alunos/create.php
$title = 'Nova Inscrição';
$scripts = '
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelector("input[name=\'nome_completo\']")?.focus();
    
    document.querySelector("select[name=\'curso_id\']")?.addEventListener("change", function() {
        const selected = this.options[this.selectedIndex];
        const valor = selected.dataset.valor;
        if (valor && !document.querySelector("input[name=\'valor_inscricao\']").value) {
            document.querySelector("input[name=\'valor_inscricao\']").value = parseFloat(valor).toLocaleString("pt-AO", {minimumFractionDigits: 2});
        }
    });
    
    
    // Auto-calcular idade
    document.querySelector("input[name=\'data_nascimento\']")?.addEventListener("change", function() {
        const nascimento = new Date(this.value);
        const hoje = new Date();
        let idade = hoje.getFullYear() - nascimento.getFullYear();
        const mes = hoje.getMonth() - nascimento.getMonth();
        if (mes < 0 || (mes === 0 && hoje.getDate() < nascimento.getDate())) idade--;
        document.querySelector("input[name=\'idade\']").value = idade > 0 ? idade : "";
    });
    
    // Calcular pendente em tempo real
    ["valor_inscricao", "valor_entregue", "valor_total_pago"].forEach(id => {
        document.querySelector("input[name=\'" + id + "\']")?.addEventListener("input", calcularPendente);
    });
    
    function calcularPendente() {
        const getValor = (name) => parseFloat(document.querySelector("input[name=\'" + name + "\']").value.replace(/[^\\d,-]/g, "").replace(",", ".")) || 0;
        const inscricao = getValor("valor_inscricao");
        const entregue = getValor("valor_entregue");
        const pago = getValor("valor_total_pago");
        const pendente = inscricao - (entregue + pago);
        document.querySelector("input[name=\'valor_pendente\']").value = pendente.toLocaleString("pt-AO", {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
    
    // Mostrar/ocultar campo irmão
    document.querySelector("#possui_irmao")?.addEventListener("change", function() {
        document.querySelector("#irmao_field").style.display = this.checked ? "block" : "none";
    });
    
    // Máscaras
    const masks = {
        telefone: (v) => v.replace(/\\D/g, "").replace(/^(\\d{2})(\\d{4})(\\d{4}).*/, "($1) $2-$3"),
        bi: (v) => {
            const clean = v.toUpperCase().replace(/[^0-9A-Z]/g, "");
            const nums = clean.replace(/[^0-9]/g, "");
            const lets = clean.replace(/[^A-Z]/g, "");
            const d1 = nums.slice(0, 9);
            const l = lets.slice(0, 2);
            const d2 = nums.slice(9, 12);
            return d1 + l + d2;
        },
        moeda: (v) => {
            const digits = v.replace(/\\D/g, "");
            if (!digits) return "";
            const num = parseInt(digits, 10);
            let str = num.toString().replace(/\\B(?=(\\d{3})+(?!\\d))/g, ".");
            return str + ",00";
        },
        alpha: (v) => v.replace(/[^a-zA-ZÀ-ÿ\\s]/g, "")
    };
    
    document.querySelectorAll("[data-mask]").forEach(input => {
        input.addEventListener("input", function() {
            const mask = masks[this.dataset.mask];
            if (mask) this.value = mask(this.value);
        });
    });
    
    // Validação visual Bootstrap
    const form = document.querySelector("form.needs-validation");
    form?.addEventListener("submit", function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add("was-validated");
    });
});
</script>
'?>

<div class="page-header mb-4">
    <div>
        <h2><i class="bi bi-person-plus-fill me-2"></i>Nova Inscrição</h2>
        <p>Ficha de Inscrição - Academia FJC</p>
    </div>
    <?php if (($_SESSION['usuario_nivel'] ?? '') === 'admin'): ?>
    <a href="<?= url('/alunos') ?>" class="btn btn-light btn-lg"><i class="bi bi-arrow-left me-1"></i> Listar Alunos</a>
    <?php endif; ?>
</div>

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

<form method="POST" action="<?= url('/inscricao') ?>" class="needs-validation" novalidate>
    <?= csrf_field() ?>
    
    <!-- Identificação do Aluno -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-primary text-white"><i class="bi bi-card-text me-2"></i>IDENTIFICAÇÃO DO ALUNO</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-medium">Nº do Aluno <span class="text-danger">*</span></label>
                    <input type="text" name="numero_aluno" class="form-control form-control-lg" value="<?= htmlspecialchars($numero_aluno ?? '') ?>" readonly>
                    <div class="form-text">Gerado automaticamente</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Tipo de Documento <span class="text-danger">*</span></label>
                    <select name="tipo_documento" class="form-select form-select-lg" required>
                        <option value="">Selecione...</option>
                        <option value="BI" <?= (old('tipo_documento') === 'BI') ? 'selected' : '' ?>>B.I. (Bilhete de Identidade)</option>
                        <option value="Cedula" <?= (old('tipo_documento') === 'Cedula') ? 'selected' : '' ?>>Cédula de Identidade</option>
                        <option value="Passaporte" <?= (old('tipo_documento') === 'Passaporte') ? 'selected' : '' ?>>Passaporte</option>
                    </select>
                    <div class="invalid-feedback">Selecione o tipo de documento</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Nº do Documento <span class="text-danger">*</span></label>
                    <input type="text" name="numero_documento" class="form-control form-control-lg" value="<?= htmlspecialchars(old('numero_documento')) ?>" required maxlength="14" placeholder="Ex: 123456789LA042" data-mask="bi">
                    <div class="form-text">Formato BI: 9 dígitos + 2 letras + 3 dígitos (ex: 123456789LA042)</div>
                    <div class="invalid-feedback">Informe o número do documento</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dados do Aluno -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-secondary text-white"><i class="bi bi-person-circle me-2"></i>DADOS DO ALUNO</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-7">
                    <label class="form-label fw-medium">Nome Completo <span class="text-danger">*</span></label>
                    <input type="text" name="nome_completo" class="form-control form-control-lg" value="<?= htmlspecialchars(old('nome_completo')) ?>" required minlength="3" maxlength="200" placeholder="Nome completo do aluno" data-mask="alpha">
                    <div class="invalid-feedback">Informe o nome completo (mín. 3 caracteres)</div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">Data de Nascimento <span class="text-danger">*</span></label>
                    <input type="date" name="data_nascimento" class="form-control form-control-lg" value="<?= htmlspecialchars(old('data_nascimento')) ?>" required max="<?= date('Y-m-d') ?>">
                    <div class="invalid-feedback">Informe a data de nascimento</div>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-medium">Idade</label>
                    <input type="number" name="idade" class="form-control form-control-lg" value="<?= htmlspecialchars(old('idade')) ?>" readonly min="0" max="120">
                    <div class="form-text">Calculada automaticamente</div>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-medium">Província</label>
                    <input type="text" name="provincia" class="form-control form-control-lg" value="Luanda" readonly>
                    <div class="form-text">Luanda — única província disponível</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Município <span class="text-danger">*</span></label>
                    <input type="text" name="municipio" class="form-control form-control-lg" value="<?= htmlspecialchars(old('municipio')) ?>" required maxlength="100" placeholder="Ex: Luanda, Viana, Cacuaco...">
                    <div class="invalid-feedback">Informe o município</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Bairro/Comuna <span class="text-danger">*</span></label>
                    <input type="text" name="bairro" class="form-control form-control-lg" value="<?= htmlspecialchars(old('bairro')) ?>" required maxlength="100" placeholder="Ex: Ingombota, Cazenga, Kilamba...">
                    <div class="invalid-feedback">Informe o bairro/comuna</div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Endereço Completo</label>
                    <textarea name="morada" class="form-control" rows="2" placeholder="Rua, número de casa, referência, bloco, apartamento..."><?= htmlspecialchars(old('morada')) ?></textarea>
                </div>
            </div>
            
            <hr class="my-3">
            
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="possui_irmao" id="possui_irmao" value="1" <?= old('possui_irmao') ? 'checked' : '' ?>>
                        <label class="form-check-label fw-medium" for="possui_irmao">Possui irmão(ã) matriculado(a) na Academia?</label>
                    </div>
                </div>
                <div class="col-md-6" id="irmao_field" style="<?= old('possui_irmao') ? '' : 'display:none' ?>">
                    <label class="form-label fw-medium">Nome do Irmão(ã)</label>
                    <input type="text" name="nome_irmao" class="form-control" value="<?= htmlspecialchars(old('nome_irmao')) ?>" placeholder="Nome do irmão matriculado" data-mask="alpha">
                </div>
            </div>
        </div>
    </div>

    <!-- Responsáveis Legais -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-primary text-white"><i class="bi bi-shield-check me-2"></i>RESPONSÁVEIS LEGAIS</div>
        <div class="card-body">
            <h6 class="text-primary mb-3"><i class="bi bi-person-badge me-1"></i>1º Responsável <span class="text-danger">*</span></h6>
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label fw-medium">Nome Completo <span class="text-danger">*</span></label>
                    <input type="text" name="responsavel1_nome" class="form-control" value="<?= htmlspecialchars(old('responsavel1_nome')) ?>" required maxlength="200" placeholder="Nome completo do responsável" data-mask="alpha">
                    <div class="invalid-feedback">Informe o nome do responsável</div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">Grau de Parentesco <span class="text-danger">*</span></label>
                    <select name="responsavel1_parentesco" class="form-select" required>
                        <option value="">Selecione...</option>
                        <option value="Pai" <?= old('responsavel1_parentesco') === 'Pai' ? 'selected' : '' ?>>Pai</option>
                        <option value="Mãe" <?= old('responsavel1_parentesco') === 'Mãe' ? 'selected' : '' ?>>Mãe</option>
                        <option value="Responsável Legal" <?= old('responsavel1_parentesco') === 'Responsável Legal' ? 'selected' : '' ?>>Responsável Legal</option>
                        <option value="Avô" <?= old('responsavel1_parentesco') === 'Avô' ? 'selected' : '' ?>>Avô</option>
                        <option value="Avó" <?= old('responsavel1_parentesco') === 'Avó' ? 'selected' : '' ?>>Avó</option>
                        <option value="Tio" <?= old('responsavel1_parentesco') === 'Tio' ? 'selected' : '' ?>>Tio</option>
                        <option value="Tia" <?= old('responsavel1_parentesco') === 'Tia' ? 'selected' : '' ?>>Tia</option>
                        <option value="Outro" <?= old('responsavel1_parentesco') === 'Outro' ? 'selected' : '' ?>>Outro</option>
                    </select>
                    <div class="invalid-feedback">Selecione o grau de parentesco</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Contacto <span class="text-danger">*</span></label>
                    <input type="tel" name="responsavel1_contacto" class="form-control" value="<?= htmlspecialchars(old('responsavel1_contacto')) ?>" required maxlength="20" placeholder="923 456 789" data-mask="telefone">
                    <div class="form-text">Formato: 9XX XXX XXX (ex: 923 456 789)</div>
                    <div class="invalid-feedback">Informe o telefone de contato</div>
                </div>
            </div>

            <hr class="my-3">

            <h6 class="text-primary mb-3"><i class="bi bi-person-badge me-1"></i>2º Responsável (Opcional)</h6>
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label fw-medium">Nome Completo</label>
                    <input type="text" name="responsavel2_nome" class="form-control" value="<?= htmlspecialchars(old('responsavel2_nome')) ?>" maxlength="200" placeholder="Nome completo" data-mask="alpha">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">Grau de Parentesco</label>
                    <select name="responsavel2_parentesco" class="form-select">
                        <option value="">Selecione...</option>
                        <option value="Pai" <?= old('responsavel2_parentesco') === 'Pai' ? 'selected' : '' ?>>Pai</option>
                        <option value="Mãe" <?= old('responsavel2_parentesco') === 'Mãe' ? 'selected' : '' ?>>Mãe</option>
                        <option value="Responsável Legal" <?= old('responsavel2_parentesco') === 'Responsável Legal' ? 'selected' : '' ?>>Responsável Legal</option>
                        <option value="Avô" <?= old('responsavel2_parentesco') === 'Avô' ? 'selected' : '' ?>>Avô</option>
                        <option value="Avó" <?= old('responsavel2_parentesco') === 'Avó' ? 'selected' : '' ?>>Avó</option>
                        <option value="Tio" <?= old('responsavel2_parentesco') === 'Tio' ? 'selected' : '' ?>>Tio</option>
                        <option value="Tia" <?= old('responsavel2_parentesco') === 'Tia' ? 'selected' : '' ?>>Tia</option>
                        <option value="Outro" <?= old('responsavel2_parentesco') === 'Outro' ? 'selected' : '' ?>>Outro</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Contacto</label>
                    <input type="tel" name="responsavel2_contacto" class="form-control" value="<?= htmlspecialchars(old('responsavel2_contacto')) ?>" maxlength="20" placeholder="923 456 789" data-mask="telefone">
                </div>
            </div>
        </div>
    </div>

    <!-- Contacto de Emergência -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-warning text-dark"><i class="bi bi-telephone-inbound me-2"></i>CONTACTO PARA RECADOS OU EMERGÊNCIAS <span class="text-danger">*</span></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Nome <span class="text-danger">*</span></label>
                    <input type="text" name="emergencia_nome" class="form-control" value="<?= htmlspecialchars(old('emergencia_nome')) ?>" required maxlength="200" placeholder="Nome do contato de emergência" data-mask="alpha">
                    <div class="invalid-feedback">Informe o nome para emergência</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Telefone <span class="text-danger">*</span></label>
                    <input type="tel" name="emergencia_telefone" class="form-control" value="<?= htmlspecialchars(old('emergencia_telefone')) ?>" required maxlength="20" placeholder="923 456 789" data-mask="telefone">
                    <div class="invalid-feedback">Informe o telefone de emergência</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dados da Inscrição -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-primary text-white"><i class="bi bi-journal-plus me-2"></i>DADOS DA INSCRIÇÃO</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Curso Inscrito <span class="text-danger">*</span></label>
                    <select name="curso_id" class="form-select form-select-lg" required>
                        <option value="">Selecione o curso...</option>
                        <?php foreach ($cursos ?? [] as $curso): ?>
                            <option value="<?= $curso['id'] ?>" data-valor="<?= $curso['valor_mensalidade'] ?>" <?= old('curso_id') == $curso['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($curso['nome']) ?> - <?= number_format($curso['valor_mensalidade'], 2, ',', '.') ?> Kz/mês
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Selecione um curso</div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">Data da Inscrição <span class="text-danger">*</span></label>
                    <input type="date" name="data_inscricao" class="form-control form-control-lg" value="<?= htmlspecialchars(old('data_inscricao') ?? date('Y-m-d')) ?>" required max="<?= date('Y-m-d') ?>">
                    <div class="invalid-feedback">Informe a data de inscrição</div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">Valor da Inscrição (Kz) <span class="text-danger">*</span></label>
                    <input type="text" name="valor_inscricao" class="form-control form-control-lg text-end" value="<?= htmlspecialchars(old('valor_inscricao')) ?>" required data-mask="moeda" placeholder="0,00">
                    <div class="invalid-feedback">Informe o valor da inscrição</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Valor Entregue (Kz)</label>
                    <input type="text" name="valor_entregue" class="form-control text-end" value="<?= htmlspecialchars(old('valor_entregue') ?? '0,00') ?>" data-mask="moeda" placeholder="0,00">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Valor Pago Anteriormente (Kz)</label>
                    <input type="text" name="valor_total_pago" class="form-control text-end" value="<?= htmlspecialchars(old('valor_total_pago') ?? '0,00') ?>" data-mask="moeda" placeholder="0,00">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Valor Pendente (Kz)</label>
                    <input type="text" name="valor_pendente" class="form-control text-end fw-bold fs-5" value="<?= htmlspecialchars(old('valor_pendente') ?? '0,00') ?>" readonly>
                </div>
            </div>
        </div>
    </div>

    <!-- Observações -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-dark text-white"><i class="bi bi-file-text me-2"></i>OBSERVAÇÕES / ANEXOS</div>
        <div class="card-body">
            <div class="alert alert-light border">
                <h6 class="mb-2"><i class="bi bi-info-circle me-1"></i>Informações Importantes para o Acompanhamento do Aluno</h6>
                <ul class="mb-0 small">
                    <li>Se o aluno já estudou ou tocou algum instrumento musical anteriormente</li>
                    <li>Se frequenta outro curso ou escola de música</li>
                    <li>Se possui alguma restrição de saúde, alergia ou necessidade especial</li>
                    <li>Qualquer outra informação relevante para o acompanhamento do aluno</li>
                </ul>
            </div>
            <label class="form-label fw-medium mt-3">Observações</label>
            <textarea name="observacoes" class="form-control" rows="5" placeholder="Digite aqui as observações..."><?= htmlspecialchars(old('observacoes')) ?></textarea>
        </div>
    </div>

    <!-- Botões -->
    <div class="d-flex justify-content-end gap-3 mb-5 flex-wrap">
        <a href="<?= url(($_SESSION['usuario_nivel'] ?? '') === 'admin' ? '/alunos' : '/inscricao') ?>" class="btn btn-outline-secondary btn-lg px-4"><i class="bi bi-x-circle me-1"></i> Cancelar</a>
        <button type="submit" class="btn btn-primary btn-lg px-5"><i class="bi bi-check-circle-fill me-1"></i> Salvar Inscrição</button>
    </div>
</form>