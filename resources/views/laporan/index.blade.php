@extends('layouts.app')

@section('content')
    <div class="container py-3">

        <!-- Tombol Navigasi Halaman -->
        <div class="d-flex mb-4 gap-2">
            <a href="{{ route('laporan.index') }}"
                class="btn btn-outline-secondary {{ request()->is('laporan*') ? 'active' : '' }}">
                Laporan Permintaan Obat
            </a>
            <a href="{{ route('admin.laporan.pemakaian') }}"
                class="btn btn-outline-secondary {{ request()->is('admin/laporan/pemakaian*') ? 'active' : '' }}">
                Laporan Pemakaian Obat
            </a>
        </div>



        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title mb-0">Filter Laporan Transaksi Obat Keluar</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('laporan.index') }}" method="GET">
                    <div class="row g-3">
                        <!-- Filter Bulan -->
                        <div class="col-md-3">
                            <label for="bulan" class="form-label">Bulan</label>
                            <select name="bulan" id="bulan" class="form-select">
                                <option value="">Semua Bulan</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}"
                                        {{ old('bulan', request('bulan')) == $i ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <!-- Filter Tahun -->
                        <div class="col-md-3">
                            <label for="tahun" class="form-label">Tahun</label>
                            <select name="tahun" id="tahun" class="form-select">
                                <option value="">Semua Tahun</option>
                                @foreach ($tahunList as $tahun)
                                    <option value="{{ $tahun }}"
                                        {{ old('tahun', request('tahun')) == $tahun ? 'selected' : '' }}>
                                        {{ $tahun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Ruangan -->
                        <div class="col-md-3">
                            <label for="ruangan" class="form-label">Puskesmas</label>
                            <select name="ruangan" id="ruangan" class="form-select">
                                <option value="">Semua Puskesmas</option>
                                @foreach ($instansiList as $ruangan)
                                    <option value="{{ $ruangan }}"
                                        {{ old('ruangan', request('ruangan')) == $ruangan ? 'selected' : '' }}>
                                        {{ $ruangan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Obat -->
                        <div class="col-md-3">
                            <label for="obat_id" class="form-label">Obat</label>
                            <select name="obat_id" id="obat_id" class="form-select">
                                <option value="">Semua Obat</option>
                                @foreach ($obatList as $obat)
                                    <option value="{{ $obat->id }}"
                                        {{ old('obat_id', request('obat_id')) == $obat->id ? 'selected' : '' }}>
                                        {{ $obat->nama_obat }} - {{ $obat->dosis }} ({{ $obat->jenisObat->nama_jenis }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-md-3 d-flex justify-content-center align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Terapkan Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Section -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title mb-0">Laporan Transaksi Obat Keluar</h4>
                <a href="{{ route('laporan.cetak', request()->only(['bulan', 'tahun', 'ruangan', 'obat_id'])) }}"
                    target="_blank" class="btn btn-primary">
                    Cetak Laporan
                </a>

            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatablesSimple" class="table-hover table align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Nama Obat</th>
                                <th>Jumlah</th>
                                <th>Harga</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Instansi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rekapTransaksi as $key => $transaksi)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($transaksi->tanggal)->translatedFormat('d F Y') }}</td>
                                    <td>
                                        {{ $transaksi->obat->nama_obat }} - {{ $transaksi->obat->dosis }}
                                        ({{ $transaksi->obat->jenisObat->nama_jenis }})
                                    </td>
                                    <td>
                                        @if ($transaksi->status == 'Disetujui')
                                            @if ($transaksi->jumlah == $transaksi->acc)
                                                {{ number_format($transaksi->jumlah, 0, ',', '.') }}
                                            @else
                                                {{ number_format($transaksi->jumlah - $transaksi->acc, 0, ',', '.') }}
                                            @endif
                                        @else
                                            {{ number_format($transaksi->jumlah, 0, ',', '.') }}
                                        @endif
                                    </td>
                                    <td>Rp {{ number_format($transaksi->obat->harga, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($transaksi->total, 0, ',', '.') }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $transaksi->status == 'Disetujui' ? 'badge-success' : 'badge-warning' }} text-dark">
                                            {{ $transaksi->status }}
                                        </span>
                                    </td>
                                    <td>{{ $transaksi->user->ruangan }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        @push('scripts')
            <script>
                $(document).ready(function() {
                    $('#datatablesSimple').DataTable({
                        "paging": true,
                        "searching": true,
                        "ordering": true,
                        "responsive": true
                    });
                });
            </script>
        @endpush
    @endsection
