<?php
// api/Controllers/AlunoController.php
namespace Controllers;

use Core\Controller;
use Models\Aluno;
use Models\Curso;

class AlunoController extends Controller
{
    private Aluno $alunoModel;
    private Curso $cursoModel;

    public function __construct()
    {
        parent::__construct();
        $this->alunoModel = new Aluno();
        $this->cursoModel = new Curso();
    }

    public function index(): void
    {
        $this->requireAdmin();

        $page = max(1, (int)($_GET['page'] ?? 1));
        $search = trim($_GET['search'] ?? '');
        $cursoId = !empty($_GET['curso_id']) ? (int)$_GET['curso_id'] : null;
        
        $conditions = [];
        if ($cursoId) {
            $conditions['curso_id'] = $cursoId;
        }

        if ($search) {
            $result = $this->alunoModel->search($search, $page, 15, $conditions);
        } else {
            $result = $this->alunoModel->paginate($page, 15, $conditions, 'created_at', 'DESC');
        }

        $cursos = $this->cursoModel->getAtivos();
        $stats = $this->alunoModel->getEstatisticas();

        $this->view('alunos/index', [
            'alunos' => $result['data'],
            'pagination' => $result,
            'search' => $search,
            'curso_id' => $cursoId,
            'cursos' => $cursos,
            'stats' => $stats,
        ]);
    }

    public function create(): void
    {
        $cursos = $this->cursoModel->getAtivos();
        $numeroAluno = $this->alunoModel->generateNumeroAluno();

        $this->view('alunos/create', [
            'cursos' => $cursos,
            'numero_aluno' => $numeroAluno,
            'today' => date('Y-m-d'),
        ]);
    }

    public function store(): void
    {
        $this->verifyCsrf();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/inscricao');
            return;
        }

        $data = $_POST;
        $data['numero_aluno'] = $this->alunoModel->generateNumeroAluno();
        
        foreach (['valor_inscricao', 'valor_entregue', 'valor_total_pago', 'valor_pendente'] as $campo) {
            if (isset($data[$campo]) && $data[$campo] !== '') {
                $data[$campo] = (string)floatval(str_replace(',', '.', str_replace('.', '', $data[$campo])));
            }
        }
        
        if (!empty($data['data_nascimento'])) {
            $data['idade'] = $this->alunoModel->calcularIdade($data['data_nascimento']);
        }

        $financeiro = $this->alunoModel->calcularPendentes($data);
        $data = array_merge($data, $financeiro);

        $data['possui_irmao'] = isset($data['possui_irmao']) ? 1 : 0;
        $data['ativo'] = 1;

        $rules = [
            'numero_aluno' => 'required|max:20',
            'tipo_documento' => 'required',
            'numero_documento' => 'required|max:50',
            'nome_completo' => 'required|min:3|max:200',
            'data_nascimento' => 'required|date',
            'provincia' => 'required|max:100',
            'municipio' => 'required|max:100',
            'bairro' => 'required|max:100',
            'curso_id' => 'required|numeric',
            'data_inscricao' => 'required|date',
            'valor_inscricao' => 'required|numeric',
        ];

        $errors = $this->validate($data, $rules);

        $numeroDocumento = trim((string)($data['numero_documento'] ?? ''));
        if ($numeroDocumento !== '' && $this->alunoModel->existsDocumento($numeroDocumento)) {
            $errors['numero_documento'] = 'Este número de documento já está cadastrado.';
        }

        if (!empty($errors)) {
            $_SESSION['old'] = $data;
            $_SESSION['errors'] = $errors;
            $this->redirect('/inscricao');
            return;
        }

        $id = false;
        $numberConflict = false;

        for ($attempt = 0; $attempt < 3; $attempt++) {
            if ($attempt > 0) {
                $data['numero_aluno'] = $this->alunoModel->generateNumeroAluno();
            }

            try {
                $id = $this->alunoModel->create($data);
                break;
            } catch (\PDOException $e) {
                $isNumberConflict = $e->getCode() === '23000'
                    && str_contains($e->getMessage(), 'numero_aluno');

                if (!$isNumberConflict) {
                    throw $e;
                }

                $numberConflict = true;
            }
        }

