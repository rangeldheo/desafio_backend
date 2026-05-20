<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePurchaseRequest;
use App\Services\PurchaseService;
use Illuminate\Http\JsonResponse;

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
     * Registra uma compra.
     */
    public function store(
        StorePurchaseRequest $request
    ): JsonResponse {
        $this->purchaseService->register(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Compra registrada com sucesso.',
            'data' => null,
        ], 201);
    }
}
