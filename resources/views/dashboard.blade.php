@extends('layouts.app')

@section('content')
    <h1 class="mt-4">Dashboard</h1>
    <span class="op-7" style="font-size: 18px">Halo,</span>
    <span class="fw-bold" style="font-size: 20px">{{ Auth::user()->level }}</span>
    </span>
    </ol>
    <ol>
    </ol>
    <div class="row">
        <!-- Card Jumlah Obat -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary mb-4 text-white">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fas fa-pills fa-2x me-3"></i>
                        <span>Jumlah Obat</span>
                    </div>
                    <div class="h5 font-weight-bold mb-0">{{ $jumlahObat }}</div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small stretched-link text-white" href="/obat">Lihat Detail</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <!-- Card Jumlah User -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning mb-4 text-white">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fas fa-users fa-2x me-3"></i>
                        <span>Jumlah User</span>
                    </div>
                    <div class="h5 font-weight-bold mb-0">{{ $jumlahUser }}</div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small stretched-link text-white" href="/user">Lihat Detail</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <!-- Card Jumlah Transaksi -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success mb-4 text-white">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fas fa-shopping-cart fa-2x me-3"></i>
                        <span>Jumlah Transaksi</span>
                    </div>
                    <div class="h5 font-weight-bold mb-0">{{ $jumlahTransaksi }}</div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small stretched-link text-white" href="/laporan">Lihat Detail</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    @endsection
