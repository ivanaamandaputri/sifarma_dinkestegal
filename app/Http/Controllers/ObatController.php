<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\JenisObat; // Import model JenisObat
use App\Models\StokMasuk;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ObatController extends Controller
{
    // Menampilkan daftar obat
    public function index()
    {
        $obat = Obat::orderBy('nama_obat', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        $batasMinimum = 5; // Batas minimum stok
        $tanggalHariIni = Carbon::now(); // Tanggal saat ini
        $peringatanExp = 30; // Batas peringatan (30 hari sebelum exp)

        // Cek stok dan kedaluwarsa
        foreach ($obat as $ob) {
            $ob->warning = $ob->stok <= $batasMinimum; // Stok hampir habis

            if ($ob->exp) {
                $tanggalExp = Carbon::parse($ob->exp);
                if ($tanggalExp->isPast()) {
                    $ob->expWarning = 'Sudah Kedaluwarsa';
                } elseif ($tanggalExp->diffInDays($tanggalHariIni) <= $peringatanExp) {
                    $ob->expWarning = 'Mendekati Kedaluwarsa';
                } else {
                    $ob->expWarning = null; // Tidak ada peringatan
                }
            } else {
                $ob->expWarning = 'Tanggal Kedaluwarsa Tidak Tersedia';
            }
        }

        $jenisObat = JenisObat::all(); // Ambil data jenis obat
        $readOnly = auth()->User()->level === 'operator'; // true jika operator, false jika admin

        return view('obat.index', compact('obat', 'readOnly', 'jenisObat')); // Mengirim data jenisObat
    }

    // Menampilkan form untuk membuat obat baru
    public function create()
    {
        // Mengambil data jenis obat untuk ditampilkan di dropdown, hanya mengambil kolom 'id' dan 'nama_jenis'
        $jenisObat = JenisObat::select('id', 'nama_jenis')->get();

        return view('obat.create', compact('jenisObat')); // Kirim data jenis obat ke view
    }

    // Menyimpan data obat baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_obat' => 'required|string|max:255',
            'dosis' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
            'harga' => 'required|numeric|min:0',
            'exp' => 'nullable|date', // Validasi untuk kolom exp
            'keterangan' => 'nullable|string', // Validasi untuk kolom keterangan
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi untuk kolom foto
            'jenis_obat_id' => 'required|exists:jenis_obat,id' // Validasi untuk jenis_obat_id
        ]);

        // Cek jika kombinasi nama_obat, dosis, dan jenis_obat_id sudah ada
        $existingObat = Obat::where('nama_obat', $request->nama_obat)
            ->where('dosis', $request->dosis)
            ->where('jenis_obat_id', $request->jenis_obat_id) // Perbaiki penggunaan kolom yang tepat
            ->first();

        if ($existingObat) {
            return redirect()->back()->withErrors(['error' => 'Data obat yang sama sudah ada'])->withInput();
        }

        // Jika ada file foto, simpan file dan ambil nama filenya
        $filename = null;
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $filename = 'Foto_' . uniqid() . '.' . $foto->getClientOriginalExtension();
            $foto->storeAs('public/obat', $filename); // Pastikan path ini benar
        }

        // Membuat obat baru
        Obat::create([
            'nama_obat' => $request->nama_obat,
            'dosis' => $request->dosis,
            'stok' => $request->stok,
            'harga' => $request->harga,
            'exp' => $request->exp,
            'keterangan' => $request->keterangan,
            'foto' => $filename,  // Masukkan nama file foto jika ada
            'jenis_obat_id' => $request->jenis_obat_id
        ]);

        return redirect()->route('obat.index')->with('success', 'Obat berhasil ditambahkan'); // Redirect ke halaman daftar obat
    }

    // Menampilkan form untuk mengedit obat
    public function edit(string $id)
    {
        $obat = Obat::findOrFail($id); // Mencari obat berdasarkan ID
        $jenisObat = JenisObat::all(); // Ambil data jenis obat untuk dropdown
        return view('obat.edit', compact('obat', 'jenisObat')); // Mengembalikan view untuk mengedit obat
    }

    // Mengupdate data obat
    public function update(Request $request, string $id)
    {
        $obat = Obat::findOrFail($id); // Mencari obat berdasarkan ID

        // Validasi input
        $request->validate([
            'nama_obat' => 'required|string|max:255',
            'dosis' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
            'harga' => 'required|numeric|min:0',
            'exp' => 'nullable|date',
            'keterangan' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'jenis_obat_id' => 'required|exists:jenis_obat,id',
        ]);

        // Jika ada file foto baru, hapus foto lama dan simpan yang baru
        if ($request->hasFile('foto')) {
            if ($obat->foto) {
                Storage::delete('public/obat/' . $obat->foto);
            }
            $foto = $request->file('foto');
            $filename = 'FTM_' . time() . '.' . $foto->getClientOriginalExtension();
            $foto->storeAs('public/obat', $filename);
            $obat->foto = $filename;
        }

        // Cek jika kombinasi nama_obat, dosis, dan jenis_obat_id sudah ada, kecuali untuk obat yang sedang diperbarui
        $existingObat = Obat::where('nama_obat', $request->nama_obat)
            ->where('dosis', $request->dosis)
            ->where('jenis_obat_id', $request->jenis_obat_id)
            ->where('id', '!=', $id) // Abaikan obat yang sedang diperbarui
            ->first();

        if ($existingObat) {
            return redirect()->back()->withErrors(['error' => 'Obat dengan kombinasi nama, dosis, dan jenis yang sama sudah ada.'])->withInput();
        }

        // Mengupdate data obat
        $obat->update([
            'nama_obat' => $request->nama_obat,
            'dosis' => $request->dosis,
            'stok' => $request->stok,
            'harga' => $request->harga,
            'exp' => $request->exp,
            'keterangan' => $request->keterangan,
            'foto' => $obat->foto, // Memastikan foto tetap sama jika tidak ada perubahan
            'jenis_obat_id' => $request->jenis_obat_id
        ]);

        return redirect()->route('obat.index')->with('success', 'Obat berhasil diperbarui');
    }

    // Menghapus data obat
    public function destroy(string $id)
    {
        $obat = Obat::findOrFail($id);
        if ($obat->foto) {
            Storage::delete('public/obat/' . $obat->foto); // Menghapus foto obat jika ada
        }
        // Periksa apakah obat ini digunakan dalam transaksi
        $transaksi = \App\Models\Transaksi::where('obat_id', $obat->id)->first();

        if ($transaksi) {
            // Jika obat terkait dengan transaksi, tampilkan pesan konfirmasi atau alihkan
            return redirect()->route('obat.index')->with('error', 'Obat ini tidak dapat dihapus karena sudah digunakan dalam transaksi.');
        }

        // Jika tidak ada transaksi, hapus foto (jika ada) dan hapus obat
        if ($obat->foto) {
            Storage::delete('public/obat/' . $obat->foto); // Menghapus foto obat jika ada
        }

        $obat->delete();

        return redirect()->route('obat.index')->with('success', 'Obat berhasil dihapus');
    }

    // Menampilkan detail obat
    public function show(string $id)
    {
        $obat = Obat::findOrFail($id); // Mencari obat berdasarkan ID
        return view('obat.show', compact('obat')); // Mengembalikan view untuk detail obat
    }

    // Menampilkan data obat untuk operator
    public function operatorIndex()
    {
        $obat = Obat::orderBy('nama_obat', 'asc')->get();
        return view('operator.dataobat', compact('obat')); // Pastikan untuk mengarahkan ke view yang tepat
    }

    // Menampilkan detail obat untuk operator
    public function operatorShowobat($id)
    {
        // Mencari data obat berdasarkan ID
        $obat = Obat::findOrFail($id);
        return view('operator.showobat', compact('obat')); // Mengembalikan detail data obat
    }

    public function tambahStok(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1',
            'sumber' => 'nullable|string|max:255',
            'tanggal' => 'required|date',
        ]);

        // Temukan obat
        $obat = Obat::findOrFail($id);

        // Tambahkan stok ke tabel stok_masuk
        StokMasuk::create([
            'obat_id' => $obat->id,    // Pastikan 'obat_id' sesuai
            'jumlah' => $request->jumlah,
            'sumber' => $request->sumber,
            'tanggal' => $request->tanggal,
        ]);

        // Update stok obat
        $obat->increment('stok', $request->jumlah);

        return redirect()->route('obat.index')->with('success', 'Stok berhasil ditambahkan!');
    }
}
