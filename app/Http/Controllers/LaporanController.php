<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Obat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    // laporan permintaan obat admin
    public function index(Request $request)
    {
        // Ambil parameter bulan, tahun, ruangan, dan obat
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $ruangan = $request->ruangan;
        $obat_id = $request->obat_id;

        // Ambil daftar tahun berdasarkan transaksi yang ada
        $tahunList = Transaksi::selectRaw('YEAR(tanggal) as tahun')
            ->distinct()
            ->orderBy('tahun', 'asc')
            ->get()
            ->pluck('tahun');  // Mengambil hanya kolom 'tahun'

        // Query transaksi berdasarkan filter yang dipilih
        $query = Transaksi::with('obat', 'user')
            ->where('status', 'Disetujui')  // Menyaring hanya transaksi yang disetujui
            ->when($bulan, function ($query) use ($bulan) {
                return $query->whereMonth('tanggal', $bulan);
            })
            ->when($tahun, function ($query) use ($tahun) {
                return $query->whereYear('tanggal', $tahun);
            })
            ->when($ruangan, function ($query) use ($ruangan) {
                return $query->whereHas('user', function ($query) use ($ruangan) {
                    return $query->where('ruangan', $ruangan);
                });
            })
            ->when($obat_id, function ($query) use ($obat_id) {
                return $query->where('obat_id', $obat_id);
            })
            ->get();

        // Ambil daftar obat dan instansi (ruangan) untuk pilihan filter
        $obatList = Obat::all();
        $instansiList = [
            'puskesmas Kaligangsa',
            'puskesmas Margadana',
            'puskesmas Tegal Barat',
            'puskesmas Debong Lor',
            'puskesmas Tegal Timur',
            'puskesmas Slerok',
            'puskesmas Tegal Selatan',
            'puskesmas Bandung'
        ];

        return view('laporan.index', compact('query', 'obatList', 'instansiList', 'bulan', 'tahun', 'ruangan', 'obat_id', 'tahunList'));
    }

    public function cetak(Request $request)
    {
        // Ambil bulan dan tahun dari request atau set default
        $bulan = $request->input('bulan', null);
        $tahun = $request->input('tahun', null);
        $ruangan = auth()->user()->ruangan;

        // Mengambil rekap total transaksi yang disetujui
        $rekapTotal = Transaksi::select('obat_id', DB::raw('SUM(jumlah) as total_disetujui'))
            ->whereIn('status', ['Disetujui'])
            ->groupBy('obat_id')
            ->with('obat') // Untuk mengambil nama obat
            ->get();

        // Query transaksi untuk laporan dengan penyesuaian status 'Disetujui' atau 'Diretur'
        $rekapTransaksi = Transaksi::select(
            'transaksi.*',
            DB::raw("CASE 
                            WHEN transaksi.status = 'Diretur' THEN transaksi.jumlah - COALESCE(retur.jumlah, 0)
                            ELSE transaksi.jumlah
                        END AS jumlah_akhir")
        )
            ->leftJoin('retur', 'transaksi.id', '=', 'retur.transaksi_id')
            ->whereIn('transaksi.status', ['Disetujui', 'Diretur'])
            ->whereHas('user', function ($query) use ($ruangan) {
                $query->where('ruangan', $ruangan);
            })
            ->when($bulan, function ($query) use ($bulan) {
                return $query->whereMonth('tanggal', $bulan);
            })
            ->when($tahun, function ($query) use ($tahun) {
                return $query->whereYear('tanggal', $tahun);
            })
            ->get();

        // Kirim data ke view operator/cetakorder
        return view('operator.cetakorder', compact('rekapTransaksi', 'rekapTotal', 'bulan', 'tahun', 'ruangan'));
    }

    // laporan permintaan obat operator
    public function laporder(Request $request)
    {
        // Ambil data obat untuk dropdown
        $obatList = Obat::all();

        // Ambil semua tahun dari data transaksi berdasarkan kolom 'tanggal'
        $tahunList = Transaksi::selectRaw('YEAR(tanggal) as tahun')
            ->distinct()
            ->orderBy('tahun', 'asc')
            ->get()
            ->pluck('tahun'); // Pluck untuk mengambil hanya kolom 'tahun'

        // Proses filter berdasarkan bulan, tahun, dan obat
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $obat_id = $request->input('obat_id');
        $ruangan = auth()->user()->ruangan; // Ambil ruangan dari user yang login

        // Query transaksi
        $rekapTransaksi = Transaksi::select(
            'transaksi.*', // Ambil semua kolom transaksi
            DB::raw("
                CASE 
                    WHEN transaksi.status = 'Diretur' THEN transaksi.jumlah - COALESCE(retur.jumlah, 0)
                    ELSE transaksi.jumlah
                END AS jumlah_akhir
            ")
        )
            ->leftJoin('retur', 'transaksi.id', '=', 'retur.transaksi_id') // Join tabel retur
            ->whereIn('transaksi.status', ['Disetujui', 'Diretur']) // Hanya ambil transaksi yang Disetujui atau Diretur
            ->whereHas('user', function ($query) use ($ruangan) {
                $query->where('ruangan', $ruangan); // Filter berdasarkan ruangan user yang login
            })
            ->when($bulan, function ($query) use ($bulan) {
                return $query->whereMonth('tanggal', $bulan);
            })
            ->when($tahun, function ($query) use ($tahun) {
                return $query->whereYear('tanggal', $tahun);
            })
            ->when($obat_id, function ($query) use ($obat_id) {
                return $query->where('obat_id', $obat_id);
            })
            ->get();

        // Menampilkan data ke view
        return view('operator.laporder', compact('rekapTransaksi', 'obatList', 'tahunList'));
    }


    // hasil cetak dari seleksi laporder operator
    public function cetakOrder(Request $request)
    {

        $rekapTotal = Transaksi::select('obat_id', DB::raw('SUM(jumlah) as total_disetujui'))
            ->whereIn('status', ['Disetujui'])
            ->groupBy('obat_id')
            ->with('obat') // Untuk mengambil nama obat
            ->get();


        // Ambil bulan dan tahun dari request atau set default
        $bulan = $request->input('bulan', null);
        $tahun = $request->input('tahun', null);
        $ruangan = auth()->user()->ruangan;

        // Query transaksi untuk laporan
        $rekapTransaksi = Transaksi::select(
            'transaksi.*',
            DB::raw("CASE 
                        WHEN transaksi.status = 'Diretur' THEN transaksi.jumlah - COALESCE(retur.jumlah, 0)
                        ELSE transaksi.jumlah
                    END AS jumlah_akhir")
        )
            ->leftJoin('retur', 'transaksi.id', '=', 'retur.transaksi_id')
            ->whereIn('transaksi.status', ['Disetujui', 'Diretur'])
            ->whereHas('user', function ($query) use ($ruangan) {
                $query->where('ruangan', $ruangan);
            })
            ->when($bulan, function ($query) use ($bulan) {
                return $query->whereMonth('tanggal', $bulan);
            })
            ->when($tahun, function ($query) use ($tahun) {
                return $query->whereYear('tanggal', $tahun);
            })
            ->get();

        // Kirim data ke view operator/cetakorder
        return view('operator.cetakorder', compact('rekapTransaksi', 'rekapTotal', 'bulan', 'tahun', 'ruangan'));
    }
}
