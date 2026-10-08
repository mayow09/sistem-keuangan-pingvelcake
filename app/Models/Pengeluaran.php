<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran';
    protected $fillable = [
        'nomor_bill', 'tanggal', 'jenis', 'penjual', 'tujuan',
        'ongkir_ppn_disc', 'total', 'cp_offline',
    ];

    public function rincian()
    {
        return $this->hasMany(RincianPengeluaran::class, 'pengeluaran_id', 'id');
    }

    public function hitungTotalPengeluaran()
    {
        $total_subtotal = $this->rincian()->sum('subtotal');
        $this->total = $total_subtotal + ($this->ongkir_ppn_disc ?? 0);
        $this->save();
    }

}
