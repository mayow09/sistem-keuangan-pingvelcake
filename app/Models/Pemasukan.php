<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemasukan extends Model
{
    use HasFactory;

    protected $table = 'pemasukan';
    protected $fillable = [
        'nomor_invoice', 'tanggal', 'customer', 'no_hp', 'social_media',
        'pengiriman', 'sub_total', 'diskon', 'ongkir', 'total_transaksi', 'jadwal_pickup'
    ];
}
