<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Добавляем недостающие поля
            $table->string('order_number')->unique()->after('id');
            $table->integer('total_quantity')->after('total');
            $table->foreignId('address_id')->nullable()->after('user_id')
                ->constrained('addresses')->nullOnDelete();
            $table->string('payment_method')->default('card')->after('status');
            $table->string('payment_status')->default('pending')->after('payment_method');
            $table->string('recipient_name')->after('payment_status');
            $table->string('recipient_phone')->after('recipient_name');
            $table->text('comment')->nullable()->after('shipping_address');

            $table->renameColumn('total', 'total_amount');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'order_number',
                'total_quantity',
                'address_id',
                'payment_method',
                'payment_status',
                'recipient_name',
                'recipient_phone',
                'comment'
            ]);
            $table->renameColumn('total_amount', 'total');
        });
    }
};
