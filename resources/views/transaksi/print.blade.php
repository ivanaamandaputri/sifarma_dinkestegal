<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi Obat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .text-center {
            text-align: center;
        }

        .mt-4 {
            margin-top: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #000;
        }

        th {
            background-color: #f2f2f2;
        }

        .btn {
            padding: 10px 15px;
            margin-top: 20px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
            border: none;
            cursor: pointer;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
                font-family: Arial, sans-serif;
            }

            .container {
                width: 100%;
                padding: 0;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            th,
            td {
                padding: 10px;
                border: 1px solid #000;
            }

            th {
                background-color: #f2f2f2;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="mb-4 text-center">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 100px;">
            <h4>Laporan Pemesanan Obat Bulanan</h4>
            <p>{{ date('d F Y') }}</p>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Obat</th>
                    <th>Dosis</th>
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <th>Harga (Rp)</th>
                    <th>Total</th>
                    <th>Nama Pemesan</th>
                    <th>Ruangan</th>
                    <th>Tanggal Transaksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($laporanTransaksi as $transaksi)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $transaksi->obat->nama_obat }}</td>
                        <td>{{ $transaksi->obat->dosis }}</td>
                        <td>{{ $transaksi->obat->jenis }}</td>
                        <td>{{ $transaksi->jumlah }}</td>
                        <td>{{ $transaksi->obat->harga }}</td>
                        <td>{{ $transaksi->total }}</td>
                        <td>{{ $transaksi->user->nama_pegawai }}</td>
                        <td>{{ $transaksi->user->ruangan }}</td>
                        <td>{{ $transaksi->created_at->format('d-m-Y H:i:s') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 text-right">
            <button onclick="window.print();" class="btn btn-primary">Print</button>
        </div>
    </div>
</body>

</html>
