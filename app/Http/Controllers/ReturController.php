<?php

namespace App\Http\Controllers;

use App\Models\Retur;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ReturController extends Controller
{

    public function index($transaksiId = null)
    {
        if ($transaksiId) {
            // Mengambil semua retur untuk transaksi tertentu
            $returs = Retur::where('transaksi_id', $transaksiId)->get();
        } else {
            $returs = Retur::all(); // Mengambil semua data retur jika tidak ada ID transaksi
        }

        return view('retur.index', compact('returs', 'transaksiId')); // Mengirim data ke view
    }


    public function create()
    {
        return view('retur.create'); // Menampilkan form untuk menambah retur
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaksi_id' => 'required|exists:transaksi,id',
            'obat_id' => 'required|exists:obat,id',
            'jenis_obat_id' => 'required|exists:jenis_obat,id',
            'jumlah' => 'required|integer|min:1',
            'alasan_retur' => 'nullable|string',
            'password' => 'required|string',
        ]);

        // Verifikasi password
        if (Auth::user()->password !== $validated['password']) {
            return back()->withErrors(['password' => 'Password tidak valid']);
        }

        // Simpan retur
        $retur = new Retur();
        $retur->transaksi_id = $validated['transaksi_id'];
        $retur->obat_id = $validated['obat_id'];
        $retur->jenis_obat_id = $validated['jenis_obat_id'];
        $retur->user_id = Auth::id();
        $retur->jumlah = $validated['jumlah'];
        $retur->alasan_retur = $validated['alasan_retur'];
        $retur->status = 'Diretur';  // Status retur
        $retur->save();

        // Update status transaksi menjadi 'Selesai' setelah retur
        $transaksi = Transaksi::find($validated['transaksi_id']);
        $transaksi->status = 'Selesai';
        $transaksi->save();

        return redirect()->route('transaksi.index')->with('success', 'Retur berhasil diproses dan transaksi selesai.');
    }

    public function show(Retur $retur)
    {
        return view('retur.show', compact('retur')); // Menampilkan detail retur
    }


    public function edit(Retur $retur)
    {
        return view('retur.edit', compact('retur')); // Menampilkan form untuk mengedit retur
    }


    public function update(Request $request, Retur $retur)
    {
        // Validasi data
        $request->validate([
            'transaksi_id' => 'required|exists:transaksi,id',
            'obat_id' => 'required|exists:obat,id',
            'user_id' => 'required|exists:user,id',
            'jumlah' => 'required|integer',
            'alasan_retur' => 'nullable|string',
            'status' => 'nullable|string',
        ]);


        // Update data
        $retur->update($request->all());

        return redirect()->route('retur.index')->with('success', 'Data berhasil diperbarui.');
    }


    public function destroy(Retur $retur)
    {
        $retur->delete(); // Menghapus data retur
        return redirect()->route('retur.index')->with('success', 'Retur berhasil dihapus.');
    }
}
