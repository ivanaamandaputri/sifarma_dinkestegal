@extends('layouts.app')

@section('content')
    <div class="container py-3">
        <h4 class="mb-3">
            <i class="fas fa-history"></i> Riwayat Pemakaian Obat: {{ $namaObat }}
        </h4>

        @if ($selectedPuskesmas)
            <p class="text-muted">Ruangan: {{ $selectedPuskesmas }}</p>
        @endif


        <div class="card">
            <div class="card-body table-responsive">
                <table class="table-striped table" id="riwayatTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama Pasien</th>
                            <th>Puskesmas</th>
                            <th>Stok Masuk</th>
                            <th>Stok Keluar</th>
                            <th>Stok Sisa</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pemakaianList as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                                <td>{{ $item->nama_pasien }}</td>
                                <td>{{ $item->user->ruangan }}</td>
                                <td>{{ $item->stok_awal }}</td>
                                <td>{{ $item->stok_keluar }}</td>
                                <td>{{ $item->stok_sisa }}</td>
                                <td>{{ $item->keterangan ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tombol Kembali --}}
        <div class="mt-4">
            <a href="{{ route('pemakaian.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
@endsection
