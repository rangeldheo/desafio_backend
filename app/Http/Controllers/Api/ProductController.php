<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

/**
 * Controller responsável pelo gerenciamento
 * de produtos do ERP.
 */
class ProductController extends Controller
{
    /**
     * Lista os produtos cadastrados.
     */
    public function index(): JsonResponse
    {
        $products = Product::query()
            ->select([
                'id',
                'nome',
                'custo_medio',
                'preco_venda',
                'estoque',
            ])
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Produtos listados com sucesso.',
            'data' => $products,
        ]);
    }

    /**
     * Cadastra um novo produto.
     */
    public function store(
        StoreProductRequest $request
    ): JsonResponse {
        $product = Product::query()->create([
            'nome' => $request->nome,
            'preco_venda' => $request->preco_venda,
            'custo_medio' => 0,
            'estoque' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Produto criado com sucesso.',
            'data' => $product,
        ], 201);
    }
}
