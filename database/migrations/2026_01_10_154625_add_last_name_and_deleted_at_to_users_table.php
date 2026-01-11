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
        Schema::table('users', function (Blueprint $table) {
            // Добавляем фамилию после имени
            $table->string('last_name')
                ->nullable()
                ->after('first_name')
                ->comment('Фамилия пользователя');

            // Добавляем колонку для мягкого удаления
            $table->timestamp('deleted_at')
                ->nullable()
                ->after('updated_at')
                ->comment('Дата и время мягкого удаления');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['last_name', 'deleted_at']);
        });
    }
};
