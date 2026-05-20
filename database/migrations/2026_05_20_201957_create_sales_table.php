<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Cria a tabela de vendas.
     * Armazena o cabeçalho da venda,
     * valor total, lucro e status.
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            $table->string('cliente');

            $table->decimal('total', 10, 2)
                ->default(0);

            $table->decimal('lucro', 10, 2)
                ->default(0);

            $table->enum('status', [
                'completed',
                'cancelled',
            ])->default('completed');

            $table->timestamps();
        });
    }

    /**
     * Remove a tabela de vendas.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
