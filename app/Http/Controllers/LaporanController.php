<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Obat;
use App\Models\PemakaianObat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        // Query transaksi sesuai filter
        $rekapTransaksi = Transaksi::select(
            'transaksi.*',
            DB::raw("
            CASE 
                WHEN transaksi.status = 'Diretur' THEN transaksi.jumlah - COALESCE(retur.jumlah, 0)
                ELSE transaksi.jumlah
            END AS jumlah_akhir
        ")
        )
            ->leftJoin('retur', 'transaksi.id', '=', 'retur.transaksi_id')
            ->whereIn('transaksi.status', ['Disetujui', 'Diretur'])
            ->when($bulan, fn($q) => $q->whereMonth('transaksi.tanggal', $bulan))
            ->when($tahun, fn($q) => $q->whereYear('transaksi.tanggal', $tahun))
            ->when($ruangan, fn($q) => $q->whereHas('user', fn($u) => $u->where('ruangan', $ruangan)))
            ->when($obat_id, fn($q) => $q->where('transaksi.obat_id', $obat_id))
            ->with(['obat', 'user'])
            ->get();

        return view('laporan.index', [
            'rekapTransaksi' => $rekapTransaksi,
            'tahunList' => $tahunList,
            'instansiList' => $instansiList,
            'obatList' => $obatList,
            'bulan' => request('bulan'),
            'tahun' => request('tahun'),
            'ruangan' => request('ruangan'),
            'obat_id' => request('obat_id'),
        ]);
    }
    // laporan cetak permintaan obat admin
    public function cetak(Request $request)
    {
        // Ambil bulan dan tahun dari request atau set default
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $ruangan = $request->input('ruangan'); // Admin bebas pilih ruangan
        $obat_id = $request->input('obat_id');

        // Ambil total per obat sesuai filter
        $rekapTotal = Transaksi::select('obat_id', DB::raw('SUM(jumlah) as total_disetujui'))
            ->whereIn('status', ['Disetujui'])
            ->when($ruangan, fn($q) => $q->whereHas('user', fn($u) => $u->where('ruangan', $ruangan)))
            ->when($bulan, fn($q) => $q->whereMonth('tanggal', $bulan))
            ->when($tahun, fn($q) => $q->whereYear('tanggal', $tahun))
            ->when($obat_id, fn($q) => $q->where('obat_id', $obat_id))
            ->groupBy('obat_id')
            ->with('obat')
            ->get();

        // Detail transaksi
        $laporanTransaksi = Transaksi::select(
            'transaksi.*',
            DB::raw("CASE 
            WHEN transaksi.status = 'Diretur' THEN transaksi.jumlah - COALESCE(retur.jumlah, 0)
            ELSE transaksi.jumlah
        END AS jumlah_akhir")
        )
            ->leftJoin('retur', 'transaksi.id', '=', 'retur.transaksi_id')
            ->whereIn('transaksi.status', ['Disetujui', 'Diretur'])
            ->when($ruangan, fn($q) => $q->whereHas('user', fn($u) => $u->where('ruangan', $ruangan)))
            ->when($bulan, fn($q) => $q->whereMonth('tanggal', $bulan))
            ->when($tahun, fn($q) => $q->whereYear('tanggal', $tahun))
            ->when($obat_id, fn($q) => $q->where('transaksi.obat_id', $obat_id))
            ->orderBy('tanggal', 'asc')
            ->with(['obat', 'user'])
            ->get();

        $rekapTotal = Transaksi::select('obat_id', DB::raw('SUM(jumlah) as total_disetujui'))
            ->whereIn('status', ['Disetujui'])
            ->when($ruangan, fn($q) => $q->whereHas('user', fn($u) => $u->where('ruangan', $ruangan)))
            ->when($bulan, fn($q) => $q->whereMonth('tanggal', $bulan))
            ->when($tahun, fn($q) => $q->whereYear('tanggal', $tahun))
            ->when($obat_id, fn($q) => $q->where('obat_id', $obat_id))
            ->groupBy('obat_id')
            ->with('obat')
            ->get();

        return view('laporan.cetak', compact(
            'laporanTransaksi',
            'rekapTotal',
            'bulan',
            'tahun',
            'ruangan',
            'obat_id'
        ));
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
                return $query->whereMonth('transaksi.tanggal', $bulan); // Tambahkan nama tabel untuk tanggal
            })
            ->when($tahun, function ($query) use ($tahun) {
                return $query->whereYear('transaksi.tanggal', $tahun); // Tambahkan nama tabel untuk tanggal
            })
            ->when($obat_id, function ($query) use ($obat_id) {
                return $query->where('transaksi.obat_id', $obat_id); // Tambahkan nama tabel untuk obat_id
            })
            ->get();


        // Menampilkan data ke view
        return view('operator.laporder', compact('rekapTransaksi', 'obatList', 'tahunList'));
    }


    // hasil cetak dari seleksi laporder operator
    public function cetakOrder(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $obat_id = $request->input('obat_id');
        $ruangan = auth()->user()->ruangan;

        // Total disetujui hanya untuk ruangan yang login dan sesuai filter
        $rekapTotal = Transaksi::select('obat_id', DB::raw('SUM(jumlah) as total_disetujui'))
            ->whereIn('status', ['Disetujui'])
            ->whereHas('user', function ($query) use ($ruangan) {
                $query->where('ruangan', $ruangan);
            })
            ->when($bulan, fn($q) => $q->whereMonth('tanggal', $bulan))
            ->when($tahun, fn($q) => $q->whereYear('tanggal', $tahun))
            ->when($obat_id, fn($q) => $q->where('obat_id', $obat_id))
            ->groupBy('obat_id')
            ->with('obat')
            ->get();

        $rekapTransaksi = Transaksi::select(
            'transaksi.*',
            DB::raw("CASE 
                WHEN transaksi.status = 'Diretur' THEN transaksi.jumlah - COALESCE(retur.jumlah, 0)
                ELSE transaksi.jumlah
            END AS jumlah_akhir")
        )
            ->leftJoin('retur', 'transaksi.id', '=', 'retur.transaksi_id')
            ->whereIn('transaksi.status', ['Disetujui', 'Diretur'])
            ->whereHas('user', fn($q) => $q->where('ruangan', $ruangan))
            ->when($bulan, fn($q) => $q->whereMonth('tanggal', $bulan))
            ->when($tahun, fn($q) => $q->whereYear('tanggal', $tahun))
            ->when($obat_id, fn($q) => $q->where('transaksi.obat_id', $obat_id))
            ->orderBy('tanggal', 'asc') // URUTKAN DARI TANGGAL PALING LAMA KE BARU
            ->with(['obat']) // tambahkan relasi jika ingin menampilkan nama obat
            ->get();

        return view('operator.cetakorder', compact('rekapTransaksi', 'rekapTotal', 'bulan', 'tahun', 'ruangan'));
    }
    // laporan pemakaian operator
    public function cetakPemakaian(Request $request)
    {
        $tanggal = $request->input('tanggal');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $obat_id = $request->input('obat_id');
        $ruangan = auth()->user()->ruangan; // <-- Ambil dari user yang login

        $pemakaianList = PemakaianObat::with(['obat.jenisObat'])
            ->whereHas('user', fn($q) => $q->where('ruangan', $ruangan))
            ->when($tanggal, fn($q) => $q->whereDay('tanggal', $tanggal))
            ->when($bulan, fn($q) => $q->whereMonth('tanggal', $bulan))
            ->when($tahun, fn($q) => $q->whereYear('tanggal', $tahun))
            ->when($obat_id, fn($q) => $q->where('obat_id', $obat_id))
            ->orderBy('tanggal', 'asc') // urutkan dari tanggal lama ke baru
            ->get();
        // Buat rekap total pemakaian berdasarkan obat
        $rekapTotal = PemakaianObat::select('obat_id', DB::raw('SUM(stok_keluar) as total_disetujui'))
            ->whereHas('user', fn($q) => $q->where('ruangan', $ruangan))
            ->when($tanggal, fn($q) => $q->whereDay('tanggal', $tanggal))
            ->when($bulan, fn($q) => $q->whereMonth('tanggal', $bulan))
            ->when($tahun, fn($q) => $q->whereYear('tanggal', $tahun))
            ->when($obat_id, fn($q) => $q->where('obat_id', $obat_id))
            ->groupBy('obat_id')
            ->with('obat.jenisObat')
            ->get();

        return view('operator.cetakpemakaian', compact(
            'pemakaianList',
            'rekapTotal',
            'tanggal',
            'bulan',
            'tahun',
            'ruangan'
        ));
        // Kirim juga $ruangan ke view
        return view('operator.cetakpemakaian', compact('pemakaianList', 'tanggal', 'bulan', 'tahun', 'ruangan'));
    }

    // laporan cetak pemakaian operator
    public function laporanPemakaian(Request $request)
    {
        $query = PemakaianObat::query()->with('obat');

        // Filter berdasarkan tanggal (1-31)
        if ($request->filled('tanggal')) {
            $query->whereDay('tanggal', $request->tanggal);
        }

        // Filter bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }

        // Filter tahun
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        // Filter berdasarkan obat
        if ($request->filled('obat_id')) {
            $query->where('obat_id', $request->obat_id);
        }

        $pemakaianList = $query->orderBy('tanggal', 'desc')->get();
        $obatList = Obat::all();
        $tahunList = PemakaianObat::selectRaw('YEAR(tanggal) as tahun')->distinct()->pluck('tahun');

        return view('operator.lappemakaian', compact('pemakaianList', 'obatList', 'tahunList'));
    }
    // laporan pemakaian admin
    public function laporanPemakaianAdmin(Request $request)
    {
        $query = PemakaianObat::with(['obat', 'user']);

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        // Jika ruangan diisi dan bukan "Semua", maka filter
        if ($request->filled('ruangan') && $request->ruangan !== 'semua') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('ruangan', $request->ruangan);
            });
        }

        if ($request->filled('obat_id')) {
            $query->where('obat_id', $request->obat_id);
        }

        $pemakaianList = $query->orderByDesc('tanggal')->get();
        $obatList = Obat::all();
        $tahunList = PemakaianObat::selectRaw('YEAR(tanggal) as tahun')->distinct()->pluck('tahun');

        // Tambahkan opsi "Semua"
        $instansiList = [
            'semua' => 'Semua Puskesmas',
            'puskesmas Kaligangsa' => 'puskesmas Kaligangsa',
            'puskesmas Margadana' => 'puskesmas Margadana',
            'puskesmas Tegal Barat' => 'puskesmas Tegal Barat',
            'puskesmas Debong Lor' => 'puskesmas Debong Lor',
            'puskesmas Tegal Timur' => 'puskesmas Tegal Timur',
            'puskesmas Slerok' => 'puskesmas Slerok',
            'puskesmas Tegal Selatan' => 'puskesmas Tegal Selatan',
            'puskesmas Bandung' => 'puskesmas Bandung',
        ];

        return view('laporan.lappemakaian', compact('pemakaianList', 'obatList', 'tahunList', 'instansiList'));
    }

    public function cetakPemakaianAdmin(Request $request)
    {
        $bulan   = $request->input('bulan');
        $tahun   = $request->input('tahun');
        $ruangan = $request->input('ruangan') ?? 'Semua Ruangan';
        $obat_id = $request->input('obat_id');

        // Rekap total
        $rekapTotal = PemakaianObat::select('obat_id', DB::raw('SUM(stok_keluar) as total_disetujui'))
            ->when($ruangan && $ruangan !== 'Semua Ruangan', function ($q) use ($ruangan) {
                $q->whereHas('user', function ($sub) use ($ruangan) {
                    $sub->where('ruangan', $ruangan);
                });
            })

            ->when($bulan, fn($q) => $q->whereMonth('tanggal', $bulan))
            ->when($tahun, fn($q) => $q->whereYear('tanggal', $tahun))
            ->when($obat_id, fn($q) => $q->where('obat_id', $obat_id))
            ->groupBy('obat_id')
            ->with('obat.jenisObat')
            ->get();

        // Detail pemakaian
        $pemakaianList = PemakaianObat::with(['obat.jenisObat'])
            ->when($ruangan && $ruangan !== 'Semua Ruangan', function ($q) use ($ruangan) {
                $q->whereHas('user', function ($sub) use ($ruangan) {
                    $sub->where('ruangan', $ruangan);
                });
            })

            ->when($bulan, fn($q) => $q->whereMonth('tanggal', $bulan))
            ->when($tahun, fn($q) => $q->whereYear('tanggal', $tahun))
            ->when($obat_id, fn($q) => $q->where('obat_id', $obat_id))
            ->orderBy('tanggal', 'asc')
            ->get();

        return view('laporan.cetak_pemakaian', compact(
            'rekapTotal',
            'pemakaianList',
            'bulan',
            'tahun',
            'ruangan',
            'obat_id'
        ));
    }
}
