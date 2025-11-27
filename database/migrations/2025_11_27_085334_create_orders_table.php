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
        // ENUM тип для статуса заказа
        DB::statement("CREATE TYPE order_status AS ENUM ('pending', 'paid', 'shipped', 'completed', 'canceled')");

        Schema::create('orders', function (Blueprint $table) {
            $table->id()->comment('Первичный ключ заказа');
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('Покупатель');
            $table->decimal('total', 12, 2)->comment('Общая сумма заказа');
            $table->enum('status', ['pending', 'paid', 'shipped', 'completed', 'canceled'])
                ->default('pending')
                ->comment('Статус заказа');
            $table->text('shipping_address')->nullable()->comment('Адрес доставки');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
