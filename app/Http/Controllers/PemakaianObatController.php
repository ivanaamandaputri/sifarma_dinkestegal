<?php

namespace App\Http\Controllers;

use App\Models\PemakaianObat;
use App\Models\Obat;
use App\Models\StokPuskesmas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PemakaianObatController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->level == 'operator') {
            $pemakaian = PemakaianObat::with('obat')
                ->where('user_id', Auth::id())
                ->get();
            // Ambil stok terbaru dari tabel stok_puskesmas untuk setiap obat milik user
            $stokPuskesmas = StokPuskesmas::where('user_id', Auth::id())->get()->keyBy('obat_id');

            return view('pemakaian_operator.index', compact('pemakaian', 'stokPuskesmas'));
        }

        $selectedPuskesmas = $request->input('puskesmas');
        $selectedObat = $request->input('obat');

        $puskesmasList = \App\Models\User::where('level', 'operator')->pluck('ruangan')->unique();
        $obatList = Obat::pluck('nama_obat', 'id');

        $pemakaian = PemakaianObat::with(['obat', 'user'])
            ->when($selectedPuskesmas, function ($query) use ($selectedPuskesmas) {
                $query->whereHas('user', function ($q) use ($selectedPuskesmas) {
                    $q->where('ruangan', $selectedPuskesmas);
                });
            })
            ->when($selectedObat, function ($query) use ($selectedObat) {
                $query->where('obat_id', $selectedObat);
            })
            ->get();

        // Ambil data stok terkini dari stok_puskesmas
        $stokTerkini = \App\Models\StokPuskesmas::all()
            ->keyBy(fn($row) => $row->obat_id . '-' . $row->user_id);

        $rekapPemakaian = $pemakaian->groupBy(function ($item) {
            return $item->obat_id . '-' . $item->user_id;
        })->map(function ($group) use ($stokTerkini) {
            $first = $group->first();
            $key = $first->obat_id . '-' . $first->user_id;
            $totalKeluar = $group->sum('stok_keluar');
            $sisa = $stokTerkini[$key]->jumlah ?? 0;
            return (object)[
                'id' => $first->obat_id,
                'nama_obat' => $first->obat->nama_obat,
                'nama_puskesmas' => $first->user->ruangan,
                'stok_awal' => $totalKeluar + $sisa, // stok sebelum dipakai
                'stok_keluar' => $totalKeluar,
                'stok_sisa' => $sisa,
            ];
        })->values();


        return view('pemakaian_admin.index', compact(
            'rekapPemakaian',
            'puskesmasList',
            'obatList',
            'selectedPuskesmas',
            'selectedObat'
        ));
    }

    public function create()
    {
        if (Auth::user()->level != 'operator') {
            abort(403);
        }

        $stokPuskesmas = StokPuskesmas::with('obat')
            ->where('user_id', Auth::id())
            ->where('jumlah', '>', 0)
            ->get();

        return view('pemakaian_operator.create', compact('stokPuskesmas'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->level != 'operator') {
            abort(403);
        }

        $request->validate([
            'obat_id' => 'required|exists:obat,id',
            'nama_pasien' => 'required|string|max:255',
            'stok_keluar' => 'required|integer|min:1',
            'tanggal' => 'required|date',
        ]);

        $stok = StokPuskesmas::where('user_id', Auth::id())
            ->where('obat_id', $request->obat_id)
            ->first();

        $stokAwal = $stok->jumlah ?? 0;

        if ($request->stok_keluar > $stokAwal) {
            return back()->withInput()->withErrors([
                'stok_keluar' => 'Stok keluar tidak boleh melebihi stok tersedia (' . $stokAwal . ')',
            ]);
        }

        $stokSisa = $stokAwal - $request->stok_keluar;

        PemakaianObat::create([
            'obat_id' => $request->obat_id,
            'user_id' => Auth::id(),
            'nama_pasien' => $request->nama_pasien,
            'stok_awal' => $stokAwal,
            'stok_keluar' => $request->stok_keluar,
            'stok_sisa' => $stokSisa,
            'keterangan' => $request->keterangan,
            'tanggal' => $request->tanggal,
        ]);

        if ($stok) {
            $stok->jumlah = $stokSisa;
            $stok->save();
        }

        return redirect()->route('pemakaian-obat.index')->with('success', 'Data pemakaian berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        if (Auth::user()->level != 'operator') {
            abort(403);
        }

        $pemakaian = PemakaianObat::findOrFail($id);

        $stok = StokPuskesmas::where('user_id', Auth::id())
            ->where('obat_id', $pemakaian->obat_id)
            ->first();

        if ($stok) {
            $stok->jumlah += $pemakaian->stok_keluar;
            $stok->save();
        }

        $pemakaian->delete();

        return redirect()->route('pemakaian-obat.index')->with('success', 'Data pemakaian berhasil dihapus dan stok dikembalikan.');
    }

    public function show($id, Request $request)
    {
        $selectedPuskesmas = $request->input('puskesmas');

        $pemakaianList = PemakaianObat::where('obat_id', $id)
            ->with(['obat', 'user'])
            ->when($selectedPuskesmas, function ($query) use ($selectedPuskesmas) {
                $query->whereHas('user', function ($q) use ($selectedPuskesmas) {
                    $q->where('ruangan', $selectedPuskesmas);
                });
            })
            ->orderBy('tanggal', 'desc')
            ->get();

        if ($pemakaianList->isEmpty()) {
            return redirect()->back()->with('error', 'Data pemakaian tidak ditemukan.');
        }

        $namaObat = $pemakaianList->first()->obat->nama_obat;

        return view('pemakaian_admin.show', compact('pemakaianList', 'namaObat', 'selectedPuskesmas'));
    }
}
