@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h4 class="mb-4">
            <i class="fas fa-syringe"></i> Data Pemakaian Total Obat Pukesmas
        </h4>

        {{-- Alert Message --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Filter Form --}}
        <form method="GET" action="{{ route('pemakaian.index') }}" class="row g-3 mb-4">
            <div class="col-md-5">
                <label for="filterPuskesmas" class="form-label">Pilih Puskesmas</label>
                <select name="puskesmas" id="filterPuskesmas" class="form-select">
                    <option value="">Semua Puskesmas</option>
                    @foreach ($puskesmasList as $puskesmas)
                        <option value="{{ $puskesmas }}" {{ request('puskesmas') == $puskesmas ? 'selected' : '' }}>
                            {{ $puskesmas }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label for="filterObat" class="form-label">Pilih Obat</label>
                <select name="obat" id="filterObat" class="form-select">
                    <option value="">Semua Obat</option>
                    @foreach ($obatList as $id => $namaObat)
                        <option value="{{ $id }}" {{ request('obat') == $id ? 'selected' : '' }}>
                            {{ $namaObat }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter"></i> Tampilkan
                </button>
            </div>
        </form>

        {{-- Rekap Table --}}
        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table class="table-bordered table-hover table align-middle" id="tablePemakaian">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Obat</th>
                            <th>Nama Puskesmas</th>
                            <th>Stok Masuk</th> {{-- stok_awal --}}
                            <th>Stok Keluar</th>
                            <th>Sisa Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapPemakaian as $index => $data)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $data->nama_obat }}</td>
                                <td>{{ $data->nama_puskesmas }}</td>
                                <td>{{ $data->stok_awal }}</td>
                                <td>{{ $data->stok_keluar }}</td>
                                <td>{{ $data->stok_sisa }}</td>
                                <td>
                                    <a href="{{ route('pemakaian.show', ['id' => $data->id]) }}?puskesmas={{ $data->nama_puskesmas }}"
                                        class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-history"></i> Detail History
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-muted text-center">Tidak ada data.</td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>
            </div>
        </div>
    </div>
@endsection
