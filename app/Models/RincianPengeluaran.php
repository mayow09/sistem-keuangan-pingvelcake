<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RincianPengeluaran extends Model
{
    use HasFactory;

    protected $table = 'rincian_pengeluaran';

    protected $fillable = ['pengeluaran_id', 'nama_barang', 'quantity', 'subtotal'];

    public function pengeluaran()
    {
        return $this->belongsTo(Pengeluaran::class, 'pengeluaran_id', 'id'); 
    }


    protected static function boot()
    {
        parent::boot();

        static::saved(function ($rincian) {
            if ($rincian->pengeluaran) {
                $rincian->pengeluaran->hitungTotalPengeluaran();
            }
        });

        static::deleted(function ($rincian) {
            if ($rincian->pengeluaran) {
                $rincian->pengeluaran->hitungTotalPengeluaran();
            }
        });
    }

}
