<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\Transaksi;
use App\Models\User;
use App\Models\Retur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // dashboard admin
    public function dashboard()
    {
        $jumlahObat = Obat::count();
        $jumlahTransaksi = Transaksi::count();
        $jumlahUser = User::count();

        $obat = Obat::orderBy('nama_obat', 'asc')->get();

        // Rekap transaksi memperhitungkan retur
        $rekapTransaksi = Transaksi::select(
            'obat.id AS obat_id',
            'obat.nama_obat',
            DB::raw('SUM(transaksi.acc) AS total_acc'),
            DB::raw('SUM(transaksi.acc * obat.harga) AS total_disetujui'),
            DB::raw('SUM(COALESCE(retur.jumlah, 0)) AS total_retur'),
            DB::raw('(obat.stok + SUM(transaksi.acc) - SUM(COALESCE(retur.jumlah, 0))) AS stok_akhir')
        )
            ->leftJoin('retur', 'transaksi.id', '=', 'retur.transaksi_id')
            ->join('obat', 'transaksi.obat_id', '=', 'obat.id')
            ->where('transaksi.status', 'Disetujui')
            ->groupBy('obat.id', 'obat.nama_obat', 'obat.stok', 'obat.harga')
            ->get();


        // Mengambil data retur
        $returData = Retur::all(); // Ambil semua data retur (atau sesuaikan dengan filter yang Anda butuhkan)

        // Mengurangi total acc dan total disetujui berdasarkan retur
        // Mengurangi total acc dan total disetujui berdasarkan retur
        foreach ($rekapTransaksi as $rekap) {
            // Mengambil jumlah retur untuk obat yang sama
            $totalRetur = $returData->where('obat_id', $rekap->obat_id)->sum('jumlah');

            // Pastikan total retur tidak melebihi total acc
            if ($totalRetur > $rekap->total_acc) {
                $totalRetur = $rekap->total_acc; // Batalkan retur jika melebihi
            }

            // Mengurangi stok berdasarkan jumlah retur
            $rekap->total_acc -= $totalRetur;  // Mengurangi total acc
            $rekap->total_disetujui -= $totalRetur * $rekap->obat->harga;  // Mengurangi total disetujui berdasarkan harga obat
        }


        return view('dashboard', compact('jumlahObat', 'jumlahTransaksi', 'jumlahUser', 'obat', 'rekapTransaksi', 'returData'));
    }


    // dashboard operator
    public function index()
    {
        // Mendapatkan semua obat yang ada, diurutkan berdasarkan nama_obat secara alfabetis (ASC)
        $obat = Obat::orderBy('nama_obat', 'asc')->get();
        // Cek level user dan arahkan ke dashboard yang sesuai
        if (Auth::user()->level == 'operator') {
            return redirect()->route('dashboard.operator'); // Redirect ke method operator
        }

        // Jika bukan operator, arahkan ke dashboard admin
        return $this->dashboard();
    }

    public function operator()
    {
        // Menghitung total jumlah transaksi berdasarkan user yang login
        $jumlahTransaksi = Transaksi::where('user_id', Auth::id())->count();

        // Ambil rekap transaksi
        $rekapTransaksi = $this->rekapTransaksi();

        // Mendapatkan total obat
        $totalObat = Obat::count();

        // Mendapatkan semua obat yang ada, diurutkan berdasarkan nama_obat secara alfabetis (ASC)
        $obat = Obat::orderBy('nama_obat', 'asc')->get();

        // Mengambil rekap transaksi dengan status 'Disetujui' dan 'Diretur'
        $rekapTransaksi = Transaksi::select(
            'transaksi.obat_id',
            DB::raw("
    SUM(
        CASE 
            WHEN transaksi.status = 'Disetujui' THEN transaksi.acc
            WHEN transaksi.status = 'Diretur' THEN transaksi.acc - COALESCE(retur.jumlah, 0)
            ELSE 0
        END
    ) AS total_disetujui
")
        )
            ->leftJoin('retur', 'transaksi.id', '=', 'retur.transaksi_id')
            ->where('transaksi.user_id', Auth::id())  // Pastikan hanya transaksi milik user yang login
            ->groupBy('transaksi.obat_id')
            ->with('obat')
            ->get();


        // Kirim data ke view
        return view('operator.dashboard', compact('totalObat', 'obat', 'rekapTransaksi', 'jumlahTransaksi'));
    }
    // milik operator 
    public function rekapTransaksi()
    {

        // Logika untuk operator tanpa menghitung retur
        $rekapTransaksi = Transaksi::select(
            'obat_id',
            DB::raw('SUM(acc) as total_acc'),
            DB::raw('SUM(acc * obat.harga) as total_disetujui')
        )
            ->join('obat', 'transaksi.obat_id', '=', 'obat.id')
            ->where('transaksi.status', 'Disetujui')
            ->where('transaksi.user_id', auth()->user()->id)
            ->groupBy('obat_id')
            ->get();

        return $rekapTransaksi;
    }
}
