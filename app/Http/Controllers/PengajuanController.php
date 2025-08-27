<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Retur;
use App\Models\Transaksi;
use App\Models\User;
use App\Models\StokPuskesmas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PengajuanController extends Controller
{
    public function index()
    {
        $transaksi = Transaksi::with('user')
            ->orderBy('tanggal', 'desc')
            ->orderBy(User::select('ruangan')
                ->whereColumn('user.id', 'transaksi.user_id')
                ->limit(1))
            ->get();

        return view('pengajuan.index', compact('transaksi'));
    }

    public function approve(Request $request, $id)
    {
        try {
            // Mencari transaksi berdasarkan ID
            $transaksi = Transaksi::findOrFail($id);

            // Validasi input
            $request->validate([
                'acc' => 'required|integer|min:1|max:' . $transaksi->jumlah, // Jumlah ACC minimal 1 dan tidak melebihi jumlah permintaan
            ]);

            $acc = $request->input('acc'); // Mengambil jumlah ACC
            $obat = $transaksi->obat; // Mengambil data obat terkait

            // Log untuk debug
            Log::info('Jumlah ACC: ' . $acc);
            Log::info('Harga Obat: ' . $obat->harga);

            // Periksa stok obat
            if ($obat->stok < $acc) {
                return response()->json(['error' => 'Stok obat tidak mencukupi.'], 400);
            }
            if ($acc > $obat->stok) {
                return response()->json(['error' => 'Jumlah ACC melebihi stok yang tersedia.'], 400);
            }

            // Proses transaksi dan update data
            $total = $acc * $obat->harga; // Menghitung total berdasarkan ACC * harga obat
            Log::info('Total yang dihitung: ' . $total);

            // Update transaksi
            $transaksi->update([
                'acc' => $acc,
                'status' => 'Disetujui',
                'total' => $total,  // Pastikan total dihitung dengan benar
            ]);

            // Update stok obat
            $obat->stok -= $acc;  // Mengurangi stok sesuai jumlah ACC yang disetujui
            $obat->save();

            // Tambahkan stok ke puskesmas setelah disetujui
            $stokPuskesmas = StokPuskesmas::where('user_id', $transaksi->user_id)
                ->where('obat_id', $transaksi->obat_id)
                ->first();

            if ($stokPuskesmas) {
                // Jika stok sudah ada, tambahkan
                $stokPuskesmas->jumlah += $acc;
                $stokPuskesmas->save();
            } else {
                // Jika belum ada, buat entri baru
                StokPuskesmas::create([
                    'user_id' => $transaksi->user_id,
                    'obat_id' => $transaksi->obat_id,
                    'jumlah' => $acc,
                ]);
            }

            // Kirim notifikasi ke operator
            Notifikasi::create([
                'user_id' => $transaksi->user_id, // ID operator yang mengajukan
                'judul' => 'Pengajuan Disetujui',
                'pesan' => 'Pengajuan Anda telah disetujui.',
                'level' => 'operator', // Level notifikasi
            ]);

            return response()->json(['message' => 'Transaksi disetujui dan stok berkurang.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function reject(Request $request, $id)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:255',
            ]);

            $transaksi = Transaksi::findOrFail($id);

            if ($transaksi->status === 'Ditolak') {
                return response()->json(['error' => 'Transaksi sudah ditolak.'], 400);
            }

            $transaksi->update([
                'status' => 'Ditolak',
                'acc' => 0,
                'total' => 0,
                'alasan_penolakan' => $request->input('reason'),
            ]);

            Notifikasi::create([
                'user_id' => $transaksi->user_id,
                'judul' => 'Pengajuan Ditolak',
                'pesan' => 'Pengajuan Anda ditolak dengan alasan: ' . $request->input('reason'),
                'level' => 'operator',
            ]);

            return response()->json(['message' => 'Transaksi berhasil ditolak.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat memproses penolakan transaksi.'], 500);
        }
    }

    public function getNotifikasi()
    {
        $level = auth()->user()->level;

        if ($level === 'admin') {
            $notifikasi = Notifikasi::where('level', 'admin')
                ->where('dibaca', false)
                ->get();
        } elseif ($level === 'operator') {
            $notifikasi = Notifikasi::where('level', 'operator')
                ->where('user_id', auth()->id())
                ->where('dibaca', false)
                ->get();
        } else {
            abort(403, 'Unauthorized access.');
        }
        // dd($notifikasi);
        // Pastikan variabel notifikasi dikirim ke tampilan
        return view('dashboard', compact('notifikasi'));
    }

    public function bacaNotifikasi($id)
    {
        $notifikasi = Notifikasi::findOrFail($id);
        $notifikasi->update(['dibaca' => true]);

        return redirect()->back();
    }

    public function retur(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'id_transaksi' => 'required|exists:transaksis,id',
                'jumlah' => 'required|integer|min:1',
                'alasan' => 'required|string|max:255',
            ]);

            // Mencari transaksi
            $transaksi = Transaksi::find($request->id_transaksi);

            if (!$transaksi) {
                return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
            }

            // Mengecek apakah stok obat cukup untuk dikembalikan
            $obat = $transaksi->obat;
            $stokLama = $obat->stok;

            // Update stok obat, menambah jumlah yang dikembalikan
            $obat->stok += $request->jumlah;
            $obat->save();

            // Melakukan update status dan alasan retur pada transaksi
            $transaksi->status = 'Retur';
            $transaksi->alasan_retur = $request->alasan;
            $transaksi->save();

            // Menyimpan retur ke dalam tabel retur
            Retur::create([
                'transaksi_id' => $transaksi->id,
                'obat_id' => $obat->id,
                'user_id' => auth()->id(),
                'jumlah' => $request->jumlah,
                'alasan_retur' => $request->alasan,
                'status' => 'Diretur',
            ]);

            // Mengirimkan response JSON untuk sukses
            return response()->json([
                'message' => 'Retur berhasil diproses dan stok diperbarui.',
                'transaksi_id' => $transaksi->id,
                'jumlah' => $request->jumlah,
                'alasan' => $request->alasan
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat memproses retur',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
