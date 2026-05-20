<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Cria a tabela de compras.
     * Representa o cabeçalho da compra
     * realizada com fornecedor.
     */
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();

            $table->string('fornecedor');

            $table->timestamps();
        });
    }

    /**
     * Remove a tabela de compras.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
