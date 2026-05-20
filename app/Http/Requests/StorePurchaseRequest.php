<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request responsável pela validação
 * do registro de compras.
 */
class StorePurchaseRequest extends FormRequest
{
    /**
     * Autoriza a request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'fornecedor' => [
                'required',
                'string',
                'min:3',
            ],

            'produtos' => [
                'required',
                'array',
                'min:1',
            ],

            'produtos.*.id' => [
                'required',
                'integer',
                'exists:products,id',
            ],

            'produtos.*.quantidade' => [
                'required',
                'integer',
                'gt:0',
            ],

            'produtos.*.preco_unitario' => [
                'required',
                'numeric',
                'gt:0',
            ],
        ];
    }

    /**
     * Mensagens personalizadas.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'fornecedor.required' =>
                'O fornecedor é obrigatório.',

            'fornecedor.min' =>
                'O fornecedor deve ter no mínimo 3 caracteres.',

            'produtos.required' =>
                'É necessário informar produtos.',

            'produtos.array' =>
                'Os produtos devem estar em formato de lista.',

            'produtos.min' =>
                'Informe pelo menos um produto.',

            'produtos.*.id.exists' =>
                'Um dos produtos informados não existe.',

            'produtos.*.quantidade.gt' =>
                'A quantidade deve ser maior que zero.',

            'produtos.*.preco_unitario.gt' =>
                'O preço unitário deve ser maior que zero.',
        ];
    }
}
