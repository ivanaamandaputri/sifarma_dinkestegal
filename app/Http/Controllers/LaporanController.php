<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Obat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    private function filterTransaksi(Request $request)
    {
        // Query dasar dengan status
        $query = Transaksi::with('obat', 'user')
            ->whereIn('status', ['selesai', 'disetujui', 'retur']);

        // Filter berdasarkan bulan
        if ($request->has('bulan') && $request->bulan != '') {
            $query->whereMonth('tanggal', $request->bulan);
        }

        // Filter berdasarkan tahun
        if ($request->has('tahun') && $request->tahun != '') {
            $query->whereYear('tanggal', $request->tahun);
        }

        // Filter berdasarkan instansi (ruangan)
        if ($request->has('ruangan') && $request->ruangan != '') {
            $query->whereHas('user', function ($query) use ($request) {
                $query->where('ruangan', $request->ruangan);
            });
        }

        // Filter berdasarkan obat
        if ($request->has('obat_id') && $request->obat_id != '') {
            $query->where('obat_id', $request->obat_id);
        }

        return $query;
    }

    public function index(Request $request)
    {
        // Gunakan fungsi filter
        $query = $this->filterTransaksi($request);

        // Ambil data transaksi dengan pagination
        $laporanTransaksi = $query->orderBy('tanggal', 'asc')->paginate(10);

        // Ambil daftar obat untuk filter
        $obatList = Obat::all();

        // Daftar instansi yang tersedia
        $instansiList = [
            'Puskesmas Kaligangsa',
            'Puskesmas Margadana',
            'Puskesmas Tegal Barat',
            'Puskesmas Debong Lor',
            'Puskesmas Tegal Timur',
            'Puskesmas Slerok',
            'Puskesmas Tegal Selatan',
            'Puskesmas Bandung',
        ];

        // Kirim data ke view
        return view('laporan.index', compact('laporanTransaksi', 'obatList', 'instansiList'));
    }

    // public function cetak(Request $request)
    // {
    //     // dd($request);
    //     // Gunakan fungsi filter
    //     $query = $this->filterTransaksi($request);

    //     // Ambil data transaksi tanpa pagination
    //     $laporanTransaksi = $query->orderBy('tanggal', 'asc')->get();

    //     // Kirim data ke view untuk cetak
    //     return view('laporan.cetak', compact('laporanTransaksi'));
    // }

    public function cetak(Request $request)
    {
        $query = Transaksi::with('obat') // Gunakan relasi
            ->select('transaksi.*')
            ->orderBy('tanggal', 'asc');

        // Filter berdasarkan bulan
        if ($request->has('bulan') && $request->bulan != '') {
            $query->whereMonth('tanggal', $request->bulan);
        }

        // Filter berdasarkan tahun
        if ($request->has('tahun') && $request->tahun != '') {
            $query->whereYear('tanggal', $request->tahun);
        }

        // Filter berdasarkan ruangan
        if ($request->has('ruangan') && $request->ruangan != '') {
            $query->where('ruangan', $request->ruangan);
        }

        // Filter berdasarkan obat
        if ($request->has('obat_id') && $request->obat_id != '') {
            $query->where('obat_id', $request->obat_id);
        }

        // Ambil data
        $laporanTransaksi = $query->get();

        return view('laporan.cetak', compact('laporanTransaksi'));
    }
}
