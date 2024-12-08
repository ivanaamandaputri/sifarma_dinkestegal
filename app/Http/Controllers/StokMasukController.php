<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\StokMasuk;
use Illuminate\Http\Request;

class StokMasukController extends Controller
{
    public function tambahStok(Request $request)
    {
        $request->validate([
            'obat_id' => 'required|exists:obat,id',
            'jumlah' => 'required|integer|min:1',
            'sumber' => 'nullable|string|max:255',
            'tanggal' => 'required|date',
        ]);

        // Tambah stok ke tabel stok_masuk
        StokMasuk::create([
            'obat_id' => $request->obat_id,
            'jumlah' => $request->jumlah,
            'sumber' => $request->sumber,
            'tanggal' => $request->tanggal,
        ]);

        // Update stok terkini di tabel obat
        $obat = Obat::find($request->obat_id);
        $obat->stok += $request->jumlah;
        $obat->save();

        return back()->with('success', 'Stok berhasil ditambahkan.');
    }
}
