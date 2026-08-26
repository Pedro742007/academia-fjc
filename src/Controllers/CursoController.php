<?php
// src/Controllers/CursoController.php
namespace Controllers;

use Core\Controller;
use Models\Curso;

class CursoController extends Controller
{
    private Curso $cursoModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
        $this->cursoModel = new Curso();
    }

    public function index(): void
    {
        $cursos = $this->cursoModel->getComAlunos();

        $this->view('cursos/index', [
            'cursos' => $cursos,
        ]);
    }

    public function create(): void
    {
        $this->view('cursos/create');
    }

    public function store(): void
    {
        $this->verifyCsrf();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/cursos');
            return;
        }

        $data = $_POST;
        $data['ativo'] = 1;

        if (isset($data['valor_mensalidade']) && $data['valor_mensalidade'] !== '') {
            $data['valor_mensalidade'] = (string)floatval(str_replace(',', '.', str_replace('.', '', $data['valor_mensalidade'])));
        }

        $rules = [
            'nome' => 'required|min:3|max:150',
            'valor_mensalidade' => 'required|numeric',
        ];

        $errors = $this->validate($data, $rules);

        if (!empty($errors)) {
            $_SESSION['old'] = $data;
            $_SESSION['errors'] = $errors;
            $this->redirect('/cursos/criar');
            return;
        }

        $id = $this->cursoModel->create($data);

        if ($id) {
            $this->flash('success', 'Curso cadastrado com sucesso!');
            $this->redirect('/cursos');
        } else {
            $this->flash('error', 'Erro ao cadastrar curso.');
            $_SESSION['old'] = $data;
            $this->redirect('/cursos/criar');
        }
    }

    public function edit(int $id): void
    {
        $curso = $this->cursoModel->find($id);

        if (!$curso) {
            $this->flash('error', 'Curso não encontrado.');
            $this->redirect('/cursos');
            return;
        }

        $this->view('cursos/edit', ['curso' => $curso]);
    }

    public function update(int $id): void
    {
        $this->verifyCsrf();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect("/cursos/{$id}/editar");
            return;
        }

        $curso = $this->cursoModel->find($id);

        if (!$curso) {
            $this->flash('error', 'Curso não encontrado.');
            $this->redirect('/cursos');
            return;
        }

        $data = $_POST;

        if (isset($data['valor_mensalidade']) && $data['valor_mensalidade'] !== '') {
            $data['valor_mensalidade'] = (string)floatval(str_replace(',', '.', str_replace('.', '', $data['valor_mensalidade'])));
        }

        $rules = [
            'nome' => 'required|min:3|max:150',
            'valor_mensalidade' => 'required|numeric',
        ];

        $errors = $this->validate($data, $rules);

        if (!empty($errors)) {
            $_SESSION['old'] = $data;
            $_SESSION['errors'] = $errors;
            $this->redirect("/cursos/{$id}/editar");
            return;
        }

        if ($this->cursoModel->update($id, $data)) {
            $this->flash('success', 'Curso atualizado com sucesso!');
            $this->redirect('/cursos');
        } else {
            $this->flash('error', 'Erro ao atualizar curso.');
            $_SESSION['old'] = $data;
            $this->redirect("/cursos/{$id}/editar");
        }
    }

    public function destroy(int $id): void
    {
        $this->verifyCsrf();

        $curso = $this->cursoModel->find($id);

        if (!$curso) {
            $this->flash('error', 'Curso não encontrado.');
            $this->redirect('/cursos');
            return;
        }

        if ($this->cursoModel->delete($id)) {
            $this->flash('success', 'Curso removido com sucesso.');
        } else {
            $this->flash('error', 'Erro ao remover curso.');
        }

        $this->redirect('/cursos');
    }
}
