@extends('layouts.app')

@section('content')
    <div class="container py-4">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="mb-4">
            <button id="btn-gudang" class="btn btn-outline-secondary me-2">Obat Gudang Farmasi</button>
            <button id="btn-puskesmas" class="btn btn-outline-primary">Obat Puskesmas</button>
        </div>

        <!-- OBAT GUDANG -->
        <div class="card mb-4" id="gudang-table">
            <div class="card-header bg-secondary text-white">
                <h4 class="card-title mb-0">Data Obat Gudang Farmasi</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatablesGudang" class="table-hover table align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Obat</th>
                                <th>Stok</th>
                                <th>Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($obatGudang as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->nama_obat }} - {{ $item->dosis }}
                                        ({{ optional($item->jenisObat)->nama_jenis ?? '-' }})
                                    </td>
                                    <td>
                                        @if ($item->stok == 0)
                                            0 <span class="badge bg-danger">Stok Habis!</span>
                                        @elseif ($item->stok <= 10)
                                            {{ $item->stok }} <span class="badge bg-warning">Stok Krisis!</span>
                                        @elseif ($item->stok <= 100)
                                            {{ $item->stok }} <span class="badge bg-dark text-white">Stok Menipis</span>
                                        @else
                                            {{ number_format($item->stok) }}
                                        @endif
                                    </td>
                                    <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                    <td>
                                        <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#detailModalGudang{{ $item->id }}">
                                            <i class="fas fa-eye"></i> Detail
                                        </button>
                                    </td>
                                </tr>

                                <!-- Modal Gudang -->
                                <div class="modal fade" id="detailModalGudang{{ $item->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Detail Obat Gudang</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row align-items-start">
                                                    <div class="col-md-4 text-center">
                                                        @if ($item->foto)
                                                            <img src="{{ asset('storage/obat/' . $item->foto) }}"
                                                                alt="{{ $item->nama_obat }}"
                                                                class="img-fluid rounded shadow-sm">
                                                        @else
                                                            <img src="https://via.placeholder.com/200"
                                                                class="img-fluid rounded shadow-sm">
                                                        @endif
                                                    </div>
                                                    <div class="col-md-8">
                                                        <h5 class="fw-bold mb-3">{{ $item->nama_obat }}
                                                            {{ $item->dosis }}
                                                            ({{ optional($item->jenisObat)->nama_jenis ?? '-' }})</h5>
                                                        <ul class="list-unstyled">
                                                            <li>
                                                                Stok Gudang:
                                                                {{ number_format($item->stok) }}
                                                            </li>
                                                            <li>
                                                                Harga: Rp
                                                                {{ number_format($item->harga, 0, ',', '.') }}
                                                            </li>
                                                            <li>
                                                                Keterangan:
                                                                <div class="bg-light mt-1 rounded p-2">
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

        <!-- OBAT PUSKESMAS -->
        <div class="card card-obat d-none mb-4" id="puskesmas-table">
            <div class="card-header bg-primary text-white">
                <h4 class="card-title mb-0">Data Obat Puskesmas</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table-hover table-bordered table align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Obat</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($obatPuskesmas as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->obat->nama_obat ?? '-' }} - {{ $item->obat->dosis ?? '-' }}
                                        ({{ optional($item->obat->jenisObat)->nama_jenis ?? '-' }})
                                    </td>
                                    <td>
                                        @php $jumlahStok = $item->jumlah; @endphp
                                        @if (is_null($jumlahStok))
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
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                                            data-bs-target="#detailModalPuskesmas{{ $item->id }}">
                                            <i class="fas fa-eye"></i> Detail
                                        </button>
                                    </td>
                                </tr>

                                <!-- Modal Puskesmas -->
                                <div class="modal fade" id="detailModalPuskesmas{{ $item->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Detail Obat Puskesmas</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row align-items-start">
                                                    <div class="col-md-4 text-center">
                                                        @if ($item->obat->foto)
                                                            <img src="{{ asset('storage/obat/' . $item->obat->foto) }}"
                                                                alt="{{ $item->obat->nama_obat }}"
                                                                class="img-fluid rounded shadow-sm">
                                                        @else
                                                            <img src="https://via.placeholder.com/200"
                                                                class="img-fluid rounded shadow-sm">
                                                        @endif
                                                    </div>
                                                    <div class="col-md-8">
                                                        <h5 class="fw-bold mb-3">{{ $item->obat->nama_obat ?? '-' }}
                                                            {{ $item->obat->dosis ?? '' }}
                                                            ({{ optional($item->obat->jenisObat)->nama_jenis ?? '-' }})
                                                        </h5>
                                                        <ul class="list-unstyled">
                                                            <li>
                                                                Stok Puskesmas:
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
                                                            <li>Harga: Rp
                                                                {{ number_format($item->obat->harga ?? 0, 0, ',', '.') }}
                                                            </li>
                                                            <li>
                                                                Keterangan:
                                                                <div class="bg-light mt-1 rounded p-2">
                                                                    {!! $item->obat->keterangan ?? '-' !!}
                                                                </div>
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


        {{-- Style --}}
        <style>
            .card-obat {
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                border-radius: 10px;
            }

            .table-hover tbody tr:hover {
                background-color: #f8f9fa;
            }

            .badge {
                font-size: 0.8rem;
                padding: 0.35em 0.6em;
            }

            img {
                max-height: 220px;
                object-fit: cover;
            }
        </style>

        {{-- Script Toggle --}}
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const btnGudang = document.getElementById("btn-gudang");
                const btnPuskesmas = document.getElementById("btn-puskesmas");
                const gudangTable = document.getElementById("gudang-table");
                const puskesmasTable = document.getElementById("puskesmas-table");

                btnGudang.addEventListener("click", () => {
                    gudangTable.classList.remove("d-none");
                    puskesmasTable.classList.add("d-none");
                });

                btnPuskesmas.addEventListener("click", () => {
                    puskesmasTable.classList.remove("d-none");
                    gudangTable.classList.add("d-none");
                });
            });
        </script>
    @endsection
