<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LivroController extends Controller
{
    /**
     * Lista todos os livros cadastrados no banco de dados.
     * Método chamado pela rota GET /livros.
     */
    public function index(): View
    {
        // Consulta os livros usando o Eloquent ORM, do mais recente para o mais antigo
        $livros = Livro::orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        // Envia a variável $livros para a view resources/views/livros/index.blade.php
        return view('livros.index', compact('livros'));
    }

    /**
     * Valida os dados enviados pelo formulário e salva um novo livro no banco.
     * Método chamado pela rota POST /livros.
     */
    public function store(Request $request): RedirectResponse
    {
        $anoAtual = (int) date('Y');

        // Valida os campos recebidos do formulário antes de gravar no banco
        $dadosValidados = $request->validate(
            [
                'nome' => ['required', 'string', 'max:255'],
                'autor' => ['required', 'string', 'max:255'],
                'categoria' => ['required', 'string', 'max:100'],
                'ano_publicacao' => ['required', 'integer', 'min:1000', "max:{$anoAtual}"],
            ],
            [
                'nome.required' => 'O nome do livro é obrigatório.',
                'nome.string' => 'O nome do livro deve ser um texto válido.',
                'nome.max' => 'O nome do livro deve ter no máximo 255 caracteres.',
                'autor.required' => 'O nome do autor é obrigatório.',
                'autor.string' => 'O nome do autor deve ser um texto válido.',
                'autor.max' => 'O nome do autor deve ter no máximo 255 caracteres.',
                'categoria.required' => 'A categoria é obrigatória.',
                'categoria.string' => 'A categoria deve ser um texto válido.',
                'categoria.max' => 'A categoria deve ter no máximo 100 caracteres.',
                'ano_publicacao.required' => 'O ano de publicação é obrigatório.',
                'ano_publicacao.integer' => 'O ano de publicação deve ser um número inteiro.',
                'ano_publicacao.min' => 'O ano de publicação deve ser igual ou superior a 1000.',
                'ano_publicacao.max' => "O ano de publicação não pode ser maior que {$anoAtual}.",
            ]
        );

        // Cria o novo registro na tabela "livros" usando o Model Livro (Eloquent ORM)
        Livro::create($dadosValidados);

        // Redireciona de volta para a listagem com uma mensagem de sucesso
        return redirect()
            ->route('livros.index')
            ->with('success', 'Livro cadastrado com sucesso!');
    }

    /**
     * Exclui o livro selecionado do banco de dados.
     * Método chamado pela rota DELETE /livros/{livro}.
     * O Laravel busca automaticamente o livro pelo ID informado na URL.
     */
    public function destroy(Livro $livro): RedirectResponse
    {
        $nomeLivro = $livro->nome;

        // Remove apenas o registro selecionado do banco de dados
        $livro->delete();

        // Redireciona de volta para a listagem com uma mensagem de confirmação
        return redirect()
            ->route('livros.index')
            ->with('success', "Livro \"{$nomeLivro}\" excluído com sucesso!");
    }
}
