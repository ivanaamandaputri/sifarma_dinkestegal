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
                <h4 class="card-title mb-0">Filter Laporan Pemakaian Obat Puskesmas</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.laporan.pemakaian') }}" method="GET">
                    <div class="row g-3">

                        <!-- Bulan -->
                        <div class="col-md-2">
                            <label for="bulan" class="form-label">Bulan</label>
                            <select name="bulan" id="bulan" class="form-select">
                                <option value="">Semua Bulan</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <!-- Tahun -->
                        <div class="col-md-2">
                            <label for="tahun" class="form-label">Tahun</label>
                            <select name="tahun" id="tahun" class="form-select">
                                <option value="">Semua Tahun</option>
                                @foreach ($tahunList as $tahun)
                                    <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>
                                        {{ $tahun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Puskesmas / Ruangan -->
                        <div class="col-md-3">
                            <label for="ruangan" class="form-label">Puskesmas</label>
                            <select name="ruangan" id="ruangan" class="form-select">
                                <option value="">Semua Puskesmas</option>
                                @foreach ($instansiList as $instansi)
                                    <option value="{{ $instansi }}"
                                        {{ request('ruangan') == $instansi ? 'selected' : '' }}>
                                        {{ $instansi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Obat -->
                        <div class="col-md-3">
                            <label for="obat_id" class="form-label">Obat</label>
                            <select name="obat_id" id="obat_id" class="form-select">
                                <option value="">Semua Obat</option>
                                @foreach ($obatList as $obat)
                                    <option value="{{ $obat->id }}"
                                        {{ request('obat_id') == $obat->id ? 'selected' : '' }}>
                                        {{ $obat->nama_obat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tombol Filter -->
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Terapkan Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Section -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title mb-0">Laporan Pemakaian Obat</h4>
                <a href="{{ route('laporan.pemakaian.cetak', [
                    'bulan' => request('bulan'),
                    'tahun' => request('tahun'),
                    'ruangan' => request('ruangan'),
                    'obat_id' => request('obat_id'),
                ]) }}"
                    class="btn btn-success">Cetak Laporan</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatablesSimple" class="table-hover table align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Puskesmas</th>
                                <th>Pasien</th>
                                <th>Nama Obat</th>
                                <th>Jumlah</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pemakaianList as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                                    <td>{{ $item->user->ruangan ?? '-' }}</td>
                                    <td>{{ $item->nama_pasien }}</td>
                                    <td>
                                        {{ $item->obat->nama_obat }}
                                        @if ($item->obat->dosis)
                                            - {{ $item->obat->dosis }}
                                        @endif
                                        @if ($item->obat->jenisObat)
                                            ({{ $item->obat->jenisObat->nama_jenis }})
                                        @endif
                                    </td>
                                    <td>{{ number_format($item->stok_keluar, 0, ',', '.') }}</td>
                                    <td>{{ $item->keterangan ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data pada filter yang dipilih.</td>
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
