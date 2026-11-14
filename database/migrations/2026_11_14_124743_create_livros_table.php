<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Executa a migration: cria a tabela "livros" no banco de dados.
     */
    public function up(): void
    {
        Schema::create('livros', function (Blueprint $table) {
            $table->id();                         // Chave primária (BIGINT autoincremento)
            $table->string('nome', 255);          // Nome do livro (VARCHAR 255)
            $table->string('autor', 255);         // Nome do autor (VARCHAR 255)
            $table->string('categoria', 100);     // Categoria (VARCHAR 100)
            $table->integer('ano_publicacao');    // Ano de publicação (INTEGER)
            $table->timestamps();                 // Colunas created_at e updated_at (TIMESTAMP)
        });
    }

    /**
     * Desfaz a migration: remove a tabela "livros" caso seja necessário reverter.
     */
    public function down(): void
    {
        Schema::dropIfExists('livros');
    }
};
