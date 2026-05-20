<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model responsável pelos produtos do ERP.
 * Armazena estoque, custo médio e preço de venda.
 */
class Product extends Model
{
    /**
     * Campos permitidos para atribuição em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'preco_venda',
        'custo_medio',
        'estoque',
    ];

    /**
     * Conversões de tipos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'preco_venda' => 'decimal:2',
        'custo_medio' => 'decimal:2',
        'estoque' => 'integer',
    ];
}
