<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JenisObatController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\PemakaianObatController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReturController;
use App\Http\Controllers\StokMasukController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UserController;
use App\Models\PemakaianObat;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('landing');
    // return view('auth/login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Rute untuk dashboard, admin, dan operator
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/operator', [DashboardController::class, 'operator'])->name('dashboard.operator'); // Rute untuk operator
});

// Rute untuk admin
Route::middleware(['auth', 'check.level:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::resource('user', UserController::class);
    Route::resource('obat', ObatController::class);
    Route::resource('jenis_obat', JenisObatController::class);
    Route::get('/jenis-obat/{id}/edit', [JenisObatController::class, 'edit'])->name('jenis_obat.edit');
    Route::resource('transaksi', TransaksiController::class);
    Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
    Route::post('/transaksi/approve/{id}', [PengajuanController::class, 'approve'])->name('transaksi.approve');
    Route::post('/transaksi/reject/{id}', [PengajuanController::class, 'reject'])->name('transaksi.reject');
    Route::post('/obat/{id}/tambah-stok', [StokMasukController::class, 'tambahStok'])->name('obat.tambahStok');
    Route::get('/lihat-pemakaian', [PemakaianObatController::class, 'index'])->name('pemakaian.index');
    Route::get('/lihat-pemakaian/{id}/detail', [PemakaianObatController::class, 'show'])->name('pemakaian.show');
    Route::get('/pemakaian/cetak', [LaporanController::class, 'cetak'])->name('pemakaian.cetak');
    Route::get('/admin/profile', [ProfileController::class, 'indexAdmin'])->name('profile.admin.index');
    Route::get('/admin/profile/edit/{user}', [ProfileController::class, 'editAdmin'])->name('profile.admin.edit');
    Route::put('/admin/profile/edit/{user}', [ProfileController::class, 'updateAdmin'])->name('profile.admin.update');
    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/cetak', [LaporanController::class, 'cetak'])->name('laporan.cetak');
    Route::get('/admin/laporan/pemakaian', [LaporanController::class, 'laporanPemakaianAdmin'])->name('admin.laporan.pemakaian');
    Route::get('/laporan/pemakaian/cetak', [LaporanController::class, 'cetakPemakaianAdmin'])
        ->name('laporan.pemakaian.cetak');


    // Route::get('/dashboard/notifikasi', [PengajuanController::class, 'getNotifikasi'])->name('dashboard.notifikasi');
    // Route::get('/notifikasi/baca/{id}', [PengajuanController::class, 'bacaNotifikasi'])->name('notifikasi.baca');
});

// Rute untuk operator
Route::middleware(['auth', 'check.level:operator'])->group(function () {
    Route::get('/dashboard/operator', [DashboardController::class, 'operator'])->name('dashboard.operator');
    Route::get('/operator/dataobat', [ObatController::class, 'operatorIndex'])->name('operator.dataobat');
    Route::get('/operator/showobat/{id}', [ObatController::class, 'operatorShowobat'])->name('operator.showobat');
    Route::resource('transaksi', TransaksiController::class);
    Route::get('/transaksi/{id}/print', [TransaksiController::class, 'print'])->name('transaksi.print');
    Route::post('/transaksi/finish/{id}', [TransaksiController::class, 'finish'])->name('transaksi.finish');
    Route::get('/transaksi/detail/{id}', [TransaksiController::class, 'detail'])->name('transaksi.detail');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit/{user}', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/edit/{user}', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/operator/laporder', [LaporanController::class, 'laporder'])->name('laporan.operator');
    Route::get('/operator/cetakorder', [LaporanController::class, 'cetakOrder'])->name('operator.cetakorder');
    Route::get('/laporan/order', [LaporanController::class, 'laporder'])->name('laporan.order');
    Route::post('/transaksi/{id}/retur', [TransaksiController::class, 'retur'])->name('transaksi.retur');
    Route::get('transaksi/{id}/retur', [TransaksiController::class, 'getRetur'])->name('transaksi.getRetur');
    Route::get('/operator/rekap', [DashboardController::class, 'rekapTransaksi'])->name('operator.rekap');
    Route::resource('pemakaian-obat', PemakaianObatController::class)->except(['edit', 'update']);
    // Route::get('/laporan/pemakaian', [LaporanController::class, 'laporanPemakaian'])->name('laporan.pemakaian');
    Route::get('/operator/laporan-pemakaian', [LaporanController::class, 'laporanPemakaian'])->name('laporan.pemakaian');
    Route::get('/operator/laporan-pemakaian/cetak', [LaporanController::class, 'cetakPemakaian'])->name('operator.cetakpemakaian');
});
