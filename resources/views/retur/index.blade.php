@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Data Retur</h1>

        @if ($transaksiId)
            <h3>Retur untuk Transaksi ID: {{ $transaksiId }}</h3>
        @endif

        @if ($returs->isEmpty())
            <p>Tidak ada data retur.</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Transaksi ID</th>
                        <th>Obat ID</th>
                        <th>Jumlah</th>
                        <th>Alasan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($returs as $retur)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $retur->transaksi_id }}</td>
                            <td>{{ $retur->obat_id }}</td>
                            <td>{{ $retur->jumlah }}</td>
                            <td>{{ $retur->alasan_retur }}</td>
                            <td>
                                <a href="{{ route('retur.show', $retur->id) }}" class="btn btn-info">Detail</a>
                                <a href="{{ route('retur.edit', $retur->id) }}" class="btn btn-warning">Edit</a>
                                <form action="{{ route('retur.destroy', $retur->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
