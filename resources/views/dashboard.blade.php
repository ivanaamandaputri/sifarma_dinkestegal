@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <h1 class="mt-4">Dashboard</h1>
    <span class="op-7" style="font-size: 18px">Halo,</span>
    <span class="fw-bold" style="font-size: 20px">{{ Auth::user()->nama_pegawai }}!😊</span>
    <span class="op-7" style="font-size: 18px"> disini anda sebagai</span>
    <span class="fw-bold" style="font-size: 20px">{{ Auth::user()->level }} {{ Auth::user()->ruangan }}</span>
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
                        <span>Jumlah Permintaan</span>
                    </div>
                    <div class="h5 font-weight-bold mb-0">{{ $jumlahTransaksi }}</div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small stretched-link text-white" href="/pengajuan">Lihat Detail</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
            <br>
        </div>

        <!-- Rekap Stok Obat Disetujui -->
        <div class="container py-3">
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title mb-0">Rekap Stok Obat</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatablesSimple" class="table-hover table align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Obat</th>
                                    <th>Total Order Disetujui </th>
                                    <th>Total Order</th>
                                    <th>Sisa Stok</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($obat as $item)
                                    @php
                                        // Ambil rekap transaksi berdasarkan obat_id
                                        $rekap = $rekapTransaksi->firstWhere('obat_id', $item->id);
                                        $totalDisetujui = $rekap ? $rekap->total_disetujui : 0; // Total order (Rp)
                                        $totalAcc = $rekap ? $rekap->total_acc : 0; // Jumlah disetujui
                                    @endphp
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>
                                            {{ $item->nama_obat }} - {{ $item->dosis }} -
                                            {{ $item->jenisObat->nama_jenis ?? 'Tidak Ditemukan' }}
                                        </td>
                                        <td>{{ $totalAcc }}</td> <!-- Jumlah Order Disetujui -->
                                        <td>Rp {{ number_format($totalDisetujui, 0, ',', '.') }}</td>
                                        <!-- Total Order (Rp) -->
                                        <td>
                                            {{ $item->stok }}
                                            @if ($item->stok == 0)
                                                <span class="badge bg-danger">Stok Habis!</span>
                                            @elseif ($item->stok < 5)
                                                <span class="badge bg-warning">Hampir Habis!</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('obat.show', $item->id) }}"
                                                class="btn btn-primary btn-sm">Detail Obat</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endsection
