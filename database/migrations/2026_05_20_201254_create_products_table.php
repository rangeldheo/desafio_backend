<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Cria a tabela de produtos do ERP.
     * Armazena dados básicos do produto,
     * estoque atual e custo médio ponderado.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('nome');

            $table->decimal('preco_venda', 10, 2);

            $table->decimal('custo_medio', 10, 2)
                ->default(0);

            $table->integer('estoque')
                ->default(0);

            $table->timestamps();
        });
    }

    /**
     * Remove a tabela de produtos.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
