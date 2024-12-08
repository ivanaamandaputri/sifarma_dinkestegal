<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Obat;
use App\Models\Retur;
use App\Models\User;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class TransaksiController extends Controller
{
    // Menampilkan daftar transaksi
    public function index()
    {
        // Mengambil transaksi yang terkait dengan pengguna yang sedang login
        $transaksi = Transaksi::with(['obat', 'user'])
            ->where('user_id', Auth::id())
            ->orderBy('tanggal', 'desc')
            ->get();
        // Kirimkan data ke view
        return view('transaksi.index', compact('transaksi'));
    }

    // Menampilkan form untuk membuat transaksi baru
    public function create()
    {
        // Mengambil semua obat untuk ditampilkan di form
        $obat = Obat::all();
        return view('transaksi.create', compact('obat'));
    }

    // Menyimpan transaksi baru
    public function store(Request $request)
    {
        // Validasi data input dari form
        $request->validate([
            'obat_id' => 'required|exists:obat,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal' => 'required|date',
        ]);

        // Mencari obat berdasarkan ID
        $obat = Obat::find($request->obat_id);

        // Memeriksa apakah jumlah yang diminta melebihi stok
        if ($request->jumlah > $obat->stok) {
            return back()->withErrors(['jumlah' => 'Jumlah melebihi stok yang tersedia'])->withInput();
        }

        // Menyimpan transaksi baru
        Transaksi::create([
            'obat_id' => $request->obat_id,
            'user_id' => Auth::id(),
            'jumlah' => $request->jumlah,
            'total' => $obat->harga * $request->jumlah,
            'status' => 'Menunggu',
            'tanggal' => $request->tanggal,
            'jenis_obat_id' => $obat->jenis_obat_id,
        ]);
        // dd($request->all());
        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil ditambahkan dan menunggu persetujuan');
    }

    // Menampilkan form untuk mengedit transaksi
    public function edit(Transaksi $transaksi)
    {
        // Pastikan hanya transaksi yang statusnya "Menunggu" yang bisa diubah oleh operator
        if ($transaksi->status != 'Menunggu') {
            return redirect()->route('transaksi.index')->with('error', 'Transaksi ini tidak bisa diedit karena sudah diproses.');
        }

        // Lanjutkan proses jika transaksi masih "Menunggu"
        $obat = Obat::all();
        return view('transaksi.edit', compact('transaksi', 'obat'));
    }

    // Memperbarui transaksi yang sudah ada
    public function update(Request $request, Transaksi $transaksi)
    {
        // Validasi data input dari form
        $request->validate([
            'obat_id' => 'required|exists:obat,id',
            'jumlah' => 'required|integer|min:1',
        ]);

        // Mencari obat berdasarkan ID
        $obat = Obat::find($request->obat_id);
        $total = $obat->harga * $request->jumlah;

        // Menghitung selisih jumlah obat
        $selisih = $request->jumlah - $transaksi->jumlah;

        // Memeriksa apakah stok mencukupi untuk pembaruan
        if ($selisih > 0 && $selisih > $obat->stok) {
            return back()->withErrors(['jumlah' => 'Jumlah melebihi stok yang tersedia'])->withInput();
        }

        // Memperbarui transaksi
        $transaksi->update([
            'obat_id' => $request->obat_id,
            'jumlah' => $request->jumlah,
            'total' => $total,
        ]);

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil diperbarui');
    }

    // Menghapus transaksi
    public function destroy(Transaksi $transaksi)
    {
        // Pastikan hanya transaksi yang statusnya "Menunggu" yang bisa dihapus oleh operator
        if ($transaksi->status != 'Menunggu') {
            return redirect()->route('transaksi.index')->with('error', 'Transaksi ini tidak bisa dihapus karena sudah diproses.');
        }

        // Mengembalikan stok obat
        $obat = $transaksi->obat;
        $obat->increment('stok', $transaksi->jumlah);

        // Menghapus transaksi
        $transaksi->delete();
        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dihapus');
    }

    // Menyimpan retur transaksi
    public function storeRetur(Request $request)
    {
        // Validasi data input untuk retur
        $validated = $request->validate([
            'transaksi_id' => 'required|exists:transaksi,id',
            'jumlah' => 'required|integer',
            'alasan' => 'required|string',
            'password' => 'required|string', // Periksa password sesuai kebutuhan

        ]);

        // Membuat retur baru
        Retur::create([
            'transaksi_id' => $validated['transaksi_id'],
            'jumlah' => $validated['jumlah'],
            'alasan_retur' => $validated['alasan'],
            'password' => $validated['password'],
            'user_id' => Auth::id(), // Ambil user yang sedang login
        ]);

        return response()->json(['success' => true]);
    }

    public function selesai($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        // Periksa status dan ubah jika perlu
        if ($transaksi->status === 'Disetujui') {
            $transaksi->status = 'Selesai';
            $transaksi->save();

            return response()->json(['message' => 'Transaksi Selesai']);
        }

        return response()->json(['error' => 'Transaksi tidak bisa diubah'], 400);
    }

    // Menangani retur transaksi
    public function retur(Request $request)
    {
        $request->validate([
            'jumlah' => 'required|integer',
            'alasan_retur' => 'nullable|string',
            'password' => 'required|string',
        ]);

        // Verifikasi password dan validasi lainnya
        // Proses untuk membuat retur baru
        $retur = Retur::create([
            'transaksi_id' => $request->transaksi_id,
            'obat_id' => $request->obat_id,
            'jumlah' => $request->jumlah,
            'alasan_retur' => $request->alasan_retur,
            'password' => $request->password, // Proses validasi password di sini
            'status' => 'Diretur',
            'user_id' => auth()->id(),
        ]);

        return response()->json(['success' => 'Retur berhasil diproses']);
    }
    public function showTransaksi($tanggal)
    {
        return view('nama-view', compact('tanggal'));
    }

    public function buatPengajuan(Request $request)
    {
        $pengajuan = Transaksi::create($request->all());

        // Kirim notifikasi ke semua admin
        $admins = User::where('level', 'admin')->get(); // Ambil semua admin
        foreach ($admins as $admin) {
            Notifikasi::create([
                'user_id' => $admin->id,
                'judul' => 'Pengajuan Baru',
                'pesan' => 'Ada pengajuan baru pada tanggal ' . $pengajuan->tanggal .
                    ' untuk obat ' . $pengajuan->obat->nama . '. Silakan periksa.',
                'level' => 'admin',
            ]);
        }

        return redirect()->back()->with('success', 'Pengajuan berhasil dibuat.');
    }
}
