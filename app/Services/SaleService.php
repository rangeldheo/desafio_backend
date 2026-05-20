<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Service responsável pelo registro
 * de vendas do ERP.
 *
 * Valida estoque, baixa estoque
 * e calcula lucro.
 */
class SaleService
{
    /**
     * Registra uma venda.
     *
     * @param array<string, mixed> $data
     * @return array<string, float>
     */
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {

            $totalVenda = 0;
            $lucroTotal = 0;

            /**
             * Primeiro valida todos os estoques.
             */
            foreach ($data['produtos'] as $item) {

                /** @var Product|null $product */
                $product = Product::query()
                    ->find($item['id']);

                if (!$product) {
                    throw new RuntimeException(
                        'Produto não encontrado.'
                    );
                }

                if (
                    $product->estoque
                    <
                    $item['quantidade']
                ) {
                    throw new RuntimeException(
                        "Estoque insuficiente para o produto {$product->nome}."
                    );
                }
            }

            /**
             * Cria cabeçalho da venda.
             */
            $saleId = DB::table('sales')
                ->insertGetId([
                    'cliente' => $data['cliente'],
                    'total' => 0,
                    'lucro' => 0,
                    'status' => 'completed',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            /**
             * Processa itens da venda.
             */
            foreach ($data['produtos'] as $item) {

                /** @var Product $product */
                $product = Product::query()
                    ->findOrFail($item['id']);

                $quantidade =
                    $item['quantidade'];

                $precoVenda =
                    $item['preco_unitario'];

                $custoUnitario =
                    $product->custo_medio;

                $subtotal =
                    $quantidade
                    *
                    $precoVenda;

                $custoTotal =
                    $quantidade
                    *
                    $custoUnitario;

                $lucroItem =
                    $subtotal
                    -
                    $custoTotal;

                /**
                 * Baixa estoque.
                 */
                $product->update([
                    'estoque' =>
                        $product->estoque
                        -
                        $quantidade,
                ]);

                /**
                 * Salva item da venda.
                 */
                DB::table('sale_items')
                    ->insert([
                        'sale_id' => $saleId,
                        'product_id' => $product->id,
                        'quantidade' => $quantidade,
                        'preco_unitario' => $precoVenda,
                        'custo_unitario' => $custoUnitario,
                        'lucro_item' => round(
                            $lucroItem,
                            2
                        ),
                        'subtotal' => round(
                            $subtotal,
                            2
                        ),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                $totalVenda +=
                    $subtotal;

                $lucroTotal +=
                    $lucroItem;
            }

            /**
             * Atualiza totais da venda.
             */
            DB::table('sales')
                ->where('id', $saleId)
                ->update([
                    'total' => round(
                        $totalVenda,
                        2
                    ),
                    'lucro' => round(
                        $lucroTotal,
                        2
                    ),
                ]);

            return [
                'total' => round(
                    $totalVenda,
                    2
                ),
                'lucro' => round(
                    $lucroTotal,
                    2
                ),
            ];
        });
    }
}
