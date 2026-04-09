<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku')->nullable()->after('stock');
            $table->string('status')->default(Product::STATUS_ACTIVE)->after('sku');
        });

        DB::table('products')
            ->whereNull('sku')
            ->orderBy('id')
            ->get()
            ->each(function ($product) {
                DB::table('products')
                    ->where('id', $product->id)
                    ->update([
                        'sku' => 'SKU-' . $product->id,
                    ]);
            });

        Schema::table('products', function (Blueprint $table) {
            $table->string('sku')->nullable(false)->change();
            $table->unique('sku');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['sku']);
            $table->dropColumn(['sku', 'status']);
        });
    }
};
