@extends('layouts.app')

@section('content')
    <div class="container py-3">
        <div class="card">
            <div class="card-header">
                <h4>Edit Obat</h4>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="card-body">
                <form action="{{ route('obat.update', $obat->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <!-- Left Column for Photo -->
                        <div class="col-md-4 text-center">
                            <div class="form-group">
                                <label for="foto"></label>
                                <!-- Display existing photo if available -->
                                <div>
                                    @if ($obat->foto)
                                        <img src="{{ asset('storage/obat/' . $obat->foto) }}" alt="Foto Obat"
                                            class="img-fluid custom-photo mt-2" width="200">
                                    @else
                                        <img src="https://via.placeholder.com/200" alt="Foto tidak tersedia"
                                            class="img-fluid custom-photo mt-2" width="200">
                                    @endif
                                </div>
                                <input type="file" name="foto" class="form-control mt-2"
                                    onchange="previewImage(event)">
                                <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti foto.</small>
                            </div>
                        </div>

                        <!-- Right Column for Other Inputs -->
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="nama_obat">Nama Obat</label>
                                <input type="text" name="nama_obat" class="form-control" placeholder="Masukkan nama obat"
                                    value="{{ old('nama_obat', $obat->nama_obat) }}" required>
                            </div>
                            <div class="form-group">
                                <label for="dosis">Dosis</label>
                                <input type="text" name="dosis" class="form-control"
                                    placeholder="Masukkan dosis obat (contoh: 100 Mg)"
                                    value="{{ old('dosis', $obat->dosis) }}" required>
                            </div>
                            <div class="form-group">
                                <label for="jenis">Jenis</label>
                                <select name="jenis_obat_id" class="form-control" required>
                                    <option value="" disabled>Pilih jenis obat</option>
                                    @foreach ($jenisObat as $jenis)
                                        <option value="{{ $jenis->id }}"
                                            {{ $obat->jenis_obat_id == $jenis->id ? 'selected' : '' }}>
                                            {{ $jenis->nama_jenis }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="harga">Harga (Rp)</label>
                                <input type="number" name="harga" class="form-control"
                                    placeholder="Masukkan harga obat (contoh: 10000)"
                                    value="{{ old('harga', $obat->harga) }}" required>
                            </div>
                            <div class="form-group">
                                <label for="stok">Stok</label>
                                <input type="number" name="stok" class="form-control"
                                    placeholder="Masukkan jumlah stok obat ini (contoh: 1000)"
                                    value="{{ old('stok', $obat->stok) }}" readonly>
                            </div>

                            <div class="form-group">
                                <label for="exp">Tanggal Kadaluwarsa</label>
                                <input type="date" name="exp" class="form-control"
                                    value="{{ old('exp', $obat->exp) }}" required>
                            </div>
                            <div class="form-group">
                                <label for="keterangan">Keterangan Obat</label>
                                <textarea name="keterangan" class="form-control" id="keterangan" rows="3" placeholder="Masukkan keterangan obat">{{ old('keterangan', $obat->keterangan) }}</textarea>
                            </div>
                            <br>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('obat.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* Mengatur ukuran lingkaran dan memastikan gambar tidak peyang */
        .custom-photo {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 15px;
        }
    </style>

    <script>
        function previewImage(event) {
            const output = document.querySelector('.form-group img');
            output.src = URL.createObjectURL(event.target.files[0]);
        }

        // Initialize CKEditor for the 'keterangan' textarea
        CKEDITOR.replace('keterangan');
    </script>
@endsection
