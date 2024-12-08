@extends('layouts.app')

@section('content')
    <div class="container py-3">
        <div class="container-fluid d-flex justify-content-between">
            <h4 class="card-title">Data Obat</h4>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
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
                                <th>Harga</th>
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
                                    <td>{{ $item->jenisObat->nama_jenis }}</td>
                                    <td>{{ $item->harga }}</td>
                                    <td>
                                        {{ $item->stok }}
                                        @if ($item->stok == 0)
                                            <span class="badge badge-danger">
                                                Stok Habis!
                                            </span>
                                        @elseif ($item->stok < 5)
                                            <span class="badge badge-warning">
                                                Hampir Habis!
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('operator.showobat', $item->id) }}" class="btn btn-primary">Detail
                                            Obat</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            /* Kesan timbul 3D */
        }
    </style>
@endsection
