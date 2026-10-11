<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_harians', function (Blueprint $table) {
            $table->decimal('energi', 8, 3)->default(0)->comment('Energi dalam kkal')->change();
            $table->decimal('protein', 7, 3)->default(0)->comment('Protein dalam gram')->change();
            $table->decimal('lemak', 7, 3)->default(0)->comment('Lemak dalam gram')->change();
            $table->decimal('karbo', 7, 3)->default(0)->comment('Karbohidrat dalam gram')->change();
            $table->decimal('serat', 7, 3)->default(0)->comment('Serat dalam gram')->change();
        });
    }

    public function down(): void
    {
        Schema::table('menu_harians', function (Blueprint $table) {
            $table->decimal('energi', 7, 2)->default(0)->comment('Energi dalam kkal')->change();
            $table->decimal('protein', 6, 2)->default(0)->comment('Protein dalam gram')->change();
            $table->decimal('lemak', 6, 2)->default(0)->comment('Lemak dalam gram')->change();
            $table->decimal('karbo', 6, 2)->default(0)->comment('Karbohidrat dalam gram')->change();
            $table->decimal('serat', 6, 2)->default(0)->comment('Serat dalam gram')->change();
        });
    }
};