<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\Pemasukan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PemasukanController extends Controller
{
    // public function index()
    // {
    //     $pemasukan = Pemasukan::all();
    //     return view('pemasukan.index', compact('pemasukan'));
    // }

    public function index()
    {
        $pemasukan = Pemasukan::orderBy('tanggal', 'desc')->paginate(30);
        return view('pemasukan.index', compact('pemasukan'));
    }

    public function show($id)
    {
        $pemasukan = Pemasukan::findOrFail($id);
        return view('pemasukan.show', compact('pemasukan'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nomor_invoice' => 'required|unique:pemasukan,nomor_invoice',
            'tanggal' => 'required|date',
            'customer' => 'required|string|max:255',
            'no_hp' => 'required|string|max:15',
            'social_media' => 'nullable|string|max:255',
            'pengiriman' => 'required',
            'sub_total' => 'required|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0',
            'ongkir' => 'nullable|numeric|min:0',
            'total_transaksi' => 'required|numeric|min:0',
            'jadwal_pickup' => 'nullable|date',
        ]);

        try {
            Pemasukan::create($data);
            return redirect()->route('pemasukan.index')->with('success', 'Pemasukan berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error saat menyimpan pemasukan:', ['error' => $e->getMessage()]);
            return back()->withErrors('Gagal menambahkan pemasukan, cek kembali data yang diinput.');
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nomor_invoice' => 'required|unique:pemasukan,nomor_invoice,' . $id,
            'tanggal' => 'required|date',
            'customer' => 'required|string|max:255',
            'no_hp' => 'required|string|max:15',
            'social_media' => 'nullable|string|max:255',
            'pengiriman' => 'required',
            'sub_total' => 'required|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0',
            'ongkir' => 'nullable|numeric|min:0',
            'total_transaksi' => 'required|numeric|min:0',
            'jadwal_pickup' => 'nullable|date',
        ]);

        try {
            $pemasukan = Pemasukan::findOrFail($id);
            $pemasukan->update($data);
            return redirect()->route('pemasukan.index')->with('success', 'Pemasukan berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error saat memperbarui pemasukan:', ['error' => $e->getMessage()]);
            return back()->withErrors('Gagal memperbarui pemasukan, cek kembali data yang diinput.');
        }
    }

    public function destroy($id)
    {
        $pemasukan = Pemasukan::findOrFail($id);
        $pemasukan->delete();
        return redirect()->route('pemasukan.index')->with('success', 'Pemasukan berhasil dihapus.');
    }

    public function generateInvoiceNumber()
    {
        $currentMonth = Carbon::now()->format('m');
        $currentYear = Carbon::now()->format('y');

        $latestInvoice = Pemasukan::whereMonth('tanggal', Carbon::now()->month)
                                ->whereYear('tanggal', Carbon::now()->year)
                                ->count() + 1;

        return str_pad($latestInvoice, 2, '0', STR_PAD_LEFT) . $currentMonth . $currentYear;
    }

    public function generateReport()
    {
        $pemasukan = Pemasukan::all();
        $pdf = Pdf::loadView('pemasukan.report', compact('pemasukan'));
        return $pdf->download('laporan_pemasukan.pdf');
    }
}
