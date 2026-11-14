<?php

use App\Http\Controllers\LivroController;
use Illuminate\Support\Facades\Route;

// Redireciona a página inicial (/) diretamente para a listagem de livros (/livros)
Route::redirect('/', '/livros');

// GET: Exibe a página principal com todos os livros cadastrados
Route::get('/livros', [LivroController::class, 'index'])->name('livros.index');

// GET: Exibe a página com o formulário de cadastro de um novo livro
Route::view('/livros/create', 'livros.create')->name('livros.create');

// POST: Recebe os dados do formulário e salva o novo livro no banco de dados
Route::post('/livros', [LivroController::class, 'store'])->name('livros.store');

// DELETE: Exclui o livro selecionado pelo seu ID no banco de dados
Route::delete('/livros/{livro}', [LivroController::class, 'destroy'])->name('livros.destroy');
