@extends('layouts.app')

@section('content')
    <div class="container py-4">

        {{-- Judul halaman --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="fw-bold text-darkr">
                Obat Keluar
            </h2>
        </div>

        {{-- Notifikasi Sukses --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Tombol Tambah --}}
        <div class="my-3">
            <a href="{{ route('pemakaian-obat.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Tambah Pemakaian
            </a>
        </div>

        {{-- Kartu Tabel --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header border-bottom bg-white">
                <h5 class="card-title mb-0">Data Pemakaian Obat - Ruangan: {{ Auth::user()->ruangan }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatablesSimple" class="table-hover table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Pasien</th>
                                <th class="text-dark">Nama Obat</th>
                                <th class="text-success">Stok Masuk</th>
                                <th class="text-danger">Stok Keluar</th>
                                <th class="text-primary">Sisa Stok</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pemakaian as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                                    <td>{{ $item->nama_pasien }}</td>
                                    <td>{{ $item->obat->nama_obat }}</td>
                                    <td>{{ $item->stok_awal }}</td>
                                    <td>{{ $item->stok_keluar }}</td>
                                    <td>{{ $item->stok_sisa }}</td>
                                    <td>{{ $item->keterangan ?? '-' }}</td>
                                    <td>
                                        <form action="{{ route('pemakaian-obat.destroy', $item->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-muted text-center">Belum ada data pemakaian.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
