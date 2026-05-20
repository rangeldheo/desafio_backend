<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSaleRequest;
use App\Services\SaleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Controller responsável pelo
 * registro de vendas do ERP.
 */
class SaleController extends Controller
{
    /**
     * @param SaleService $saleService
     */
    public function __construct(
        private readonly SaleService $saleService
    ) {
    }

    /**
     * Lista vendas.
     */
    public function index(): JsonResponse
    {
        $sales = DB::table('sales')
            ->orderByDesc('id')
            ->get()
            ->map(function ($sale) {

                $items = DB::table(
                    'sale_items'
                )
                    ->join(
                        'products',
                        'products.id',
                        '=',
                        'sale_items.product_id'
                    )
                    ->where(
                        'sale_id',
                        $sale->id
                    )
                    ->select([
                        'products.nome as produto',
                        'sale_items.quantidade',
                        'sale_items.preco_unitario',
                    ])
                    ->get();

                return [
                    'id' =>
                        $sale->id,

                    'cliente' =>
                        $sale->cliente,

                    'total' =>
                        $sale->total,

                    'lucro' =>
                        $sale->lucro,

                    'status' =>
                        $sale->status,

                    'produtos' =>
                        $items,

                    'created_at' =>
                        $sale->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'message' =>
                'Vendas listadas com sucesso.',
            'data' => $sales,
        ]);
    }

    /**
     * Registra uma venda.
     */
    public function store(
        StoreSaleRequest $request
    ): JsonResponse {
        try {
            $result = $this->saleService
                ->register(
                    $request->validated()
                );

            return response()->json([
                'success' => true,
                'message' =>
                    'Venda registrada com sucesso.',
                'data' => [
                    'total' =>
                        $result['total'],

                    'lucro' =>
                        $result['lucro'],
                ],
            ], 201);

        } catch (
            RuntimeException $exception
        ) {

            return response()->json([
                'success' => false,
                'message' =>
                    $exception->getMessage(),
                'data' => null,
            ], 422);
        }
    }
}
