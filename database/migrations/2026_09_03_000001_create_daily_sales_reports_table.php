<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_sales_reports', function (Blueprint $table) {
            $table->id();

            $table->date('report_date')->unique();

            $table->unsignedInteger('orders_count')->default(0);
            $table->unsignedInteger('sales_count')->default(0);
            $table->decimal('revenue', 12, 2)->default(0);
            $table->unsignedInteger('canceled_count')->default(0);

            $table->timestamp('calculated_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_sales_reports');
    }
};
