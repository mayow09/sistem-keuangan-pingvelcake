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
        Schema::create('rincian_pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pengeluaran_id'); // Pastikan kolom ini ada sebelum foreign key
            $table->foreign('pengeluaran_id')->references('id')->on('pengeluaran')->onDelete('cascade');
            $table->string('nama_barang');
            $table->integer('quantity');
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rincian_pengeluaran');
    }
};
