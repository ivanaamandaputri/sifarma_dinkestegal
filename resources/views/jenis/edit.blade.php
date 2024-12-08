@extends('layouts.app')

@section('content')
    <div class="container py-3">
        <div class="card">
            <div class="card-header">
                <h4>Edit Jenis Obat</h4>
            </div>
            <div class="card-body">
                <!-- Form untuk mengedit jenis obat -->
                <form action="{{ route('jenis_obat.update', $jenisObat) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="nama_jenis">Nama Jenis</label>
                        <input type="text" class="form-control @error('nama_jenis') is-invalid @enderror" id="nama_jenis"
                            name="nama_jenis" value="{{ old('nama_jenis', $jenisObat->nama_jenis) }}" required
                            placeholder="Masukkan nama jenis obat">

                        <!-- Menampilkan pesan error khusus untuk nama_jenis -->
                        @error('nama_jenis')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <br>
                    <button type="submit" class="btn btn-primary">Perbarui</button>
                    <a href="{{ route('jenis_obat.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
@endsection
