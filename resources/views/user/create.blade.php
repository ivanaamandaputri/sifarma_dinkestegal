@extends('layouts.app')

@section('content')
    <div class="container py-3">
        <div class="card">
            <div class="card-header">
                <h4>Tambah User Baru</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('user.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- Kolom Kiri untuk Foto -->
                        <div class="col-md-4 text-center">
                            <div class="form-group">
                                <label for="foto"></label>
                                <!-- Placeholder untuk foto pengguna baru -->
                                <div>
                                    <img src="https://via.placeholder.com/200" alt="Foto tidak tersedia"
                                        class="img-fluid custom-photo mt-2" width="200">
                                </div>
                                <input type="file" name="foto" class="form-control mt-2"
                                    onchange="previewImage(event)">
                            </div>
                        </div>

                        <!-- Kolom Kanan untuk Input Lainnya -->
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="nip">NIP</label>
                                <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror"
                                    placeholder="Masukkan NIP" required>
                                @error('nip')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="nama_pegawai">Nama Pegawai</label>
                                <input type="text" name="nama_pegawai"
                                    class="form-control @error('nama_pegawai') is-invalid @enderror"
                                    placeholder="Masukkan Nama Pegawai" required>
                                @error('nama_pegawai')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="jabatan">Jabatan</label>
                                <select name="jabatan" class="form-control @error('jabatan') is-invalid @enderror" required>
                                    <option value="" selected disabled>Pilih Jabatan</option>
                                    <option value="Kepala Apotik">Kepala Apotik</option>
                                    <option value="Apoteker">Apoteker</option>
                                    <option value="Staf">Staf</option>
                                </select>
                                @error('jabatan')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="ruangan">Ruangan</label>
                                <select name="ruangan" class="form-control @error('ruangan') is-invalid @enderror" required>
                                    <option value="" selected disabled>Pilih Ruangan</option>
                                    <option value="Instalasi Farmasi">Instalasi Farmasi</option>
                                    <option value="puskesmas Kaligangsa">Puskesmas Kaligangsa</option>
                                    <option value="puskesmas Margadana">Puskesmas Margadana</option>
                                    <option value="puskesmas Tegal Barat">Puskesmas Tegal Barat</option>
                                    <option value="puskesmas Debong Lor">Puskesmas Debong Lor</option>
                                    <option value="puskesmas Tegal Timur">Puskesmas Tegal Timur</option>
                                    <option value="puskesmas Slerok">Puskesmas Slerok</option>
                                    <option value="puskesmas Tegal Selatan">Puskesmas Tegal Selatan</option>
                                    <option value="puskesmas Bandung">Puskesmas Bandung</option>
                                </select>
                                @error('ruangan')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="level">Level</label>
                                <select name="level" class="form-control @error('level') is-invalid @enderror" required>
                                    <option value="" selected disabled>Pilih Level</option>
                                    <option value="admin">Admin</option>
                                    <option value="operator">Operator</option>
                                </select>
                                @error('level')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Masukkan Password" required>
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="konfirmasi_password">Konfirmasi Password</label>
                                <input type="password" name="konfirmasi_password"
                                    class="form-control @error('konfirmasi_password') is-invalid @enderror"
                                    placeholder="Konfirmasi Password" required>
                                @error('konfirmasi_password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <br>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('user.index') }}" class="btn btn-secondary">Batal</a>
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
            const output = document.querySelector('.custom-photo');
            output.src = URL.createObjectURL(event.target.files[0]);
        }
    </script>
@endsection
