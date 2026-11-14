<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Livro: representa a tabela "livros" no banco de dados.
 * Cada objeto desta classe corresponde a uma linha (um livro) da tabela.
 */
class Livro extends Model
{
    // Define explicitamente o nome da tabela no banco de dados
    protected $table = 'livros';

    // Lista os campos que podem ser preenchidos em massa pelo método Livro::create()
    protected $fillable = [
        'nome',
        'autor',
        'categoria',
        'ano_publicacao',
    ];

    // Garante que o campo ano_publicacao seja tratado como número inteiro no PHP
    protected $casts = [
        'ano_publicacao' => 'integer',
    ];
}
