<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')
                ->nullable()
                ->after('id')
                ->constrained('roles')
                ->nullOnDelete();
        });

        // Перенос ролей из role_user в users.role_id
        if (Schema::hasTable('role_user')) {
            DB::statement("
                UPDATE users
                SET role_id = role_user.role_id
                FROM role_user
                WHERE users.id = role_user.user_id
            ");
        }

        // Назначить роль user тем, у кого role_id пустой
        $userRoleId = DB::table('roles')
            ->where('slug', Role::ROLE_USER)
            ->value('id');

        if ($userRoleId) {
            DB::table('users')
                ->whereNull('role_id')
                ->update(['role_id' => $userRoleId]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
        });
    }
};
