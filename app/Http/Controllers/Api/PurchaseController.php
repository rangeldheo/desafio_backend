<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePurchaseRequest;
use App\Services\PurchaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * Controller responsável pelo
 * registro de compras do ERP.
 */
class PurchaseController extends Controller
{
    /**
     * @param PurchaseService $purchaseService
     */
    public function __construct(
        private readonly PurchaseService $purchaseService
    ) {
    }

    /**
     * Lista compras.
     */
    public function index(): JsonResponse
    {
        $purchases = DB::table('purchases')
            ->orderByDesc('id')
            ->get()
            ->map(function ($purchase) {

                $items = DB::table(
                    'purchase_items'
                )
                    ->join(
                        'products',
                        'products.id',
                        '=',
                        'purchase_items.product_id'
                    )
                    ->where(
                        'purchase_id',
                        $purchase->id
                    )
                    ->select([
                        'products.nome as produto',
                        'purchase_items.quantidade',
                        'purchase_items.preco_unitario',
                    ])
                    ->get();

                return [
                    'id' =>
                        $purchase->id,

                    'fornecedor' =>
                        $purchase->fornecedor,

                    'produtos' =>
                        $items,

                    'created_at' =>
                        $purchase->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'message' =>
                'Compras listadas com sucesso.',
            'data' => $purchases,
        ]);
    }

    /**
     * Registra uma compra.
     */
    public function store(
        StorePurchaseRequest $request
    ): JsonResponse {
        $this->purchaseService
            ->register(
                $request->validated()
            );

        return response()->json([
            'success' => true,
            'message' =>
                'Compra registrada com sucesso.',
            'data' => null,
        ], 201);
    }
}
