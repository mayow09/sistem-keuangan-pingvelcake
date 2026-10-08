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
        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_bill')->unique();
            $table->date('tanggal');
            $table->enum('jenis', ['ecommerce', 'offline store']);
            $table->string('penjual');
            $table->string('tujuan');
            $table->text('rincian_pesanan');
            $table->decimal('subtotal', 15, 2);
            $table->decimal('ongkir_ppn_disc', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluaran');
    }
};
