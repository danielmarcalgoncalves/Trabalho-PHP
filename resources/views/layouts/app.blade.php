<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- @yield('title') insere o título definido por cada página; se não houver, usa "Biblioteca Digital" --}}
    <title>@yield('title', 'Biblioteca Digital')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="site-header">
        <div class="container header-content">
            <a href="{{ route('livros.index') }}" class="brand">
                <svg class="brand-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span>
                    <span class="brand-title">Biblioteca Digital</span>
                    <span class="brand-subtitle">Sistema de Gerenciamento de Livros</span>
                </span>
            </a>

            <nav>
                <a href="{{ route('livros.create') }}" class="btn btn-primary">
                    + Cadastrar Livro
                </a>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            {{-- Exibe mensagens de sucesso ou erro enviadas pelo Controller via ->with(...) --}}
            @if (session('success'))
                <div class="alert alert-success" role="status">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-error" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            {{-- @yield('content') marca o espaço onde o conteúdo de cada página será inserido --}}
            @yield('content')
        </div>
    </main>

    <footer class="site-footer">
        <div class="container">
            Biblioteca Digital &mdash; Trabalho Acadêmico Full Stack (Laravel &amp; Blade)
        </div>
    </footer>
</body>
</html>
