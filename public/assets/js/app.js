// public/assets/js/app.js
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Máscaras para inputs
    initMasks();
    
    // Validação de formulários
    initFormValidation();
    
    // Confirmação de exclusão
    initDeleteConfirmation();
    
    // Auto-cálculo de idade
    initAutoIdade();
    
    // Auto-cálculo financeiro
    initAutoFinanceiro();
    
    // Toggle irmão
    initIrmaoToggle();
    
    // Busca em tempo real (debounce)
    initLiveSearch();
    
    // Print
    initPrintButtons();
    
    // Animações
    initRevealAnimations();
    initStatCounters();
    initNavbarScroll();
});

// ===== MÁSCARAS =====
function initMasks() {
    // CPF
    document.querySelectorAll('input[data-mask="cpf"]').forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            e.target.value = value;
        });
    });

    // Telefone (formato angolano: 9XX XXX XXX)
    document.querySelectorAll('input[data-mask="telefone"]').forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 3) {
                value = value;
            } else if (value.length <= 6) {
                value = value.replace(/(\d{3})(\d)/, '$1 $2');
            } else {
                value = value.replace(/(\d{3})(\d{3})(\d)/, '$1 $2 $3');
            }
            e.target.value = value;
        });
    });

    // CEP
    document.querySelectorAll('input[data-mask="cep"]').forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
            e.target.value = value;
        });
    });

    // Data (formato BR para input type=date)
    document.querySelectorAll('input[type="date"]').forEach(input => {
        // O navegador cuida da formatação, mas podemos validar
        input.addEventListener('change', function(e) {
            const data = new Date(e.target.value);
            const hoje = new Date();
            if (data > hoje) {
                showToast('Data não pode ser futura', 'warning');
                e.target.value = '';
            }
        });
    });

    // Moeda
    document.querySelectorAll('input[data-mask="moeda"]').forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = (parseInt(value) / 100).toFixed(2) + '';
            value = value.replace('.', ',');
            value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            e.target.value = value;
        });
        
        input.addEventListener('focus', function(e) {
            if (e.target.value === '0,00') e.target.value = '';
        });
        
        input.addEventListener('blur', function(e) {
            if (!e.target.value) e.target.value = '0,00';
        });
    });

    // Apenas números
    document.querySelectorAll('input[data-mask="numeric"]').forEach(input => {
        input.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });
    });

    // Letras e espaços apenas
    document.querySelectorAll('input[data-mask="alpha"]').forEach(input => {
        input.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, '');
        });
    });

    // BI / Documento angolano: 9 dígitos + 2 letras + 3 dígitos (ex: 123456789LA042)
    document.querySelectorAll('input[data-mask="bi"]').forEach(input => {
        input.addEventListener('input', function(e) {
            const clean = e.target.value.toUpperCase().replace(/[^0-9A-Z]/g, '');
            const nums = clean.replace(/[^0-9]/g, '');
            const lets = clean.replace(/[^A-Z]/g, '');
            const d1 = nums.slice(0, 9);
            const l = lets.slice(0, 2);
            const d2 = nums.slice(9, 12);
            e.target.value = d1 + l + d2;
        });
    });
}

// ===== VALIDAÇÃO DE FORMULÁRIOS =====
function initFormValidation() {
    const forms = document.querySelectorAll('.needs-validation');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                
                // Focar no primeiro campo inválido
                const firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Validação customizada CPF
    document.querySelectorAll('input[data-validate="cpf"]').forEach(input => {
        input.addEventListener('blur', function(e) {
            const cpf = e.target.value.replace(/\D/g, '');
            if (cpf && !validarCPF(cpf)) {
                e.target.setCustomValidity('CPF inválido');
                e.target.classList.add('is-invalid');
            } else {
                e.target.setCustomValidity('');
                e.target.classList.remove('is-invalid');
            }
        });
    });
}

function validarCPF(cpf) {
    cpf = cpf.replace(/[^\d]/g, '');
    if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) return false;
    
    let soma = 0;
    for (let i = 0; i < 9; i++) soma += parseInt(cpf[i]) * (10 - i);
    let resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf[9])) return false;
    
    soma = 0;
    for (let i = 0; i < 10; i++) soma += parseInt(cpf[i]) * (11 - i);
    resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf[10])) return false;
    
    return true;
}

