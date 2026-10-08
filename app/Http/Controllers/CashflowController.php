<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CashflowExport;

class CashflowController extends Controller
{

    public function index(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->endOfMonth()->toDateString());

        $pengeluaran = DB::table('pengeluaran')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->select(
                'tanggal',
                DB::raw("'Pengeluaran' as tipe"),
                'penjual as keterangan',
                'total as jumlah'
            )
            ->get();

        $pemasukan = DB::table('pemasukan')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->select(
                'tanggal',
                DB::raw("'Pemasukan' as tipe"),
                DB::raw("CONCAT(customer, ' (', social_media, ')') as keterangan"),
                'total_transaksi as jumlah'
            )
            ->get();

        $cashflows = $pemasukan->merge($pengeluaran)->sortBy('tanggal');

        return view('cashflow.index', compact('cashflows', 'startDate', 'endDate'));
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->endOfMonth()->toDateString());

        $pengeluaran = DB::table('pengeluaran')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->select('tanggal', DB::raw("'Pengeluaran' as tipe"), 'penjual as keterangan', 'total as jumlah')
            ->get();

        $pemasukan = DB::table('pemasukan')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->select('tanggal', DB::raw("'Pemasukan' as tipe"), DB::raw("CONCAT(customer, ' (', social_media, ')') as keterangan"), 'total_transaksi as jumlah')
            ->get();

        $cashflows = $pemasukan->merge($pengeluaran)->sortBy('tanggal');

        return Excel::download(new CashflowExport($cashflows, $startDate, $endDate), 'cashflow.xlsx');
    }


}
