{{-- @extends indica que esta página herda a estrutura HTML de layouts/app.blade.php --}}
@extends('layouts.app')

{{-- @section('title') preenche o @yield('title') do layout base --}}
@section('title', 'Livros Cadastrados — Biblioteca Digital')

{{-- @section('content') preenche o @yield('content') do layout base --}}
@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Acervo de Livros</h1>
            <p class="page-description">
                @if ($livros->count() === 1)
                    1 livro cadastrado no banco de dados.
                @else
                    {{ $livros->count() }} livros cadastrados no banco de dados.
                @endif
            </p>
        </div>

        <a href="{{ route('livros.create') }}" class="btn btn-primary">
            Cadastrar Novo Livro
        </a>
    </div>

    <div class="card">
        @if ($livros->isEmpty())
            <div class="empty-state">
                <h2>Nenhum livro cadastrado</h2>
                <p>O acervo ainda está vazio. Cadastre o primeiro livro para começar.</p>
                <a href="{{ route('livros.create') }}" class="btn btn-primary">
                    Cadastrar Primeiro Livro
                </a>
            </div>
        @else
            <div class="table-wrapper">
                <table class="books-table">
                    <thead>
                        <tr>
                            <th>Nome do Livro</th>
                            <th>Autor</th>
                            <th>Categoria</th>
                            <th>Ano de Publicação</th>
                            <th class="actions-col">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- @foreach percorre cada objeto $livro vindo do LivroController@index --}}
                        @foreach ($livros as $livro)
                            <tr>
                                <td class="book-title">{{ $livro->nome }}</td>
                                <td>{{ $livro->autor }}</td>
                                <td>
                                    <span class="badge">{{ $livro->categoria }}</span>
                                </td>
                                <td>{{ $livro->ano_publicacao }}</td>
                                <td class="actions-col">
                                    {{-- Formulário HTML usa POST, mas @method('DELETE') informa ao Laravel que é uma exclusão (DELETE) --}}
                                    <form
                                        action="{{ route('livros.destroy', $livro) }}"
                                        method="POST"
                                        data-livro="{{ $livro->nome }}"
                                        onsubmit="return confirm('Tem certeza que deseja excluir o livro &quot;' + this.dataset.livro + '&quot;?');"
                                    >
                                        {{-- @csrf gera um token de segurança obrigatório contra falsificação de requisições --}}
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            Excluir
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
