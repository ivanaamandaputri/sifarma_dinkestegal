@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-dark d-flex justify-content-between align-items-center text-white">
                <h5 class="mb-0">Profil Admin</h5>
                <a href="{{ route('profile.admin.edit', $user->id) }}" class="btn btn-light btn-sm">
                    <i class="fas fa-edit me-1"></i> Edit Profil
                </a>
            </div>

            @if (session('success'))
                <div class="alert alert-success m-3">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card-body">
                <div class="row g-4 align-items-center">
                    <!-- Foto -->
                    <div class="col-md-4 text-center">
                        @if ($user->foto)
                            <img src="{{ asset('storage/user/' . $user->foto) }}" alt="Foto User"
                                class="img-fluid custom-photo shadow">
                        @else
                            <img src="https://via.placeholder.com/200" alt="No Foto" class="img-fluid custom-photo shadow">
                        @endif
                    </div>

                    <!-- Informasi -->
                    <div class="col-md-8">
                        <div class="row mb-3">
                            <label class="col-sm-4 fw-semibold">NIP</label>
                            <div class="col-sm-8 text-muted">{{ $user->nip ?? '-' }}</div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4 fw-semibold">Nama Pegawai</label>
                            <div class="col-sm-8 text-muted">{{ $user->nama_pegawai }}</div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4 fw-semibold">Jabatan</label>
                            <div class="col-sm-8 text-muted">{{ $user->jabatan }}</div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4 fw-semibold">Ruangan</label>
                            <div class="col-sm-8 text-muted">{{ $user->ruangan }}</div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4 fw-semibold">Level</label>
                            <div class="col-sm-8 text-muted">{{ ucfirst($user->level) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .custom-photo {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 15px;
        }

        @media (max-width: 768px) {
            .custom-photo {
                width: 150px;
                height: 150px;
            }
        }
    </style>
@endpush
