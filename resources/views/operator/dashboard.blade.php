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
                <span class="fw-bold" style="font-size: 20px;">{{ Auth::user()->nama_pegawai }}!</span>
                <span style="font-size: 18px;">Anda login sebagai</span>
                <span class="fw-bold" style="font-size: 20px;">{{ Auth::user()->level }} {{ Auth::user()->ruangan }}</span>
            </div>
        </div>

        <div class="row mb-4">
            <!-- Kartu Info -->
            <div class="col-xl-3 col-md-6">
                <div class="card bg-secondary mb-4 text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-pills fa-2x me-3"></i>
                            <span>Obat Gudang Farmasi</span>
                        </div>
                        <div class="h5 font-weight-bold mb-0">{{ $totalObat }}</div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small stretched-link text-white" href="/operator/dataobat">Lihat Detail</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary mb-4 text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-briefcase-medical fa-2x me-3"></i>
                            <span>Obat Puskesmas</span>
                        </div>
                        <div class="h5 font-weight-bold mb-0">{{ number_format($jumlahObatPuskesmas) }}</div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small stretched-link text-white" href="/operator/dataobat">Lihat Detail</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-warning mb-4 text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-clock fa-2x me-3"></i>
                            <span>Order Belum Diproses</span>
                        </div>
                        <div class="h5 font-weight-bold mb-0">{{ $orderMenunggu }}</div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small stretched-link text-white" href="/transaksi">Lihat Detail</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Rekap -->
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title mb-0">Stok Obat</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatablesSimple" class="table-hover table align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Obat</th>
                                <th>Stok Gudang Farmasi</th>
                                <th>Stok {{ Auth::user()->ruangan }}</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($obat as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->nama_obat }} - {{ $item->dosis }} ({{ $item->jenisObat->nama_jenis }})
                                    </td>
                                    <td>{{ number_format($item->stok) }}</td>
                                    <td>
                                        @php
                                            $jumlahStok = $item->jumlah;
                                            $rekapAda = $rekapTransaksi->firstWhere('obat_id', $item->id);
                                        @endphp

                                        @if (!$rekapAda)
                                            -
                                        @elseif ($jumlahStok == 0)
                                            0 <span class="badge bg-danger">Stok Habis!</span>
                                        @elseif ($jumlahStok <= 10)
                                            {{ $jumlahStok }} <span class="badge bg-warning">Stok Krisis!</span>
                                        @elseif ($jumlahStok <= 100)
                                            {{ $jumlahStok }} <span class="badge bg-dark text-white">Stok Menipis</span>
                                        @else
                                            {{ $jumlahStok }}
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#detailModal{{ $item->id }}">
                                            <i class="fas fa-eye"></i> Detail
                                        </button>
                                        <a href="{{ route('transaksi.create', ['obat_id' => $item->id]) }}"
                                            class="btn btn-success btn-sm">
                                            <i class="fas fa-shopping-cart"></i> Order
                                        </a>
                                    </td>
                                </tr>
                                <!-- Modal Detail Obat -->
                                <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1"
                                    aria-labelledby="detailModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Detail Obat</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row align-items-start">
                                                    <div class="col-md-4 text-center">
                                                        @if ($item->foto)
                                                            <img src="{{ asset('storage/obat/' . $item->foto) }}"
                                                                alt="Foto {{ $item->nama_obat }}"
                                                                class="img-fluid rounded shadow-sm">
                                                        @else
                                                            <img src="https://via.placeholder.com/200"
                                                                alt="Foto Tidak Tersedia"
                                                                class="img-fluid rounded shadow-sm">
                                                        @endif
                                                    </div>
                                                    <div class="col-md-8">
                                                        <h5 class="fw-bold mb-3">
                                                            {{ $item->nama_obat }} {{ $item->dosis }}
                                                            ({{ $item->jenisObat->nama_jenis ?? 'Jenis Tidak Diketahui' }})
                                                        </h5>

                                                        <ul class="list-unstyled">
                                                            <li class="d-flex">
                                                                <span class="me-2" style="min-width: 140px;">Stok
                                                                    Gudang:</span>
                                                                <span>{{ number_format($item->stok) }}</span>
                                                            </li>
                                                            <li class="d-flex">
                                                                <span class="me-2" style="min-width: 140px;">Stok
                                                                    Puskesmas:</span>
                                                                <span>
                                                                    @php $jumlahStok = $item->jumlah; @endphp
                                                                    @if (is_null($jumlahStok))
                                                                        -
                                                                    @elseif ($jumlahStok == 0)
                                                                        0 <span class="badge bg-danger">Stok Habis!</span>
                                                                    @elseif ($jumlahStok <= 10)
                                                                        {{ $jumlahStok }} <span
                                                                            class="badge bg-warning">Stok Krisis!</span>
                                                                    @elseif ($jumlahStok <= 100)
                                                                        {{ $jumlahStok }} <span
                                                                            class="badge bg-dark text-white">Stok
                                                                            Menipis</span>
                                                                    @else
                                                                        {{ $jumlahStok }}
                                                                    @endif
                                                                </span>
                                                            </li>
                                                            <li class="d-flex">
                                                                <span class="me-2" style="min-width: 140px;">Harga:</span>
                                                                <span>Rp
                                                                    {{ number_format($item->harga, 0, ',', '.') }}</span>
                                                            </li>
                                                            <li class="d-flex">
                                                                <span class="me-2"
                                                                    style="min-width: 140px;">Keterangan:</span>
                                                                <div class="bg-light flex-grow-1 mt-1 rounded border p-2">
                                                                    {!! $item->keterangan !!}</div>
                                                            </li>
                                                        </ul>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                        paging: true,
                        searching: true,
                        ordering: true,
                        pageLength: 5
                    });
                });
            </script>
        @endpush
    </div>
@endsection
