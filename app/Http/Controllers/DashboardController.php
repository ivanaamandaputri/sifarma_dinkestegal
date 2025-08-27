<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\Transaksi;
use App\Models\User;
use App\Models\Retur;
use App\Models\StokPuskesmas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // dashboard admin
    public function dashboard()
    {
        $jumlahObat = Obat::count();
        $jumlahTransaksi = Transaksi::where('status', 'Menunggu')->count();
        $jumlahUser = User::count();

        // Semua obat
        $obat = Obat::orderBy('nama_obat', 'asc')->get();

        // Join ke stok_puskesmas dan user untuk tahu stok masing-masing puskesmas
        $stokPuskesmas = DB::table('stok_puskesmas')
            ->join('user', 'stok_puskesmas.user_id', '=', 'user.id')
            ->select('stok_puskesmas.obat_id', 'stok_puskesmas.jumlah', 'user.ruangan')
            ->get();

        return view('dashboard', compact('jumlahObat', 'jumlahTransaksi', 'jumlahUser', 'obat', 'stokPuskesmas'));
    }

    // dashboard operator
    public function index()
    {
        if (Auth::user()->level == 'operator') {
            return redirect()->route('dashboard.operator');
        }

        return $this->dashboard();
        // Mendapatkan semua obat yang ada, diurutkan berdasarkan nama_obat secara alfabetis (ASC)
        $obat = Obat::with('jenisObat')->orderBy('nama_obat', 'asc')->get();

        // Ambil semua stok dari user login
        $stokPuskesmas = StokPuskesmas::where('user_id', Auth::id())->get()->keyBy('obat_id');

        // Suntikkan data stok ke masing-masing obat
        foreach ($obat as $item) {
            $item->jumlah = $stokPuskesmas[$item->id]->jumlah ?? 0;
        }

        // Cek level user dan arahkan ke dashboard yang sesuai
        if (Auth::user()->level == 'operator') {
            return redirect()->route('dashboard.operator'); // Redirect ke method operator
        }

        // Jika bukan operator, arahkan ke dashboard admin
        return $this->dashboard();
    }

    public function operator()
    {
        $jumlahObatPuskesmas = StokPuskesmas::where('user_id', Auth::id())->count();
        $totalObat = Obat::count();
        $orderMenunggu = Transaksi::where('user_id', Auth::id())
            ->where('status', 'Menunggu')
            ->count();

        // Ambil semua obat
        $obat = Obat::with('jenisObat')->orderBy('nama_obat', 'asc')->get();

        // Ambil stok puskesmas berdasarkan user login dan keyBy obat_id
        $stokPuskesmas = StokPuskesmas::where('user_id', Auth::id())->get()->keyBy('obat_id');

        // Suntikkan jumlah stok dari puskesmas ke masing-masing obat
        foreach ($obat as $item) {
            $item->jumlah = $stokPuskesmas[$item->id]->jumlah ?? null;
        }

        // Rekap transaksi sesuai user login
        $rekapTransaksi = Transaksi::select(
            'transaksi.obat_id',
            DB::raw("SUM(
                CASE 
                    WHEN transaksi.status = 'Disetujui' THEN transaksi.acc
                    WHEN transaksi.status = 'Diretur' THEN transaksi.acc - COALESCE(retur.jumlah, 0)
                    ELSE 0
                END
            ) AS total_disetujui")
        )
            ->leftJoin('retur', 'transaksi.id', '=', 'retur.transaksi_id')
            ->where('transaksi.user_id', Auth::id())
            ->groupBy('transaksi.obat_id')
            ->with('obat')
            ->get();

        return view('operator.dashboard', compact(
            'totalObat',
            'obat',
            'rekapTransaksi',
            'jumlahObatPuskesmas',
            'stokPuskesmas',
            'orderMenunggu'
        ));
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
