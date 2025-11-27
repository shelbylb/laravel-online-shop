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
        Schema::create('users', function (Blueprint $table) {
            $table->id()->comment('Первичный ключ пользователя');
            $table->string('first_name')->comment('Имя пользователя');
            $table->string('email')->unique()->comment('Уникальный адрес электронной почты');
            $table->string('password')->comment('Хэшированный пароль пользователя');
            $table->string('avatar')->nullable()->comment('Ссылка на аватар пользователя');
            $table->string('phone', 20)->nullable()->comment('Номер телефона пользователя');
            $table->timestamps(); // created_at и updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
