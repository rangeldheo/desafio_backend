<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Cria a tabela de itens da venda.
     * Armazena snapshot financeiro do momento da venda.
     */
    public function up(): void
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sale_id')
                ->constrained('sales')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            $table->integer('quantidade');

            $table->decimal('preco_unitario', 10, 2);

            $table->decimal('custo_unitario', 10, 2);

            $table->decimal('lucro_item', 10, 2);

            $table->decimal('subtotal', 10, 2);

            $table->timestamps();
        });
    }

    /**
     * Remove a tabela de itens de venda.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
