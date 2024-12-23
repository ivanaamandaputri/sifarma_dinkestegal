@extends('layouts.app')

@section('content')
    <div class="container py-3">
        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title mb-0">Filter Laporan Permintaan Obat</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('laporan.order') }}" method="GET">
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
                            <select name="tahun" class="form-select">
                                <option value="">Semua Tahun</option>
                                @if ($tahunList->isEmpty())
                                    <option value="" disabled>Tidak ada data tahun</option>
                                @else
                                    @foreach ($tahunList as $tahun)
                                        <option value="{{ $tahun }}"
                                            {{ request('tahun') == $tahun ? 'selected' : '' }}>
                                            {{ $tahun }}
                                        </option>
                                    @endforeach
                                @endif
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
                                        {{ $obat->nama_obat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Terapkan Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Section -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title mb-0">Laporan Permintaan Obat</h4>
                <a href="{{ route('operator.cetakorder') }}" class="btn btn-success">Cetak Laporan</a>
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
                                <th>Status</th>
                                <th>Instansi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rekapTransaksi as $index => $transaksi)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
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
                                    <td>{{ $transaksi->status }}</td>
                                    <td>{{ $transaksi->user->ruangan }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada permintaan order pada seleksi yang
                                        dipilih</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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
