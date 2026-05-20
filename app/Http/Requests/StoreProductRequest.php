<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request responsável pela validação
 * do cadastro de produtos.
 */
class StoreProductRequest extends FormRequest
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
            'nome' => [
                'required',
                'string',
                'min:3',
            ],

            'preco_venda' => [
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
            'nome.required' => 'O nome é obrigatório.',
            'nome.min' => 'O nome deve ter no mínimo 3 caracteres.',

            'preco_venda.required' => 'O preço de venda é obrigatório.',
            'preco_venda.numeric' => 'O preço de venda deve ser numérico.',
            'preco_venda.gt' => 'O preço de venda deve ser maior que zero.',
        ];
    }
}
