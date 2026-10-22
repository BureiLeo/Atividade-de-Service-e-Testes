<?php

namespace Tests\Feature;

use App\Models\Produto;
use App\Services\ProdutoService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class ProdutoServiceTest extends TestCase
{
    use LazilyRefreshDatabase;

    #[TestWith([['nome' => 'Lajota atualizada', 'preco_custo' => 200, 'preco' => 220]])]
    #[TestWith([['preco' => 120]])]
    #[TestWith([['preco_custo' => 90]])]
    #[TestWith([['nome' => 'Lajota atualizada']])]
    #[TestWith([['preco_custo' => 100.05, 'preco' => 110.06]])]
    public function test_atualiza_produto_com_margem_valida(array $dados): void
    {
        $originais = ['nome' => 'Lajota', 'preco_custo' => 100, 'preco' => 110];
        $produto = Produto::create($originais);
        $service = new ProdutoService;

        $atualizado = $service->update($produto, $dados);

        $this->assertSame($produto, $atualizado);
        $this->assertDatabaseHas('produtos', array_replace($originais, $dados, ['id' => $produto->id]));
        $this->assertDatabaseCount('produtos', 1);
    }

    #[TestWith([['nome' => 'Nao deve atualizar', 'preco_custo' => 200, 'preco' => 219.99], '220.00'])]
    #[TestWith([['preco' => 109.99], '110.00'])]
    #[TestWith([['preco_custo' => 100.01], '110.01'])]
    public function test_nao_atualiza_produto_com_margem_inferior_ao_minimo(array $dados, string $minimo): void
    {
        $originais = ['nome' => 'Lajota', 'preco_custo' => 100, 'preco' => 110];
        $produto = Produto::create($originais);
        $service = new ProdutoService;

        try {
            $service->update($produto, $dados);
            $this->fail('A atualizacao deveria rejeitar o preco abaixo da margem minima.');
        } catch (ValidationException $exception) {
            $this->assertSame([
                'preco' => ["O preço do produto deve ser no mínimo {$minimo} (Margem Mínima)"],
            ], $exception->errors());
        }

        $this->assertDatabaseHas('produtos', array_merge($originais, ['id' => $produto->id]));
        $this->assertDatabaseCount('produtos', 1);
        $this->assertFalse($produto->isDirty());
    }
}
