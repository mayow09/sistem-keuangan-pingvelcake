<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menyelaraskan skema database dengan form dan validasi aplikasi:
     * - Rincian barang kini disimpan di tabel rincian_pengeluaran, sehingga kolom lama
     *   `rincian_pesanan` dan `subtotal` di tabel pengeluaran dibuat opsional.
     * - Pilihan pengiriman di form (Delivery, GoSend, GrabExpress, dll.) tidak semuanya
     *   ada di ENUM awal, sehingga kolom `pengiriman` diubah menjadi string.
     * - Kolom biaya tambahan yang di validasi bersifat opsional (diskon, ongkir,
     *   ongkir/PPN/diskon) dibuat boleh kosong dengan nilai bawaan 0.
     */
    public function up(): void
    {
        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->text('rincian_pesanan')->nullable()->change();
            $table->decimal('subtotal', 15, 2)->default(0)->change();
            $table->decimal('ongkir_ppn_disc', 15, 2)->nullable()->default(0)->change();
        });

        Schema::table('pemasukan', function (Blueprint $table) {
            $table->string('pengiriman')->change();
            $table->decimal('diskon', 15, 2)->nullable()->default(0)->change();
            $table->decimal('ongkir', 15, 2)->nullable()->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('pemasukan', function (Blueprint $table) {
            $table->enum('pengiriman', ['paxel', 'self pickup', 'travel', 'lainnya'])->change();
            $table->decimal('diskon', 15, 2)->default(0)->change();
            $table->decimal('ongkir', 15, 2)->default(0)->change();
        });

        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->text('rincian_pesanan')->change();
            $table->decimal('subtotal', 15, 2)->change();
            $table->decimal('ongkir_ppn_disc', 15, 2)->default(0)->change();
        });
    }
};
