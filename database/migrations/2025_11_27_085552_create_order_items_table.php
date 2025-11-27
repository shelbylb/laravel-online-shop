<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id()->comment('Первичный ключ позиции заказа');
            $table->foreignId('order_id')->constrained()->onDelete('cascade')->comment('Связь с заказом');
            $table->foreignId('product_id')->constrained()->onDelete('cascade')->comment('Связь с продуктом');
            $table->integer('quantity')->comment('Количество товара');
            $table->decimal('price', 12, 2)->comment('Цена за единицу товара на момент покупки');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
