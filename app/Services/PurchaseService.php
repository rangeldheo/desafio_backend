<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

/**
 * Service responsável pelo registro
 * de compras do ERP.
 *
 * Atualiza estoque e recalcula
 * custo médio ponderado.
 */
class PurchaseService
{
    /**
     * Registra uma compra.
     *
     * @param array<string, mixed> $data
     * @return void
     */
    public function register(array $data): void
    {
        DB::transaction(function () use ($data) {

            $purchase = DB::table('purchases')
                ->insertGetId([
                    'fornecedor' => $data['fornecedor'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            foreach ($data['produtos'] as $item) {

                /** @var Product $product */
                $product = Product::query()
                    ->findOrFail($item['id']);

                $quantidadeAtual =
                    $product->estoque;

                $custoAtual =
                    $product->custo_medio;

                $novaQuantidade =
                    $item['quantidade'];

                $novoCusto =
                    $item['preco_unitario'];

                $novoCustoMedio = (
                    ($quantidadeAtual * $custoAtual)
                    +
                    ($novaQuantidade * $novoCusto)
                ) / (
                    $quantidadeAtual
                    +
                    $novaQuantidade
                );

                $novoEstoque =
                    $quantidadeAtual
                    +
                    $novaQuantidade;

                $product->update([
                    'estoque' => $novoEstoque,
                    'custo_medio' => round(
                        $novoCustoMedio,
                        2
                    ),
                ]);

                DB::table('purchase_items')
                    ->insert([
                        'purchase_id' => $purchase,
                        'product_id' => $product->id,
                        'quantidade' => $novaQuantidade,
                        'preco_unitario' => $novoCusto,
                        'subtotal' =>
                            $novaQuantidade
                            * $novoCusto,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
            }
        });
    }
}
