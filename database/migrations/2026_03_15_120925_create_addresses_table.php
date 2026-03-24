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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();

            // Связь с пользователем
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Адресные поля
            $table->string('country', 100);
            $table->string('city', 100);
            $table->string('street', 200);
            $table->string('house', 20);
            $table->string('apartment', 20)->nullable();

            // Флаг "адрес по умолчанию"
            $table->boolean('is_default')->default(false)->index();

            $table->timestamps();
            $table->softDeletes(); // Мягкое удаление

            // Составной индекс для поиска основных адресов пользователя
            $table->index(['user_id', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
