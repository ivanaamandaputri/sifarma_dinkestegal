<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\JenisObat; // Import model JenisObat
use App\Models\StokMasuk;
use App\Models\Transaksi;
use App\Models\StokPuskesmas;
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

            // if ($ob->exp) {
            //     $tanggalExp = Carbon::parse($ob->exp);
            //     if ($tanggalExp->isPast()) {
            //         $ob->expWarning = 'Sudah Kedaluwarsa';
            //     } elseif ($tanggalExp->diffInDays($tanggalHariIni) <= $peringatanExp) {
            //         $ob->expWarning = 'Mendekati Kedaluwarsa';
            //     } else {
            //         $ob->expWarning = null; // Tidak ada peringatan
            //     }
            // } else {
            //     $ob->expWarning = 'Tanggal Kedaluwarsa Tidak Tersedia';
            // }
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
            'exp' => 'nullable|date',
            'keterangan' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'jenis_obat_id' => 'required|exists:jenis_obat,id'
        ]);

        // Cek data obat duplikat
        $existingObat = Obat::where('nama_obat', $request->nama_obat)
            ->where('dosis', $request->dosis)
            ->where('jenis_obat_id', $request->jenis_obat_id)
            ->first();

        if ($existingObat) {
            return redirect()->back()->withErrors(['error' => 'Data obat yang sama sudah ada'])->withInput();
        }

        // Upload foto jika ada
        $filename = null;
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $filename = 'Foto_' . uniqid() . '.' . $foto->getClientOriginalExtension();
            $foto->storeAs('public/obat', $filename);
        }

        // Ambil singkatan dari jenis obat
        $jenisObat = JenisObat::findOrFail($request->jenis_obat_id);
        $prefix = $this->generateSingkatan($jenisObat->nama_jenis);

        // Hitung jumlah obat yang sudah ada dalam jenis ini
        $jumlahObatDalamJenis = Obat::where('jenis_obat_id', $request->jenis_obat_id)->count();

        // Nomor urut = jumlah obat + 1
        $nomorUrut = str_pad($jumlahObatDalamJenis + 1, 4, '0', STR_PAD_LEFT);

        // Simpan obat dengan kode otomatis
        $obat = Obat::create([
            'nama_obat' => $request->nama_obat,
            'dosis' => $request->dosis,
            'stok' => $request->stok,
            'harga' => $request->harga,
            'exp' => $request->exp,
            'keterangan' => $request->keterangan,
            'foto' => $filename,
            'jenis_obat_id' => $request->jenis_obat_id,
            'kode_obat' => $prefix . $nomorUrut,
        ]);

        return redirect()->route('obat.index')->with('success', 'Obat berhasil ditambahkan');
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
        $obat = Obat::findOrFail($id); // Ambil data lama

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

        // Cek duplikat kecuali data yang sedang diupdate
        $existingObat = Obat::where('nama_obat', $request->nama_obat)
            ->where('dosis', $request->dosis)
            ->where('jenis_obat_id', $request->jenis_obat_id)
            ->where('id', '!=', $obat->id)
            ->first();

        if ($existingObat) {
            return back()->withErrors(['error' => 'Obat dengan kombinasi nama, dosis, dan jenis ini sudah ada.'])->withInput();
        }

        // Ganti foto jika ada upload baru
        if ($request->hasFile('foto')) {
            if ($obat->foto) {
                Storage::delete('public/obat/' . $obat->foto);
            }
            $foto = $request->file('foto');
            $filename = 'FTM_' . time() . '.' . $foto->getClientOriginalExtension();
            $foto->storeAs('public/obat', $filename);
            $obat->foto = $filename;
        }

        // Jika jenis_obat berubah, buat kode_obat baru
        if ($request->jenis_obat_id != $obat->jenis_obat_id) {
            $jenisBaru = JenisObat::findOrFail($request->jenis_obat_id);
            $prefixBaru = $this->generateSingkatan($jenisBaru->nama_jenis);
            $jumlahObatDalamJenisBaru = Obat::where('jenis_obat_id', $request->jenis_obat_id)->count();
            $nomorUrutBaru = str_pad($jumlahObatDalamJenisBaru + 1, 4, '0', STR_PAD_LEFT);
            $obat->kode_obat = $prefixBaru . $nomorUrutBaru;
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

        // Update data (pastikan kode_obat ikut diubah jika diperlukan)
        $obat->update([
            'nama_obat' => $request->nama_obat,
            'dosis' => $request->dosis,
            'stok' => $request->stok,
            'harga' => $request->harga,
            'exp' => $request->exp,
            'keterangan' => $request->keterangan,
            'foto' => $obat->foto,
            'jenis_obat_id' => $request->jenis_obat_id,
            'kode_obat' => $obat->kode_obat, // pastikan selalu ikut diupdate
        ]);


        return redirect()->route('obat.index')->with('success', 'Obat berhasil diperbarui');
    }

    // Menghapus data obat
    public function destroy(string $id)
    {
        $obat = Obat::findOrFail($id);

        // Periksa relasi ke tabel 'stok_masuk'
        if ($obat->stokMasuk()->exists()) {
            return redirect()->route('obat.index')
                ->with('error', 'Obat ini tidak dapat dihapus karena masih memiliki data stok masuk yang terkait.');
        }

        // Periksa relasi ke tabel 'transaksi'
        if (\App\Models\Transaksi::where('obat_id', $obat->id)->exists()) {
            return redirect()->route('obat.index')
                ->with('error', 'Obat ini tidak dapat dihapus karena sudah digunakan dalam transaksi.');
        }

        // Hapus foto jika ada
        if ($obat->foto) {
            Storage::delete('public/obat/' . $obat->foto);
        }

        // Hapus data obat
        $obat->delete();

        return redirect()->route('obat.index')->with('success', 'Obat berhasil dihapus.');
    }


    // Menampilkan detail obat
    public function show(string $id)
    {
        $obat = Obat::findOrFail($id); // Mencari obat berdasarkan ID
        return view('obat.show', compact('obat')); // Mengembalikan view untuk detail obat
    }

    private function generateSingkatan($namaJenis)
    {
        $words = explode(' ', strtoupper(trim($namaJenis)));
        $base = '';

        // Batasi maksimal 3 kata
        $words = array_slice($words, 0, 3);

        if (count($words) === 1) {
            // Jika hanya satu kata
            $word = preg_replace('/[^A-Z]/', '', $words[0]);
            $base = substr($word, 0, 3);
        } else {
            // Jika dua atau tiga kata, ambil 1 huruf pertama dari tiap kata
            $base = '';
            foreach ($words as $w) {
                $base .= substr(preg_replace('/[^A-Z]/', '', $w), 0, 1);
            }

            // Jika kurang dari 3 huruf, lengkapi dari kata pertama
            if (strlen($base) < 3) {
                $first = preg_replace('/[^A-Z]/', '', $words[0]);
                $base = str_pad($base, 3, substr($first, 1));
            }

            $base = strtoupper(substr($base, 0, 3));
        }

        // Ambil semua prefix yang sudah ada dari kode_obat
        $existingPrefixes = Obat::selectRaw('DISTINCT LEFT(kode_obat, 3) as prefix')
            ->pluck('prefix')
            ->map(fn($p) => strtoupper($p))
            ->toArray();

        // Jika base belum dipakai, langsung pakai
        if (!in_array($base, $existingPrefixes)) {
            return $base;
        }

        // Jika sudah dipakai, cari variasi numerik di belakang
        $counter = 1;
        while (in_array($base . $counter, $existingPrefixes)) {
            $counter++;
        }

        return $base . $counter;
    }

    public function operatorIndex()
    {
        // Obat dari gudang (stok di tabel obat)
        $obatGudang = Obat::orderBy('nama_obat', 'asc')->get();

        // Obat dari puskesmas (stok di tabel stok_puskesmas)
        $obatPuskesmas = StokPuskesmas::with('obat')
            ->where('user_id', auth()->id()) // ambil stok sesuai operator yang login
            ->get();


        return view('operator.dataobat', compact('obatGudang', 'obatPuskesmas'));
    }
}
