@extends('layouts.app')

@section('content')
    <div class="container py-3">
        <div class="container-fluid d-flex justify-content-between">
            <h4 class="card-title">Data Obat</h4>
            @if (!$readOnly)
                <!-- Jika bukan operator, tampilkan tombol tambah -->
                <a href="{{ route('obat.create') }}" class="btn btn-primary mb-3">Tambah Obat</a>
            @endif
        </div>

        <!-- Notifikasi Sukses -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Notifikasi Error -->
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatablesSimple" class="table-hover table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Obat</th>
                                <th>Dosis</th>
                                <th>Jenis</th>
                                <th>Harga (Rp)</th>
                                <th>Exp</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($obat as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->nama_obat }}</td>
                                    <td>{{ $item->dosis }}</td>
                                    <td>{{ $item->jenisObat->nama_jenis ?? 'Tidak Ditemukan' }}</td>
                                    <td>{{ number_format($item->harga, 0, ',', '.') }}</td>
                                    <td>
                                        {{ $item->exp? \Carbon\Carbon::parse($item->exp)->locale('id')->translatedFormat('j M Y'): 'Tidak tersedia' }}
                                        @if ($item->expWarning)
                                            <span
                                                class="badge {{ $item->expWarning == 'Sudah Kedaluwarsa' ? 'bg-danger' : 'bg-warning' }}">
                                                {{ $item->expWarning }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ number_format($item->stok, 0, ',', '.') }}
                                        @if ($item->stok == 0)
                                            <span class="badge bg-danger">
                                                {{ auth()->user()->level == 'admin' ? 'Stok Habis, Restok Segera!' : 'Stok Habis!' }}
                                            </span>
                                        @elseif ($item->stok < 1000)
                                            <span class="badge bg-warning">
                                                {{ auth()->user()->level == 'admin' ? 'Hampir Habis, Restok Segera!' : 'Hampir Habis!' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <!-- Tombol Detail -->
                                        <a href="{{ route('obat.show', $item->id) }}"
                                            class="btn btn-info btn-sm">Detail</a>

                                        @if (!$readOnly)
                                            <!-- Tombol Edit -->
                                            <a href="{{ route('obat.edit', $item->id) }}"
                                                class="btn btn-warning btn-sm">Edit</a>

                                            <!-- Tombol Tambah Stok -->
                                            <button class="btn btn-success btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modalTambahStok{{ $item->id }}">Tambah Stok</button>


                                            <!-- Tombol Hapus -->
                                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modalHapus{{ $item->id }}"
                                                @if ($item->stokMasuk()->exists()) disabled @endif>
                                                Hapus
                                            </button>
                                        @endif
                                    </td>

                                </tr>

                                <!-- Modal Konfirmasi Hapus -->
                                <div class="modal fade" id="modalHapus{{ $item->id }}" tabindex="-1"
                                    aria-labelledby="modalHapusLabel{{ $item->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalHapusLabel{{ $item->id }}">Konfirmasi
                                                    Hapus</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                @if (!$item->stokMasuk()->exists())
                                                    AAnda yakin ingin menghapus data obat
                                                    <strong>{{ $item->nama_obat }}</strong>?
                                                    Data yang sudah dihapus tidak dapat dikembalikan.
                                                @else
                                                    Data obat <strong>{{ $item->nama_obat }}</strong> tidak bisa dihapus
                                                    karena masih memiliki stok masuk yang terkait.
                                                @endif
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Batal</button>
                                                @if (!$item->stokMasuk()->exists())
                                                    <form action="{{ route('obat.destroy', $item->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                                    </form>
                                                @endif
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




    @foreach ($obat as $item)
        <!-- Modal Tambah Stok -->
        <div class="modal fade" id="modalTambahStok{{ $item->id }}" tabindex="-1"
            aria-labelledby="modalTambahStokLabel{{ $item->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahStokLabel{{ $item->id }}">Tambah Stok untuk
                            {{ $item->nama_obat }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('obat.tambahStok', $item->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="jumlah" class="form-label">Jumlah Stok</label>
                                <input type="number" name="jumlah" id="jumlah" class="form-control" min="1"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="sumber" class="form-label">Sumber Stok (Opsional)</label>
                                <input type="text" name="sumber" id="sumber" class="form-control"
                                    placeholder="Contoh: Supplier A">
                            </div>
                            <div class="mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" name="tanggal" id="tanggal" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">Tambah Stok</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- JS Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            /* Kesan timbul 3D */
        }
    </style>
@endsection
