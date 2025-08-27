@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h4 class="mb-4 text-center">Laporan Rekap Pemakaian Obat</h4>

        <table class="table-bordered table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Obat</th>
                    <th>Puskesmas</th>
                    <th>Stok Masuk</th>
                    <th>Stok Keluar</th>
                    <th>Sisa Stok</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rekapPemakaian as $i => $data)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $data->nama_obat }}</td>
                        <td>{{ $data->nama_puskesmas }}</td>
                        <td>{{ $data->stok_awal }}</td>
                        <td>{{ $data->stok_keluar }}</td>
                        <td>{{ $data->stok_sisa }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <script>
            window.print(); // langsung print saat halaman terbuka
        </script>
    </div>
@endsection
