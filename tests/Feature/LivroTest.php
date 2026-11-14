<?php

namespace Tests\Feature;

use App\Models\Livro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LivroTest extends TestCase
{
    use RefreshDatabase;

    public function test_pagina_inicial_redireciona_para_listagem_de_livros(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('livros.index'));
    }

    public function test_listagem_exibe_livros_cadastrados(): void
    {
        Livro::create([
            'nome' => 'Dom Casmurro',
            'autor' => 'Machado de Assis',
            'categoria' => 'Romance',
            'ano_publicacao' => 1899,
        ]);

        $response = $this->get(route('livros.index'));

        $response->assertStatus(200);
        $response->assertSee('Biblioteca Digital');
        $response->assertSee('Dom Casmurro');
        $response->assertSee('Machado de Assis');
        $response->assertSee('Romance');
        $response->assertSee('1899');
    }

    public function test_formulario_de_cadastro_exibe_campos_e_csrf(): void
    {
        $response = $this->get(route('livros.create'));

        $response->assertStatus(200);
        $response->assertSee('name="_token"', false);
        $response->assertSee('name="nome"', false);
        $response->assertSee('name="autor"', false);
        $response->assertSee('name="categoria"', false);
        $response->assertSee('name="ano_publicacao"', false);
    }

    public function test_cadastro_salva_livro_no_banco_e_redireciona_com_mensagem(): void
    {
        $response = $this->post(route('livros.store'), [
            'nome' => 'O Cortiço',
            'autor' => 'Aluísio Azevedo',
            'categoria' => 'Realismo',
            'ano_publicacao' => 1890,
        ]);

        $response->assertRedirect(route('livros.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('livros', [
            'nome' => 'O Cortiço',
            'autor' => 'Aluísio Azevedo',
            'categoria' => 'Realismo',
            'ano_publicacao' => 1890,
        ]);
    }

    public function test_validacao_impede_cadastro_com_dados_invalidos(): void
    {
        $response = $this->post(route('livros.store'), [
            'nome' => '',
            'autor' => '',
            'categoria' => '',
            'ano_publicacao' => 999,
        ]);

        $response->assertSessionHasErrors([
            'nome',
            'autor',
            'categoria',
            'ano_publicacao',
        ]);

        $this->assertDatabaseCount('livros', 0);
    }

    public function test_exclusao_remove_apenas_o_livro_selecionado(): void
    {
        $livro1 = Livro::create([
            'nome' => 'Memórias Póstumas de Brás Cubas',
            'autor' => 'Machado de Assis',
            'categoria' => 'Romance',
            'ano_publicacao' => 1881,
        ]);

        $livro2 = Livro::create([
            'nome' => 'Vidas Secas',
            'autor' => 'Graciliano Ramos',
            'categoria' => 'Ficção',
            'ano_publicacao' => 1938,
        ]);

        $response = $this->delete(route('livros.destroy', $livro1));

        $response->assertRedirect(route('livros.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('livros', ['id' => $livro1->id]);
        $this->assertDatabaseHas('livros', ['id' => $livro2->id]);
    }
}
