{{-- @extends herda a estrutura HTML compartilhada de layouts/app.blade.php --}}
@extends('layouts.app')

@section('title', 'Cadastrar Livro — Biblioteca Digital')

{{-- @section('content') define o conteúdo principal exibido no @yield('content') do layout --}}
@section('content')
    <div class="form-card">
        <div class="page-header">
            <div>
                <h1 class="page-title">Cadastrar Novo Livro</h1>
                <p class="page-description">Preencha os dados abaixo para adicionar um livro ao acervo.</p>
            </div>

            <a href="{{ route('livros.index') }}" class="btn btn-secondary">
                Voltar à Listagem
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-error" role="alert">
                <strong>Não foi possível salvar o livro. Verifique os campos abaixo:</strong>
                <ul>
                    @foreach ($errors->all() as $erro)
                        <li>{{ $erro }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="{{ route('livros.store') }}" method="POST">
                    {{-- @csrf adiciona um token oculto de proteção exigido pelo Laravel em formulários POST --}}
                    @csrf

                    <div class="form-group">
                        <label for="nome" class="form-label">
                            Nome do Livro <span class="required-mark">*</span>
                        </label>
                        <input
                            type="text"
                            id="nome"
                            name="nome"
                            value="{{ old('nome') }}"
                            maxlength="255"
                            required
                            class="form-control @error('nome') is-invalid @enderror"
                            placeholder="Ex.: Dom Casmurro"
                        >
                        {{-- @error('nome') exibe a mensagem de erro caso a validação deste campo falhe --}}
                        @error('nome')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="autor" class="form-label">
                            Autor <span class="required-mark">*</span>
                        </label>
                        <input
                            type="text"
                            id="autor"
                            name="autor"
                            value="{{ old('autor') }}"
                            maxlength="255"
                            required
                            class="form-control @error('autor') is-invalid @enderror"
                            placeholder="Ex.: Machado de Assis"
                        >
                        @error('autor')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="categoria" class="form-label">
                            Categoria <span class="required-mark">*</span>
                        </label>
                        <input
                            type="text"
                            id="categoria"
                            name="categoria"
                            value="{{ old('categoria') }}"
                            maxlength="100"
                            required
                            class="form-control @error('categoria') is-invalid @enderror"
                            placeholder="Ex.: Romance, Ficção, Tecnologia"
                        >
                        @error('categoria')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="ano_publicacao" class="form-label">
                            Ano de Publicação <span class="required-mark">*</span>
                        </label>
                        <input
                            type="number"
                            id="ano_publicacao"
                            name="ano_publicacao"
                            value="{{ old('ano_publicacao') }}"
                            min="1000"
                            max="{{ date('Y') }}"
                            required
                            class="form-control @error('ano_publicacao') is-invalid @enderror"
                            placeholder="Ex.: 1899"
                        >
                        @error('ano_publicacao')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('livros.index') }}" class="btn btn-secondary">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Salvar Livro
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
