<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\StokMasuk;
use Illuminate\Http\Request;

class StokMasukController extends Controller
{
    public function tambahStok(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1',
            'sumber' => 'nullable|string|max:255',
            'tanggal' => 'required|date',
        ]);

        $obat = Obat::find($id);
        if (!$obat) {
            return back()->with('error', 'Obat tidak ditemukan.');
        }

        StokMasuk::create([
            'obat_id' => $obat->id,
            'jumlah' => $request->jumlah,
            'sumber' => $request->sumber,
            'tanggal' => $request->tanggal,
        ]);

        $obat->stok += $request->jumlah;
        $obat->save();

        return back()->with('success', "Stok obat {$obat->nama_obat} berhasil ditambahkan {$request->jumlah}.");
    }
}
