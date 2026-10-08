<?php

use App\Http\Controllers\CashflowController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\RekapController;
use App\Models\Pemasukan;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

// PEMASUKAN
Route::get('/', [PemasukanController::class, 'index'])->name('pemasukan.index');

Route::get('/pemasukan', [PemasukanController::class, 'index'])->name('pemasukan.index');
Route::get('/pemasukan/create', [PemasukanController::class, 'create'])->name('pemasukan.create');
Route::post('/pemasukan', [PemasukanController::class, 'store'])->name('pemasukan.store');
Route::put('/pemasukan/{id}', [PemasukanController::class, 'update'])->name('pemasukan.update');
Route::delete('/pemasukan/{id}', [PemasukanController::class, 'destroy'])->name('pemasukan.destroy');

Route::get('/get-latest-invoice/{bulan}/{tahun}', function ($bulan, $tahun) {
    $latestInvoice = Pemasukan::whereMonth('tanggal', $bulan)
        ->whereYear('tanggal', "20$tahun")
        ->count();

    return response()->json(['latest' => $latestInvoice]);
});


Route::get('/pemasukan/report', [PemasukanController::class, 'generateReport'])->name('pemasukan.report');


// PENGELUARAN
Route::get('/pengeluaran', [PengeluaranController::class, 'index'])->name('pengeluaran.index');
Route::post('/pengeluaran', [PengeluaranController::class, 'store'])->name('pengeluaran.store');
Route::get('/pengeluaran/{id}', [PengeluaranController::class, 'show'])->name('pengeluaran.show');
Route::get('/pengeluaran/{id}/detail', [PengeluaranController::class, 'getDetail']);
Route::put('/pengeluaran/{id}', [PengeluaranController::class, 'update'])->name('pengeluaran.update');
Route::delete('/pengeluaran/{id}', [PengeluaranController::class, 'destroy'])->name('pengeluaran.destroy');

// REKAP
Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');
Route::get('/rekap/generate', [RekapController::class, 'generateRekap'])->name('rekap.generate');

Route::get('/cashflow', [CashflowController::class, 'index'])->name('cashflow.index');
Route::get('/cashflow/export', [CashflowController::class, 'exportExcel'])->name('cashflow.export');
