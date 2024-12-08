<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi Obat Keluar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f7f7f7;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 28px;
            margin: 0;
            color: #333;
        }

        .header p {
            font-size: 16px;
            margin-top: 5px;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        td {
            background-color: #ffffff;
        }

        .badge {
            padding: 4px 8px;
            color: #fff;
            border-radius: 4px;
            font-size: 12px;
        }

        .badge-light {
            background-color: #f8f9fa;
            color: #333;
        }

        .badge-success {
            background-color: #28a745;
        }

        .badge-warning {
            background-color: #ffc107;
            color: black;
        }

        .badge-selesai {
            background-color: #f8f9fa;
            color: #000;
        }

        .footer {
            margin-top: 40px;
            font-size: 14px;
            font-weight: bold;
        }

        .footer .ttd-section {
            margin-top: 30px;
            text-align: right;
            font-size: 14px;
        }

        .footer .ttd-section .signature {
            margin-top: 50px;
            font-size: 14px;
            text-align: right;
        }

        .print-btn {
            margin: 20px 0;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .print-btn:hover {
            background-color: #0056b3;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .container {
                padding: 0;
            }

            .header h1 {
                font-size: 22px;
            }

            .footer {
                font-size: 12px;
                margin-top: 30px;
            }

            table {
                font-size: 12px;
                margin-top: 20px;
            }

            th,
            td {
                padding: 6px;
            }

            .badge {
                padding: 4px 6px;
                font-size: 10px;
            }

            .print-btn {
                display: none;
            }
        }

        .rekapan-section {
            display: flex;
            justify-content: space-between;
        }

        .rekapan-section>div {
            width: 48%;
        }

        .rekapan-section table {
            width: 100%;
            margin-top: 10px;
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <h1>Laporan Transaksi Obat Keluar</h1>
            {{-- <p>Bulan: {{ $bulan }} | Tahun: {{ $tahun }} | Ruangan: {{ $ruangan }} | Obat:
                {{ $obat ?? 'Semua Obat' }}</p> --}}
        </div>

        <!-- Print Button -->
        <button class="print-btn" onclick="window.print()">Cetak Laporan</button>

        <!-- Rekapan Total Per Obat dan Rekapan Status -->
        <div class="rekapan-section">
            <div>
                <h3>Rekapan Total Per Obat</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Nama Obat</th>
                            <th>Total Jumlah</th>
                            <th>Total Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($laporanTransaksi->groupBy('obat_id') as $obatId => $transaksiGroup)
                            <tr>
                                <td>{{ $transaksiGroup->first()->obat->nama_obat ?? 'Data Obat Tidak Ada' }}</td>
                                </td>
                                <td>{{ $transaksiGroup->sum('acc') }}</td>
                                <td>{{ number_format($transaksiGroup->sum('total'), 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div>
                <h3>Rekapan Status Transaksi</h3>
                <table>
                    <tbody>
                        <tr>
                            <td><span class="badge badge-selesai">Selesai</span></td>
                            <td>{{ $laporanTransaksi->where('status', 'selesai')->count() }} transaksi selesai</td>
                        </tr>
                        <tr>
                            <td><span class="badge badge-success">Disetujui</span></td>
                            <td>{{ $laporanTransaksi->where('status', 'disetujui')->count() }} transaksi disetujui</td>
                        </tr>
                        <tr>
                            <td><span class="badge badge-warning">Retur</span></td>
                            <td>{{ $laporanTransaksi->where('status', 'retur')->count() }} transaksi retur</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Table Section -->
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama Obat</th>
                    <th>Jumlah</th>
                    <th>Harga</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Instansi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($laporanTransaksi as $index => $transaksi)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $transaksi->tanggal->formatLocalized('%e %B %Y') }}</td>
                        <td>{{ $transaksi->obat->nama_obat }} - {{ $transaksi->obat->dosis }} -
                            {{ $transaksi->obat->jenisObat->nama_jenis }}</td>
                        <td>{{ $transaksi->acc }}</td>
                        <td>{{ number_format($transaksi->obat->harga, 0, ',', '.') }}</td>
                        <td>{{ number_format($transaksi->total, 0, ',', '.') }}</td>
                        <td>
                            <span
                                class="badge {{ $transaksi->status == 'selesai' ? 'badge-selesai' : ($transaksi->status == 'disetujui' ? 'badge-success' : 'badge badge-light') }}">
                                {{ ucfirst($transaksi->status) }}
                            </span>
                        </td>
                        <td>{{ $transaksi->user->ruangan }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-secondary">
                <tr>
                    <th colspan="5" class="text-end">Grand Total</th>
                    <th>{{ number_format($laporanTransaksi->sum('total'), 0, ',', '.') }}</th>
                    <th colspan="2"></th>
                </tr>
            </tfoot>
        </table>

        <!-- Footer Section -->
        <div class="footer">
            <p>Grand Total: {{ number_format($laporanTransaksi->sum('total'), 0, ',', '.') }}</p>
            <div class="ttd-section">
                <p>Tanda Tangan</p>
                <p class="signature">______________________</p>
                <p>Kepala Instalasi Farmasi</p>
            </div>
        </div>
    </div>

</body>

</html>
