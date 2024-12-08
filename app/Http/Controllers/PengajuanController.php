<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PengajuanController extends Controller
{
    public function index()
    {
        $transaksi = Transaksi::with('user')  // Mengambil data relasi user
            ->orderBy('tanggal', 'desc')  // Urutkan berdasarkan tanggal
            ->orderBy(User::select('ruangan')  // Urutkan berdasarkan kolom ruangan di tabel user
                ->whereColumn('user.id', 'transaksi.user_id')  // Sesuaikan dengan kolom user_id pada transaksi
                ->limit(1))  // Pastikan hanya satu nilai yang diambil dari user
            ->paginate(10);

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
}
