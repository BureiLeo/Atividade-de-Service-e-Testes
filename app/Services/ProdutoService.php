<?php

namespace App\Services;

use App\Models\Produto;
use Illuminate\Validation\ValidationException;

class ProdutoService
{
    private const MARGEM_MINIMA = 0.10;

    public function create(array $dados): Produto
    {
        $this->verificarMargem($dados['preco'], $dados['preco_custo']);

        return Produto::create($dados);
    }

    public function update(Produto $produto, array $dados): Produto
    {
        $this->verificarMargem(
            $dados['preco'] ?? $produto->preco,
            $dados['preco_custo'] ?? $produto->preco_custo,
        );

        $produto->update($dados);

        return $produto;
    }

    private function verificarMargem(float $preco, float $preco_custo): void
    {
        // O preço mínimo de um produto deve ser
        // maior ou igual a mais 10% do valor de custo
        $margem = $preco_custo + ($preco_custo * self::MARGEM_MINIMA);
        $minimo = round($margem, 2);

        if ($preco < $minimo) {
            throw ValidationException::withMessages([
                'preco' => sprintf('O preço do produto deve ser no mínimo %.2f (Margem Mínima)', $minimo),
            ]);
        }

    }
}
