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
        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->dropColumn('tujuan');
        });

        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->enum('tujuan', [
                'Restock Bahan',
                'Restock Packaging',
                'Restock Decorative',
                'Pengadaan',
                'RnD',
                'Promote',
                'WFC',
                'Reward Staf',
                'Diskon Pelanggan',
                'Operasional',
            ])->after('penjual');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->dropColumn('tujuan');
        });

        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->string('tujuan')->after('penjual');
        });
    }
};