        if ($id) {
            $this->flash('success', 'Aluno cadastrado com sucesso!');
            $this->redirect($this->isAdmin() ? '/alunos' : '/inscricao');
        } else {
            $this->flash('error', $numberConflict
                ? 'Não foi possível gerar um número único para o aluno. Tente novamente.'
                : 'Erro ao cadastrar aluno.');
            $_SESSION['old'] = $data;
            if ($numberConflict) {
                $_SESSION['errors'] = [
                    'numero_aluno' => 'Não foi possível reservar o número da inscrição.',
                ];
            }
            $this->redirect('/inscricao');
        }
    }

    public function show(int $id): void
    {
        $this->requireAdmin();

        $aluno = $this->alunoModel->getWithCurso($id);
        
        if (!$aluno) {
            $this->flash('error', 'Aluno não encontrado.');
            $this->redirect('/alunos');
            return;
        }

        $this->view('alunos/show', ['aluno' => $aluno]);
    }

    public function edit(int $id): void
    {
        $this->requireAdmin();

        $aluno = $this->alunoModel->find($id);
        
        if (!$aluno) {
            $this->flash('error', 'Aluno não encontrado.');
            $this->redirect('/alunos');
            return;
        }

        $cursos = $this->cursoModel->getAtivos();

        $this->view('alunos/edit', [
            'aluno' => $aluno,
            'cursos' => $cursos,
        ]);
    }

    public function update(int $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect("/alunos/{$id}/edit");
            return;
        }

        $aluno = $this->alunoModel->find($id);
        
        if (!$aluno) {
            $this->flash('error', 'Aluno não encontrado.');
            $this->redirect('/alunos');
            return;
        }

        $data = $_POST;
        
        if (!empty($data['data_nascimento'])) {
            $data['idade'] = $this->alunoModel->calcularIdade($data['data_nascimento']);
        }

        $financeiro = $this->alunoModel->calcularPendentes($data);
        $data = array_merge($data, $financeiro);
        $data['possui_irmao'] = isset($data['possui_irmao']) ? 1 : 0;

        $rules = [
            'nome_completo' => 'required|min:3|max:200',
            'data_nascimento' => 'required|date',
            'curso_id' => 'required|numeric',
            'valor_inscricao' => 'required|numeric',
        ];

        $errors = $this->validate($data, $rules);

        if ($this->alunoModel->existsDocumento($data['numero_documento'], $id)) {
            $errors['numero_documento'] = 'Este número de documento já está cadastrado.';
        }

        if (!empty($errors)) {
            $_SESSION['old'] = $data;
            $_SESSION['errors'] = $errors;
            $this->redirect("/alunos/{$id}/edit");
            return;
        }

        if ($this->alunoModel->update($id, $data)) {
            $this->flash('success', 'Aluno atualizado com sucesso!');
            $this->redirect('/alunos');
        } else {
            $this->flash('error', 'Erro ao atualizar aluno.');
            $_SESSION['old'] = $data;
            $this->redirect("/alunos/{$id}/edit");
        }
    }

    public function destroy(int $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();

        $aluno = $this->alunoModel->find($id);

        if (!$aluno) {
            $this->flash('error', 'Aluno não encontrado.');
            $this->redirect('/alunos');
            return;
        }

        if ($this->alunoModel->delete($id)) {
            $this->flash('success', 'Aluno removido com sucesso.');
        } else {
            $this->flash('error', 'Erro ao remover aluno.');
        }

        $this->redirect('/alunos');
    }

    public function apiSearch(): void
    {
        $this->requireAdmin();

        $term = trim($_GET['q'] ?? '');
        $result = $this->alunoModel->search($term, 1, 10);
        
        $this->json([
            'data' => array_map(fn($a) => [
                'id' => (int)$a['id'],
                'numero_aluno' => $a['numero_aluno'],
                'nome_completo' => $a['nome_completo'],
                'curso_nome' => $a['curso_nome'] ?? '',
            ], $result['data']),
        ]);
    }
}
