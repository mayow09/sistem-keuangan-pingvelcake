<?php

namespace App\Http\Controllers;

use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\Rekap;
use Illuminate\Http\Request;

class RekapController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Rekap::truncate();

        $bulanPemasukan = Pemasukan::selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as bulan")
            ->groupBy('bulan')
            ->pluck('bulan');

        $bulanPengeluaran = Pengeluaran::selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as bulan")
            ->groupBy('bulan')
            ->pluck('bulan');

        $semuaBulan = $bulanPemasukan->merge($bulanPengeluaran)->unique();

        foreach ($semuaBulan as $bulan) {
            $totalPemasukan = Pemasukan::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan])
                ->sum('sub_total');

            $totalPengeluaran = Pengeluaran::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan])
                ->sum('total');

            $margin = $totalPemasukan - $totalPengeluaran;

            Rekap::create([
                'bulan' => $bulan,
                'pemasukan' => $totalPemasukan,
                'pengeluaran' => $totalPengeluaran,
                'margin' => $margin,
            ]);
        }

        $filterBulan = $request->bulan;
        $rekap = Rekap::orderBy('bulan', 'desc');
        if ($filterBulan) {
            $rekap->where('bulan', $filterBulan);
        }

        return view('rekap.index', [
            'rekap' => $rekap->get(),
            'filterBulan' => $filterBulan,
        ]);
    }



    private function generateAllRekap()
    {
        $bulanPemasukan = Pemasukan::selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as bulan")
            ->groupBy('bulan')->pluck('bulan');

        $bulanPengeluaran = Pengeluaran::selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as bulan")
            ->groupBy('bulan')->pluck('bulan');

        $semuaBulan = $bulanPemasukan->merge($bulanPengeluaran)->unique();

        foreach ($semuaBulan as $bulan) {
            $totalPemasukan = Pemasukan::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan])
                ->sum('sub_total');
            $totalPengeluaran = Pengeluaran::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan])
                ->sum('total');
            $margin = $totalPemasukan - $totalPengeluaran;

            Rekap::updateOrCreate(
                ['bulan' => $bulan],
                ['pemasukan' => $totalPemasukan, 'pengeluaran' => $totalPengeluaran, 'margin' => $margin]
            );
        }
    }



    public function generateRekap()
    {
        $bulan = date('Y-m');

        $totalPemasukan = Pemasukan::whereMonth('tanggal', date('m'))
            ->whereYear('tanggal', date('Y'))
            ->sum('sub_total');

        $totalPengeluaran = Pengeluaran::whereMonth('tanggal', date('m'))
            ->whereYear('tanggal', date('Y'))
            ->sum('total');

        $margin = $totalPemasukan - $totalPengeluaran;

        $rekap = Rekap::updateOrCreate(
            ['bulan' => $bulan],
            ['pemasukan' => $totalPemasukan, 'pengeluaran' => $totalPengeluaran, 'margin' => $margin]
        );

        return redirect()->route('rekap.index')->with('success', 'Rekap Bulanan diperbarui!');
    }

    // public function index()
    // {
    //     $rekap = Rekap::all();
    //     return view('rekap.index', compact('rekap'));
    // }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
