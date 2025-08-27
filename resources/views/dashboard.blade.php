@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <h1 class="mt-4">Dashboard</h1>
    <span class="op-7" style="font-size: 18px">Halo,</span>
    <span class="fw-bold" style="font-size: 20px">{{ Auth::user()->nama_pegawai }}!</span>
    <span class="op-7" style="font-size: 18px"> Anda login sebagai</span>
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
                        <span>Obat Gudang Farmasi</span>
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
            <div class="card bg-success mb-4 text-white">
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
            <div class="card bg-warning mb-4 text-white">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fas fa-shopping-cart fa-2x me-3"></i>
                        <span>Order Belum Diproses</span>
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

        <!-- Rekap Stok Obat -->
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
                                    <th>No</th>
                                    <th>Nama Obat</th>
                                    <th>Stok Gudang</th>
                                    <th>Puskesmas Kaligangsa</th>
                                    <th>Puskesmas Margadana</th>
                                    <th>Puskesmas Tegal Barat</th>
                                    <th>Puskesmas Debong Lor</th>
                                    <th>Puskesmas Tegal Timur</th>
                                    <th>Puskesmas Slerok</th>
                                    <th>Puskesmas Tegal Selatan</th>
                                    <th>Puskesmas Bandung</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($obat as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $item->nama_obat }} - {{ $item->dosis }} -
                                            {{ $item->jenisObat->nama_jenis ?? 'Tidak Ditemukan' }}
                                        </td>
                                        <td>
                                            {{ $item->stok }}
                                            @if ($item->stok == 0)
                                                <span class="badge bg-danger">Habis!</span>
                                            @elseif ($item->stok <= 10)
                                                <span class="badge bg-warning text-dark">Krisis!</span>
                                            @elseif ($item->stok <= 100)
                                                <span class="badge bg-dark text-white">Menipis</span>
                                            @endif
                                        </td>

                                        {{-- Loop berdasarkan ruangan user yang operator --}}
                                        @foreach (['puskesmas Kaligangsa', 'puskesmas Margadana', 'puskesmas Tegal Barat', 'puskesmas Debong Lor', 'puskesmas Tegal Timur', 'puskesmas Slerok', 'puskesmas Tegal Selatan', 'puskesmas Bandung'] as $ruangan)
                                            @php
                                                $jumlahStok =
                                                    $stokPuskesmas
                                                        ->where('obat_id', $item->id)
                                                        ->where('ruangan', $ruangan)
                                                        ->first()->jumlah ?? 0;
                                            @endphp
                                            <td class="text-center">
                                                @php
                                                    $stok = $stokPuskesmas->first(function ($stokItem) use (
                                                        $item,
                                                        $ruangan,
                                                    ) {
                                                        return $stokItem->obat_id == $item->id &&
                                                            $stokItem->ruangan == $ruangan;
                                                    });
                                                @endphp

                                                @if ($stok)
                                                    {{ $stok->jumlah }}
                                                    @if ($stok->jumlah > 0)
                                                        @if ($stok->jumlah <= 10)
                                                            <span class="badge bg-warning text-dark"> Krisis!</span>
                                                        @elseif ($stok->jumlah <= 100)
                                                            <span class="badge bg-dark text-white">Menipis</span>
                                                        @endif
                                                    @elseif ($stok->jumlah == 0)
                                                        <span class="badge bg-danger">Habis!</span>
                                                    @endif
                                                @else
                                                    -
                                                @endif

                                            </td>
                                        @endforeach
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#detailModal{{ $item->id }}">
                                                Detail
                                            </button>
                                        </td>
                                    </tr>
                                    <!-- Modal Detail Obat -->
                                    <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1"
                                        aria-labelledby="detailModalLabel{{ $item->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Detail Obat</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Tutup"></button>
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
                                                                    <span class="me-2"
                                                                        style="min-width: 140px;">Harga:</span>
                                                                    <span>Rp
                                                                        {{ number_format($item->harga, 0, ',', '.') }}</span>
                                                                </li>
                                                                <li class="d-flex">
                                                                    <span class="me-2"
                                                                        style="min-width: 140px;">Keterangan:</span>
                                                                    <div
                                                                        class="bg-light flex-grow-1 mt-1 rounded border p-2">
                                                                        {!! $item->keterangan !!}
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
        </div>
    @endsection
