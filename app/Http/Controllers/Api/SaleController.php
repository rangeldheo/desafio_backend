<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSaleRequest;
use App\Services\SaleService;
use Illuminate\Http\JsonResponse;
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
                'message' => 'Venda registrada com sucesso.',
                'data' => [
                    'total' => $result['total'],
                    'lucro' => $result['lucro'],
                ],
            ], 201);

        } catch (RuntimeException $exception) {

            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'data' => null,
            ], 422);
        }
    }
}
