<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\RincianPengeluaran;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class PengeluaranController extends Controller
{

    public function index()
    {
        $pengeluaran = Pengeluaran::all();
        return view('pengeluaran.index', compact('pengeluaran'));
    }

    public function getDetail($id)
    {
        try {
            $pengeluaran = Pengeluaran::with('rincian')->findOrFail($id);

            if ($pengeluaran->rincian->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data rincian pengeluaran tidak ditemukan untuk pengeluaran ID: ' . $id
                ], 404);
            }

            return response()->json([
                'success' => true,
                'pengeluaran' => $pengeluaran,
                'rincian' => $pengeluaran->rincian
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {

            $data = $request->validate([
                'nomor_bill' => 'required|unique:pengeluaran',
                'tanggal' => 'required|date',
                'jenis' => 'required',
                'cp_offline' => 'nullable',
                'penjual' => 'required',
                'tujuan' => 'required',
                'ongkir_ppn_disc' => 'nullable|numeric',
                'rincian.*.nama_barang' => 'required',
                'rincian.*.quantity' => 'required|integer|min:1',
                'rincian.*.subtotal' => 'required|numeric|min:0',
                'total' => 'required|numeric',
            ]);

            Log::info('Data yang akan disimpan ke pengeluaran:', $request->only([
                'nomor_bill', 'tanggal', 'jenis', 'penjual', 'tujuan', 'ongkir_ppn_disc', 'total'
            ]));

            $pengeluaran = Pengeluaran::create($request->only([
                'nomor_bill', 'tanggal', 'jenis', 'penjual', 'tujuan', 'ongkir_ppn_disc', 'total'
            ]));

            if (!$pengeluaran) {
                return response()->json(['success' => false, 'message' => 'Gagal menyimpan pengeluaran.']);
            }

            foreach ($request->rincian as $index => $rincian) {
                Log::info("Data rincian ke-$index yang akan disimpan:", $rincian);
            }

            foreach ($request->rincian as $rincian) {
                $rincianPengeluaran = RincianPengeluaran::create([
                    'pengeluaran_id' => $pengeluaran->id,
                    'nama_barang' => $rincian['nama_barang'],
                    'quantity' => $rincian['quantity'],
                    'subtotal' => $rincian['subtotal']
                ]);

                if (!$rincianPengeluaran) {
                    return response()->json(['success' => false, 'message' => 'Gagal menyimpan rincian pengeluaran.']);
                }
            }

            return redirect()->route('pengeluaran.index')->with('success', 'Pengeluaran berhasil ditambahkan.');

        } catch (\Exception $e) {
            Log::error('Error saat menyimpan data: ' . $e->getMessage());
            return redirect()->route('pengeluaran.index')->with('success', 'Pengeluaran berhasil ditambahkan.');
        }
    }

    public function show($id)
    {
        $pengeluaran = Pengeluaran::with('rincian')->findOrFail($id);
        return response()->json($pengeluaran);
    }

    // public function update(Request $request, $id)
    // {
    //     $pengeluaran = Pengeluaran::findOrFail($id);

    //     $pengeluaran->rincian()->delete();

    //     if ($request->rincian) {
    //         $total = 0;

    //         foreach ($request->rincian as $rincian) {
    //             $total += $rincian['subtotal'];

    //             $pengeluaran->rincian()->create([
    //                 'nama_barang' => $rincian['nama_barang'],
    //                 'quantity' => $rincian['quantity'],
    //                 'subtotal' => $rincian['subtotal'],
    //             ]);
    //         }

    //         $total += $request->ongkir_ppn_disc;

    //         $pengeluaran->update([
    //             'nomor_bill' => $request->nomor_bill,
    //             'tanggal' => $request->tanggal,
    //             'jenis' => $request->jenis,
    //             'penjual' => $request->penjual,
    //             'tujuan' => $request->tujuan,
    //             'ongkir_ppn_disc' => $request->ongkir_ppn_disc,
    //             'total' => $total,
    //         ]);
    //     }

    //     return redirect()->route('pengeluaran.index')->with('success', 'Pengeluaran berhasil diperbarui!');
    // }

    public function update(Request $request, $id)
{
    $data = $request->validate([
        'nomor_bill' => 'required',
        'tanggal' => 'required|date',
        'jenis' => 'required',
        'penjual' => 'required',
        'tujuan' => 'required',
        'ongkir_ppn_disc' => 'nullable|numeric',
        'total' => 'required|numeric',
        'rincian' => 'nullable|array',
        'rincian.*.id' => 'nullable|integer', // tambahkan id rincian jika ada
        'rincian.*.nama_barang' => 'required|string',
        'rincian.*.quantity' => 'required|integer|min:1',
        'rincian.*.subtotal' => 'required|numeric|min:0',
    ]);

    $pengeluaran = Pengeluaran::findOrFail($id);
    $pengeluaran->update($request->only(['nomor_bill', 'tanggal', 'jenis', 'penjual', 'tujuan', 'ongkir_ppn_disc', 'total']));

    $existingRincianIDs = [];

    if (!empty($data['rincian'])) {
        foreach ($data['rincian'] as $rincian) {
            if (!empty($rincian['id'])) {
                // Update rincian lama
                $r = RincianPengeluaran::find($rincian['id']);
                if ($r && $r->pengeluaran_id == $pengeluaran->id) {
                    $r->update([
                        'nama_barang' => $rincian['nama_barang'],
                        'quantity' => $rincian['quantity'],
                        'subtotal' => $rincian['subtotal']
                    ]);
                    $existingRincianIDs[] = $r->id;
                }
            } else {
                // Tambah rincian baru
                $new = RincianPengeluaran::create([
                    'pengeluaran_id' => $pengeluaran->id,
                    'nama_barang' => $rincian['nama_barang'],
                    'quantity' => $rincian['quantity'],
                    'subtotal' => $rincian['subtotal']
                ]);
                $existingRincianIDs[] = $new->id;
            }
        }

        // Hapus rincian yang tidak dikirim lagi (yang berarti dihapus oleh user)
        RincianPengeluaran::where('pengeluaran_id', $pengeluaran->id)
            ->whereNotIn('id', $existingRincianIDs)
            ->delete();
    }

    return redirect()->route('pengeluaran.index')->with('success', 'Pengeluaran berhasil diperbarui.');
}




    // public function update(Request $request, $id)
    // {
    //     $data = $request->validate([
    //         'nomor_bill' => 'required',
    //         'tanggal' => 'required|date',
    //         'jenis' => 'required',
    //         'penjual' => 'required',
    //         'tujuan' => 'required',
    //         'ongkir_ppn_disc' => 'nullable|numeric',
    //         'rincian.*.nama_barang' => 'required',
    //         'rincian.*.quantity' => 'required|integer|min:1',
    //         'rincian.*.subtotal' => 'required|numeric|min:0',
    //         'total' => 'required|numeric',
    //     ]);

    //     $pengeluaran = Pengeluaran::findOrFail($id);
    //     $pengeluaran->update($request->only(['nomor_bill', 'tanggal', 'jenis', 'penjual', 'tujuan', 'ongkir_ppn_disc', 'total']));
    //     $pengeluaran->rincian()->delete();

    //     foreach ($request->rincian as $rincian) {
    //         RincianPengeluaran::create([
    //             'pengeluaran_id' => $pengeluaran->id,
    //             'nama_barang' => $rincian['nama_barang'],
    //             'quantity' => $rincian['quantity'],
    //             'subtotal' => $rincian['subtotal']
    //         ]);
    //     }

    //     return redirect()->route('pengeluaran.index')->with('success', 'Pengeluaran berhasil diperbarui.');
    // }

    public function destroy($id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        $pengeluaran->rincian()->delete();
        $pengeluaran->delete();
        return redirect()->route('pengeluaran.index')->with('success', 'Pengeluaran berhasil dihapus.');
    }

}
