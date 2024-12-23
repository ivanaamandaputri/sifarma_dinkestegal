@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <!-- Header -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <div class="greeting mb-4">
            <h1 class="mt-4">Dashboard</h1>
            <div class="greeting-text">
                <span style="font-size: 18px;">Halo,</span>
                <span class="fw-bold" style="font-size: 20px;">
                    {{ Auth::user()->nama_pegawai }}!😊
                </span>
                <span style="font-size: 18px;">disini anda sebagai</span>
                <span class="fw-bold" style="font-size: 20px;">
                    {{ Auth::user()->level }}
                    {{ Auth::user()->ruangan }}
                </span>
            </div>
        </div>

        <div class="row mb-4">
            <!-- Data Obat GF -->
            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary mb-4 text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <i class="fas fa-pills fa-2x me-3"></i>
                            <span>Data Obat GF</span>
                        </div>
                        <div class="h5 font-weight-bold mb-0">{{ $totalObat }}</div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small stretched-link text-white" href="/operator/dataobat">Lihat Detail</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>

            <!-- Total Order -->
            <div class="col-xl-3 col-md-6">
                <div class="card bg-success mb-4 text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <i class="fas fa-shopping-cart fa-2x me-3"></i>
                            <span>Total Order</span>
                        </div>
                        <div class="h5 font-weight-bold mb-0">{{ $jumlahTransaksi }}</div> <!-- Tampilkan Total Order -->
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small stretched-link text-white" href="/transaksi">Lihat Detail</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Obat Table -->
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title mb-0">Data Obat Gudang Farmasi</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatablesSimple" class="table-hover table align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Informasi Obat</th> <!-- Kolom gabungan -->
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($obat as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $item->nama_obat }} - {{ $item->dosis }} ({{ $item->jenisObat->nama_jenis }})
                                    </td>
                                    <td>
                                        {{ $item->stok }}
                                        @if ($item->stok == 0)
                                            <span class="badge bg-danger">Stok Habis!</span>
                                        @elseif ($item->stok < 5)
                                            <span class="badge bg-warning">Hampir Habis!</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('operator.showobat', $item->id) }}"
                                            class="btn btn-primary btn-sm">Detail Obat</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Rekap Order Obat Table -->
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title mb-0">Rekap Order Obat {{ Auth::user()->ruangan }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatablesSimple1" class="table-hover table align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Informasi Obat</th> <!-- Kolom gabungan -->
                                <th>Total Disetujui ✅</th>
                            </tr>
                        <tbody>
                            @foreach ($rekapTransaksi as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $item->obat->nama_obat }} - {{ $item->obat->dosis }}
                                        ({{ $item->obat->jenisObat->nama_jenis }})
                                    </td>
                                    <td>{{ number_format($item->total_disetujui, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <style>
            .greeting {
                font-size: 18px;
                color: #333;
            }

            .greeting-text {
                font-size: 18px;
                color: #555;
            }

            .user-info {
                font-size: 20px;
                font-weight: bold;
                color: #000;
            }

            .card {
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                border-radius: 10px;
            }

            .table thead {
                background-color: #f8f9fa;
                font-weight: bold;
            }

            .badge {
                font-size: 0.9rem;
                padding: 0.4em 0.6em;
            }

            .table-hover tbody tr:hover {
                background-color: #f1f1f1;
            }
        </style>

        @push('scripts')
            <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
            <script>
                $(document).ready(function() {
                    $('#datatablesSimple').DataTable({
                        "paging": true,
                        "searching": true,
                        "ordering": true,
                        "pageLength": 5
                    });

                    $('#datatablesSimple1').DataTable({
                        "paging": true,
                        "searching": true,
                        "ordering": true,
                        "pageLength": 5
                    });
                });
            </script>
        @endpush
    </div>
@endsection