// ===== CONFIRMAÇÃO DE EXCLUSÃO =====
function initDeleteConfirmation() {
    document.querySelectorAll('[data-confirm-delete]').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const message = this.dataset.confirmDelete || 'Tem certeza que deseja excluir?';
            const url = this.href || this.dataset.url;
            const method = this.dataset.method || 'DELETE';
            
            if (confirm(message)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.style.display = 'none';
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = method;
                form.appendChild(methodInput);
                
                // CSRF token se existir
                const csrf = document.querySelector('meta[name="csrf-token"]');
                if (csrf) {
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrf.content;
                    form.appendChild(csrfInput);
                }
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
}

// ===== AUTO CÁLCULO IDADE =====
function initAutoIdade() {
    const dataNascimento = document.querySelector('input[name="data_nascimento"]');
    const idadeInput = document.querySelector('input[name="idade"]');
    
    if (dataNascimento && idadeInput) {
        dataNascimento.addEventListener('change', function() {
            if (this.value) {
                const nascimento = new Date(this.value);
                const hoje = new Date();
                let idade = hoje.getFullYear() - nascimento.getFullYear();
                const mes = hoje.getMonth() - nascimento.getMonth();
                
                if (mes < 0 || (mes === 0 && hoje.getDate() < nascimento.getDate())) {
                    idade--;
                }
                
                idadeInput.value = idade;
                idadeInput.dispatchEvent(new Event('change'));
            }
        });
    }
}

// ===== AUTO CÁLCULO FINANCEIRO =====
function initAutoFinanceiro() {
    const valorInscricao = document.querySelector('input[name="valor_inscricao"]');
    const valorEntregue = document.querySelector('input[name="valor_entregue"]');
    const valorTotalPago = document.querySelector('input[name="valor_total_pago"]');
    const valorPendente = document.querySelector('input[name="valor_pendente"]');
    
    function calcular() {
        const inscricao = parseMoeda(valorInscricao?.value) || 0;
        const entregue = parseMoeda(valorEntregue?.value) || 0;
        const totalPago = parseMoeda(valorTotalPago?.value) || 0;
        
        const total = entregue + totalPago;
        const pendente = Math.max(0, inscricao - total);
        
        if (valorPendente) {
            valorPendente.value = formatMoeda(pendente);
            valorPendente.style.color = pendente > 0 ? '#e74c3c' : '#27ae60';
            valorPendente.style.fontWeight = '600';
        }
    }
    
    [valorInscricao, valorEntregue, valorTotalPago].forEach(input => {
        if (input) {
            input.addEventListener('input', calcular);
            input.addEventListener('blur', calcular);
        }
    });
    
    // Calcular ao carregar
    calcular();
}

function parseMoeda(valor) {
    if (!valor) return 0;
    return parseFloat(valor.replace(/\./g, '').replace(',', '.')) || 0;
}

function formatMoeda(valor) {
    return valor.toLocaleString('pt-AO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// ===== TOGGLE IRMÃO =====
function initIrmaoToggle() {
    const checkbox = document.querySelector('input[name="possui_irmao"]');
    const nomeIrmao = document.querySelector('input[name="nome_irmao"]');
    
    if (checkbox && nomeIrmao) {
        function toggle() {
            nomeIrmao.disabled = !checkbox.checked;
            nomeIrmao.required = checkbox.checked;
            if (!checkbox.checked) {
                nomeIrmao.value = '';
            }
        }
        
        checkbox.addEventListener('change', toggle);
        toggle(); // Estado inicial
    }
}

// ===== BUSCA EM TEMPO REAL =====
function initLiveSearch() {
    const searchInput = document.querySelector('input[name="search"]');
    const searchForm = searchInput?.closest('form');
    
    if (searchInput && searchForm) {
        let debounceTimer;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                if (this.value.length >= 2 || this.value.length === 0) {
                    searchForm.submit();
                }
            }, 500);
        });
    }
}

