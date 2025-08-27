@extends('layouts.app')

@section('content')
    <div class="container py-3">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h4>Edit Profil Admin</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.admin.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <!-- Foto -->
                        <div class="col-md-4 text-center">
                            <div class="form-group">
                                <label for="foto"></label>
                                <div>
                                    @if ($user->foto)
                                        <img src="{{ asset('storage/user/' . $user->foto) }}" alt="Foto Admin"
                                            class="img-fluid custom-photo mt-2" width="200">
                                    @else
                                        <img src="https://via.placeholder.com/150" alt="Foto tidak tersedia"
                                            class="img-fluid custom-photo mt-2" width="200">
                                    @endif
                                </div>
                                <input type="file" name="foto" class="form-control mt-2"
                                    onchange="previewImage(event)">
                            </div>
                        </div>

                        <!-- Data Admin -->
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="nip">NIP</label>
                                <input type="text" name="nip" class="form-control" value="{{ $user->nip }}"
                                    required>
                                @error('nip')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="nama_pegawai">Nama Pegawai</label>
                                <input type="text" name="nama_pegawai" class="form-control"
                                    value="{{ $user->nama_pegawai }}" required>
                                @error('nama_pegawai')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="jabatan">Jabatan</label>
                                <input type="text" name="jabatan" class="form-control" value="{{ $user->jabatan }}"
                                    required>
                                @error('jabatan')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="ruangan">Ruangan</label>
                                <input type="text" name="ruangan" class="form-control" value="{{ $user->ruangan }}"
                                    required>
                                @error('ruangan')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="old_password">Password Lama</label>
                                <input type="password" id="old_password" name="old_password" class="form-control"
                                    placeholder="Kosongkan jika tidak ingin mengganti password">
                                @error('old_password')
                                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="new_password">Password Baru</label>
                                <input type="password" id="new_password" name="new_password" class="form-control"
                                    placeholder="Minimal 6 karakter">
                                @error('new_password')
                                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="new_password_confirmation">Konfirmasi Password Baru</label>
                                <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                                    class="form-control" placeholder="Konfirmasi password baru">
                                @error('new_password_confirmation')
                                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <br>
                            <button type="submit" class="btn btn-dark">Simpan Perubahan</button>
                            <a href="{{ route('profile.admin.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
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
    </script>
@endsection
