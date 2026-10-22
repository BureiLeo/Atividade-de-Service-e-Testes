<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiProdutoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_cria_produto_com_margem_correta(): void
    {
        $this->postJson('/api/produtos', [
            'nome' => 'Lajota',
            'preco_custo' => 100,
            'preco' => 110,
        ])->assertCreated();

        $this->assertDatabaseHas('produtos', ['nome' => 'Lajota']);
    }

    public function test_nao_cria_produto_com_margem_inferior_ao_minimo_e_retorna_422(): void
    {
        $this->postJson('/api/produtos', [
            'nome' => 'Lajota',
            'preco_custo' => 100,
            'preco' => 109.99,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['preco'])
            ->assertJsonPath('errors.preco.0', 'O preço do produto deve ser no mínimo 110.00 (Margem Mínima)');

        $this->assertDatabaseCount('produtos', 0);
    }
}
