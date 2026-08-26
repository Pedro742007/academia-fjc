# Deploy no Vercel - Academia FJC

## Estrutura do Projeto

```
/
├── api/                    # PHP serverless functions
│   ├── index.php           # Entry point (todas as rotas)
│   ├── config/
│   │   ├── database.php    # Config (usa env vars do Vercel)
│   │   └── helpers.php     # Funções auxiliares
│   ├── Core/
│   │   ├── Controller.php  # Base controller
│   │   ├── Database.php    # Conexão PDO
│   │   ├── DbSessionHandler.php  # Sessions via MySQL
│   │   └── Model.php       # Base model
│   ├── Controllers/        # Controllers da aplicação
│   └── Models/             # Models da aplicação
├── public/                 # Arquivos estáticos
│   └── assets/             # CSS, JS, imagens
├── src/                    # Views (PHP templates)
│   └── Views/              # Templates das páginas
├── database/
│   └── schema.sql          # Schema do banco de dados
├── vercel.json             # Configuração do Vercel
└── .gitignore
```

## Pré-requisitos

1. Conta no [Vercel](https://vercel.com) (gratuita)
2. Conta com MySQL (InfinityFree, PlanetScale, Railway, ou outro)
3. Git instalado

## Passo 1 — Preparar o banco de dados

1. Acesse o phpMyAdmin do seu provedor MySQL
2. Execute o conteúdo de `database/schema.sql`
3. Anote: **Host**, **Database Name**, **User** e **Password**

## Passo 2 — Push para o GitHub

```bash
cd "Academia FJC"
git init
git add .
git commit -m "Deploy Academia FJC no Vercel"
git remote add origin https://github.com/SEU_USUARIO/academia-fjc.git
git push -u origin main
```

## Passo 3 — Deploy no Vercel

1. Acesse [vercel.com/new](https://vercel.com/new)
2. Importe o repositório do GitHub
3. O Vercel detecta automaticamente o `vercel.json` + PHP
4. **NÃO configure build settings** — já está no vercel.json

## Passo 4 — Variáveis de Ambiente

No painel do Vercel → **Settings** → **Environment Variables**, adicione:

| Variável | Valor | Descrição |
|----------|-------|-----------|
| `DB_HOST` | `sql202.infinityfree.com` | Host do MySQL |
| `DB_PORT` | `3306` | Porta do MySQL |
| `DB_NAME` | `if0_42750170_Academiafjc` | Nome do banco |
| `DB_USER` | `if0_42750170` | Usuário MySQL |
| `DB_PASS` | `sua_senha_aqui` | Senha do MySQL |

> Marque como **Production**, **Preview** e **Development**.

## Passo 5 — Redeploy

Após configurar as env vars, vá em **Deployments** → clique nos 3 pontinhos → **Redeploy**.

## Acessar

- **Site**: `https://seu-projeto.vercel.app/`
- **Área admin**: `https://seu-projeto.vercel.app/admin`
  - Login: `admin@academiafjc.com` / `admin123`
  - **TROQUE A SENHA EM PRODUÇÃO!**

## Segurança Implementada

| Recurso | Status |
|---------|--------|
| Credenciais em env vars (não no código) | ✅ |
| Senhas com bcrypt cost 12 | ✅ |
| Session handler via MySQL (serverless-safe) | ✅ |
| Regenerate ID a cada 5 min (anti fixation) | ✅ |
| Rate limiting no login (5 tentativas / 15 min) | ✅ |
| Tokens CSRF em todos os forms | ✅ |
| Headers de segurança (X-Frame, XSS, etc.) | ✅ |
| Cookie SameSite=Lax + HttpOnly | ✅ |
| Input sanitization (htmlspecialchars) | ✅ |
| Prepared statements (anti SQL injection) | ✅ |
| Auto-rehash de senhas desatualizadas | ✅ |
| Cache de assets (1 ano, immutable) | ✅ |
| Senhas não logadas em erros | ✅ |

## Comandos úteis Vercel CLI

```bash
# Instalar CLI
npm i -g vercel

# Deploy de preview
vercel

# Deploy em produção
vercel --prod

# Ver logs
vercel logs
```

## Limitações do plano gratuito

- 100 GB de bandwidth/mês
- Serverless functions com max 10s de execução
- 1000 horas de build/mês
- Funciona perfeitamente para esta aplicação
