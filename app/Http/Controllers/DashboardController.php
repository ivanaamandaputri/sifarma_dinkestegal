<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard()
    {
        // Menghitung jumlah masing-masing model untuk admin
        $jumlahObat = Obat::count();
        $jumlahTransaksi = Transaksi::count();
        $jumlahUser = User::count();

        return view('dashboard', compact('jumlahObat', 'jumlahTransaksi', 'jumlahUser'));
    }

    public function operator()
    {
        // Menampilkan dashboard khusus untuk operator
        $totalObat = Obat::count();
        return view('operator.dashboard', compact('totalObat'));
        return redirect()->route('obat.index.operator');
    }

    public function index()
    {
        // Cek level user dan arahkan ke dashboard yang sesuai
        if (Auth::user()->level == 'operator') {
            return redirect()->route('dashboard.operator'); // Redirect ke method operator
        }

        // Jika bukan operator, arahkan ke dashboard admin
        return $this->dashboard();
    }
}
