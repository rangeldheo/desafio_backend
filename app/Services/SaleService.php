<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Service responsável
 * pelo gerenciamento
 * de vendas do ERP.
 */
class SaleService
{
    /**
     * Registra uma venda.
     *
     * @param array<string, mixed> $data
     * @return array<string, float>
     */
    public function register(
        array $data
    ): array {
        return DB::transaction(
            function () use ($data) {

                $products =
                    $this->loadProducts(
                        $data['produtos']
                    );

                $this->validateStock(
                    $data['produtos'],
                    $products
                );

                $saleId =
                    $this->createSale(
                        $data['cliente']
                    );

                $result =
                    $this->processItems(
                        $saleId,
                        $data['produtos'],
                        $products
                    );

                $this->updateSaleTotals(
                    $saleId,
                    $result
                );

                return $result;
            }
        );
    }

    /**
     * Cancela uma venda
     * e devolve os itens
     * ao estoque.
     */
    public function cancel(
        int $saleId
    ): void {
        DB::transaction(
            function () use ($saleId) {

                $sale =
                    DB::table('sales')
                        ->lockForUpdate()
                        ->where(
                            'id',
                            $saleId
                        )
                        ->first();

                if (!$sale) {
                    throw new RuntimeException(
                        'Venda não encontrada.'
                    );
                }

                if (
                    $sale->status
                    ===
                    'cancelled'
                ) {
                    throw new RuntimeException(
                        'A venda já está cancelada.'
                    );
                }

                $items =
                    DB::table(
                        'sale_items'
                    )
                        ->where(
                            'sale_id',
                            $saleId
                        )
                        ->get();

                $productIds =
                    $items
                        ->pluck(
                            'product_id'
                        )
                        ->unique()
                        ->toArray();

                $products =
                    Product::query()
                        ->lockForUpdate()
                        ->whereIn(
                            'id',
                            $productIds
                        )
                        ->get()
                        ->keyBy('id');

                foreach (
                    $items
                    as
                    $item
                ) {
                    /** @var Product|null $product */
                    $product =
                        $products[
                            $item->product_id
                        ]
                        ?? null;

                    if (!$product) {
                        throw new RuntimeException(
                            'Produto da venda não encontrado.'
                        );
                    }

                    $this->increaseStock(
                        $product,
                        $item->quantidade
                    );
                }

                DB::table('sales')
                    ->where(
                        'id',
                        $saleId
                    )
                    ->update([
                        'status' =>
                            'cancelled',

                        'updated_at' =>
                            now(),
                    ]);
            }
        );
    }

    /**
     * Carrega todos
     * os produtos
     * da venda.
     *
     * @param array<int, array<string, mixed>> $items
     *
     * @return Collection<int, Product>
     */
    private function loadProducts(
        array $items
    ): Collection {
        $ids =
            collect($items)
                ->pluck('id')
                ->unique()
                ->toArray();

        return Product::query()
            ->lockForUpdate()
            ->whereIn(
                'id',
                $ids
            )
            ->get()
            ->keyBy('id');
    }

    /**
     * Valida estoque
     * dos produtos.
     *
     * @param array<int, array<string, mixed>> $items
     * @param Collection<int, Product> $products
     */
    private function validateStock(
        array $items,
        Collection $products
    ): void {
        foreach (
            $items
            as
            $item
        ) {
            /** @var Product|null $product */
            $product =
                $products[
                    $item['id']
                ]
                ?? null;

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
    }

    /**
     * Cria o cabeçalho
     * da venda.
     */
    private function createSale(
        string $cliente
    ): int {
        return DB::table('sales')
            ->insertGetId([
                'cliente' =>
                    $cliente,

                'total' => 0,

                'lucro' => 0,

                'status' =>
                    'completed',

                'created_at' =>
                    now(),

                'updated_at' =>
                    now(),
            ]);
    }

    /**
     * Processa itens
     * da venda.
     *
     * @param array<int, array<string, mixed>> $items
     * @param Collection<int, Product> $products
     *
     * @return array<string, float>
     */
    private function processItems(
        int $saleId,
        array $items,
        Collection $products
    ): array {
        $totalVenda = 0;
        $lucroTotal = 0;

        foreach (
            $items
            as
            $item
        ) {
            /** @var Product $product */
            $product =
                $products[
                    $item['id']
                ];

            $quantidade =
                (int) $item[
                    'quantidade'
                ];

            $precoVenda =
                (float) $item[
                    'preco_unitario'
                ];

            $subtotal =
                $quantidade
                *
                $precoVenda;

            $custoTotal =
                $quantidade
                *
                $product
                    ->custo_medio;

            $lucroItem =
                $subtotal
                -
                $custoTotal;

            $this->decreaseStock(
                $product,
                $quantidade
            );

            $this->createSaleItem(
                $saleId,
                $product,
                $quantidade,
                $precoVenda,
                $lucroItem,
                $subtotal
            );

            $totalVenda +=
                $subtotal;

            $lucroTotal +=
                $lucroItem;
        }

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
    }

    /**
     * Salva item
     * da venda.
     */
    private function createSaleItem(
        int $saleId,
        Product $product,
        int $quantidade,
        float $precoVenda,
        float $lucroItem,
        float $subtotal
    ): void {
        DB::table('sale_items')
            ->insert([
                'sale_id' =>
                    $saleId,

                'product_id' =>
                    $product->id,

                'quantidade' =>
                    $quantidade,

                'preco_unitario' =>
                    $precoVenda,

                'custo_unitario' =>
                    $product
                        ->custo_medio,

                'lucro_item' =>
                    round(
                        $lucroItem,
                        2
                    ),

                'subtotal' =>
                    round(
                        $subtotal,
                        2
                    ),

                'created_at' =>
                    now(),

                'updated_at' =>
                    now(),
            ]);
    }

    /**
     * Atualiza totais
     * da venda.
     *
     * @param array<string, float> $result
     */
    private function updateSaleTotals(
        int $saleId,
        array $result
    ): void {
        DB::table('sales')
            ->where(
                'id',
                $saleId
            )
            ->update([
                'total' =>
                    $result[
                        'total'
                    ],

                'lucro' =>
                    $result[
                        'lucro'
                    ],

                'updated_at' =>
                    now(),
            ]);
    }

    /**
     * Baixa estoque.
     */
    private function decreaseStock(
        Product $product,
        int $quantity
    ): void {
        $product->update([
            'estoque' =>
                $product->estoque
                -
                $quantity,
        ]);
    }

    /**
     * Devolve estoque.
     */
    private function increaseStock(
        Product $product,
        int $quantity
    ): void {
        $product->update([
            'estoque' =>
                $product->estoque
                +
                $quantity,
        ]);
    }
}
