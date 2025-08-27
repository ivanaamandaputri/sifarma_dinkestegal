@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h4><i class="fas fa-plus-circle"></i> Tambah Pemakaian Obat</h4>

        {{-- Tampilkan Error --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('pemakaian-obat.store') }}" method="POST" class="mt-3">
            @csrf

            {{-- Obat --}}
            <div class="mb-3">
                <label for="obat_id" class="form-label">Nama Obat</label>
                <select name="obat_id" id="obat_id" class="form-select" required>
                    <option value="">-- Pilih Obat --</option>
                    @foreach ($stokPuskesmas as $item)
                        <option value="{{ $item->obat->id }}" data-stok="{{ $item->jumlah }}"
                            {{ old('obat_id') == $item->obat->id ? 'selected' : '' }}>
                            {{ $item->obat->nama_obat }} (Stok: {{ $item->jumlah }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Nama Pasien --}}
            <div class="mb-3">
                <label for="nama_pasien" class="form-label">Nama Pasien</label>
                <input type="text" name="nama_pasien" class="form-control" value="{{ old('nama_pasien') }}" required>
            </div>

            {{-- Stok --}}
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Stok Masuk</label>
                    <input type="number" id="stok_awal" class="form-control" readonly value="{{ old('stok_awal') }}">
                    <input type="hidden" name="stok_awal" id="stok_awal_hidden" value="{{ old('stok_awal') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Stok Keluar</label>
                    <input type="number" name="stok_keluar" id="stok_keluar" class="form-control"
                        value="{{ old('stok_keluar') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Stok Sisa</label>
                    <input type="number" id="stok_sisa" class="form-control" readonly value="{{ old('stok_sisa') }}">
                </div>
            </div>

            {{-- Tanggal --}}
            <div class="mb-3">
                <label for="tanggal" class="form-label">Tanggal Pemakaian</label>
                <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal') ?? date('Y-m-d') }}"
                    required>
            </div>

            {{-- Keterangan --}}
            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea name="keterangan" rows="3" class="form-control">{{ old('keterangan') }}</textarea>
            </div>

            {{-- Tombol Aksi --}}
            <div class="d-flex justify-content-start gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <a href="{{ route('pemakaian-obat.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>

    <script>
        // Ambil stok saat pilih obat
        document.getElementById('obat_id').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const stokAwal = selected.getAttribute('data-stok');
            document.getElementById('stok_awal').value = stokAwal;
            document.getElementById('stok_awal_hidden').value = stokAwal;
            document.getElementById('stok_keluar').value = '';
            document.getElementById('stok_sisa').value = '';
        });

        // Hitung stok sisa
        document.getElementById('stok_keluar').addEventListener('input', function() {
            const stokAwal = parseInt(document.getElementById('stok_awal').value || 0);
            const keluar = parseInt(this.value || 0);
            const sisa = stokAwal - keluar;
            document.getElementById('stok_sisa').value = sisa >= 0 ? sisa : 0;
        });
    </script>
@endsection