// ===== BOTÕES DE IMPRESSÃO =====
function initPrintButtons() {
    document.querySelectorAll('[data-print]').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const target = this.dataset.print;
            
            if (target === 'current') {
                window.print();
            } else {
                const element = document.querySelector(target);
                if (element) {
                    printElement(element);
                }
            }
        });
    });
}

function printElement(element) {
    const printWindow = window.open('', '_blank');
    const styles = Array.from(document.querySelectorAll('link[rel="stylesheet"], style'))
        .map(el => el.outerHTML)
        .join('\n');
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Imprimir</title>
            ${styles}
            <style>
                @media print {
                    .no-print { display: none !important; }
                    body { padding: 20px; }
                }
            </style>
        </head>
        <body>${element.outerHTML}</body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 250);
}

// ===== TOAST NOTIFICATIONS =====
function showToast(message, type = 'info') {
    const container = getOrCreateToastContainer();
    
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    container.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast, { delay: 4000 });
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', () => toast.remove());
}

function getOrCreateToastContainer() {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
    }
    return container;
}

// ===== LOADING OVERLAY =====
function showLoading(message = 'Carregando...') {
    let overlay = document.getElementById('loading-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'loading-overlay';
        overlay.className = 'loading-overlay';
        overlay.innerHTML = '<div class="spinner"></div>';
        document.body.appendChild(overlay);
    }
    overlay.classList.add('active');
}

function hideLoading() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) overlay.classList.remove('active');
}

// ===== FORMATAÇÃO DE DATAS =====
function formatDateBR(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString + 'T00:00:00');
    return date.toLocaleDateString('pt-AO');
}

function formatDateTimeBR(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleString('pt-AO');
}

// ===== REVELAR ELEMENTOS AO ROLAR =====
function initRevealAnimations() {
    const targets = document.querySelectorAll('.stat-card, .card:not(.auth-card)');
    if (targets.length === 0) return;

    if (!('IntersectionObserver' in window)) {
        targets.forEach(el => el.classList.add('visible'));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            const el = entry.target;
            const siblings = el.parentElement ? Array.from(el.parentElement.children) : [el];
            const idx = siblings.indexOf(el);
            el.classList.add('js-animate', 'visible');
            el.style.animationDelay = Math.min(idx * 60, 360) + 'ms';
            observer.unobserve(el);
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });

    targets.forEach(el => {
        el.classList.add('js-animate');
        observer.observe(el);
    });
}

// ===== CONTAGEM ANIMADA DOS NÚMEROS =====
function initStatCounters() {
    document.querySelectorAll('.stat-card h3').forEach(el => {
        const original = el.textContent.trim();
        const match = original.match(/^([\d\s.]*[.,]?\d*)\s*(.*)$/);
        if (!match) return;

        const target = parseFloat(match[1].replace(/\./g, '').replace(',', '.'));
        if (isNaN(target)) return;

        const suffix = match[2] ? ' ' + match[2] : '';
        const decimals = /[.,]\d+$/.test(match[1]) ? 2 : 0;
        const duration = 800;
        const start = performance.now();

        function tick(now) {
            const p = Math.min(1, (now - start) / duration);
            const eased = 1 - Math.pow(1 - p, 3);
            const formatted = (target * eased).toLocaleString('pt-AO', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });
            el.textContent = formatted + suffix;
            if (p < 1) requestAnimationFrame(tick);
        }

        requestAnimationFrame(tick);
    });
}

// ===== SOMBRA NA NAVBAR AO ROLAR =====
function initNavbarScroll() {
    const navbar = document.querySelector('.navbar');
    if (!navbar) return;

    function update() {
        navbar.classList.toggle('scrolled', window.scrollY > 8);
    }

    window.addEventListener('scroll', update, { passive: true });
    update();
}

// ===== FORMATAR VALOR MOEDA PARA USO GLOBAL =====
function formatMoedaPT(valor) {
    return valor.toLocaleString('pt-AO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// ===== EXPORTAR PARA USO GLOBAL =====
window.AcademiaFJC = {
    showToast,
    showLoading,
    hideLoading,
    formatDateBR,
    formatDateTimeBR,
    parseMoeda,
    formatMoeda,
    validarCPF,
    printElement
};