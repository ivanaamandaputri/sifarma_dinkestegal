<?php

namespace App\Http\Controllers;

use App\Models\JenisObat;
use Illuminate\Http\Request;

class JenisObatController extends Controller
{
    // Menampilkan daftar jenis obat
    public function index()
    {
        // Mengambil semua data jenis obat dan mengurutkan berdasarkan 'name' secara ascending
        $jenisObat = JenisObat::orderBy('nama_jenis', 'asc')->get();

        // Mengembalikan view dengan data jenis obat
        return view('jenis.index', compact('jenisObat'));
    }

    // Menampilkan form untuk membuat jenis obat baru
    public function create()
    {
        // Mengembalikan view untuk membuat jenis obat
        return view('jenis.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jenis' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Za-z\s]+$/',
                'unique:jenis_obat,nama_jenis'
            ],
        ], [
            'nama_jenis.required' => 'Jenis obat harus diisi.',
            'nama_jenis.regex' => 'Jenis obat hanya boleh huruf dan spasi.',
            'nama_jenis.unique' => 'Jenis obat sudah digunakan.',
            'nama_jenis.max' => 'Jenis obat terlalu panjang.'
        ]);

        $jenisObat = JenisObat::create($request->all());

        return redirect()->route('jenis_obat.index')->with('success', $jenisObat->nama_jenis . ' berhasil ditambahkan');
    }



    // Method edit
    public function edit($id)
    {
        $jenisObat = JenisObat::findOrFail($id);
        return view('jenis.edit', compact('jenisObat'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_jenis' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Za-z\s]+$/',
                'unique:jenis_obat,nama_jenis,' . $id
            ],
        ], [
            'nama_jenis.required' => 'Jenis obat harus diisi.',
            'nama_jenis.regex' => 'Jenis obat hanya boleh huruf dan spasi.',
            'nama_jenis.unique' => 'Jenis obat sudah digunakan.',
            'nama_jenis.max' => 'Jenis obat terlalu panjang.'
        ]);

        $jenisObat = JenisObat::findOrFail($id);
        $jenisObat->update($request->all());

        return redirect()->route('jenis_obat.index')->with('success', $jenisObat->nama_jenis . ' berhasil diperbarui');
    }


    public function destroy(JenisObat $jenisObat)
    {
        // Periksa apakah jenis obat sedang digunakan di tabel obat
        $obatTerkait = $jenisObat->obat()->exists();

        if ($obatTerkait) {
            // Jika ada obat terkait, kirimkan pesan error ke session
            return redirect()->route('jenis_obat.index')->with('error', "Jenis obat \"$jenisObat->nama_jenis\" tidak dapat dihapus karena sedang digunakan di tabel obat.");
        }

        // Simpan nama jenis obat sebelum menghapus
        $namaJenis = $jenisObat->nama_jenis;

        // Menghapus jenis obat
        $jenisObat->delete();

        // Redirect kembali dengan pesan sukses
        return redirect()->route('jenis_obat.index')->with('success', "Jenis obat \"$namaJenis\" berhasil dihapus.");
    }
}
