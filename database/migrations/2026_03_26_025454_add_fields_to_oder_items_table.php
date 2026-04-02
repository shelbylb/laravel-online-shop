<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Добавляем недостающие поля
            $table->string('product_name')->after('product_id');
            $table->decimal('subtotal', 12, 2)->after('price');
            $table->renameColumn('price', 'product_price');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['product_name', 'subtotal']);
            $table->renameColumn('product_price', 'price');
        });
    }
};
