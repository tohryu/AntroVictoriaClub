<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cover_configuracion', function (Blueprint $table) {
            $table->boolean('entrada_libre')->default(false)->after('precio');
        });
    }

    public function down(): void
    {
        Schema::table('cover_configuracion', function (Blueprint $table) {
            $table->dropColumn('entrada_libre');
        });
    }
};
