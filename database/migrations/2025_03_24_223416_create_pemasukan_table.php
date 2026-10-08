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
        Schema::create('pemasukan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_invoice')->unique();
            $table->date('tanggal');
            $table->string('customer');
            $table->string('no_hp');
            $table->string('social_media');
            $table->enum('pengiriman', ['paxel', 'self pickup', 'travel', 'lainnya']);
            $table->decimal('sub_total', 15, 2);
            $table->decimal('diskon', 15, 2)->default(0);
            $table->decimal('ongkir', 15, 2)->default(0);
            $table->decimal('total_transaksi', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemasukan');
    }
};
