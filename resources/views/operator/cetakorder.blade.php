<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Permintaan Obat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 20px;
            max-width: 900px;
            margin: 0 auto;
            border: 1px solid #000;
        }

        .kop-surat {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .kop-surat img {
            width: 80px;
            height: auto;
            margin-right: 20px;
        }

        .kop-surat .title {
            font-size: 18px;
            font-weight: bold;
        }

        h1 {
            text-align: center;
            font-size: 24px;
            margin: 10px 0;
        }

        .sub-title {
            text-align: center;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .rekap-table,
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .rekap-table th,
        .data-table th {
            background-color: #f4f4f4;
            border: 1px solid #000;
            text-align: left;
            padding: 8px;
        }

        .rekap-table td,
        .data-table td {
            border: 1px solid #000;
            padding: 8px;
        }

        .footer {
            margin-top: 40px;
            font-size: 14px;
            font-weight: bold;
            /* Menambahkan tebal pada footer secara keseluruhan */
            text-align: center;
            /* Agar teks footer terpusat */
        }

        .footer .signature {
            margin-top: 15px;
            /* Menyesuaikan jarak atas */
            font-weight: normal;
            /* Untuk memastikan bagian tanda tangan tidak tebal */
            line-height: 1.4;
            /* Menambah jarak antar paragraf */
        }

        .footer .signature p {
            margin: 3px 0;
            /* Mengatur jarak atas dan bawah antara elemen */
            font-size: 14px;
            /* Menyesuaikan ukuran font */
        }

        .footer .signature p:last-child {
            margin-bottom: 0;
            /* Menghilangkan jarak bawah pada elemen terakhir */
        }

        .footer .signature p b {
            font-weight: bold;
            /* Menebalkan bagian tertentu saja, seperti nama pegawai atau NIP */
        }

        /* Media Query for Print */
        @media print {
            body {
                font-size: 12px;
            }

            .container {
                max-width: 100%;
                padding: 0;
                border: none;
            }

            .kop-surat img {
                width: 60px;
            }

            .footer,
            .signature {
                text-align: left;
            }

            .footer {
                margin-top: 40px;
            }

            /* Hide buttons or unnecessary elements during print */
            button,
            .no-print {
                display: none;
            }
        }

        /* Responsive design for small screens */
        @media (max-width: 600px) {
            .container {
                padding: 10px;
            }

            h1 {
                font-size: 20px;
            }

            .rekap-table th,
            .data-table th,
            .rekap-table td,
            .data-table td {
                padding: 6px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Kop Surat -->
        <div class="kop-surat">
            <img src="{{ asset('img/dinkes.png') }}" alt="Logo Dinkes">
            <div class="title">
                DINAS KESEHATAN KOTA TEGAL<br>
                INSTALASI FARMASI
            </div>
        </div>

        <br>
        <!-- Judul -->
        <h1>Laporan Permintaan Obat {{ auth()->user()->ruangan }}</h1>
        <p>Bulan: {{ $bulan }} | Tahun: {{ $tahun }} | Ruangan: {{ $ruangan }}</p>

        <!-- Rekap Total Permintaan -->
        <table class="rekap-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Obat</th>
                    <th>Total Disetujui</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rekapTotal as $index => $rekap)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            {{ $rekap->obat->nama_obat }} - {{ $rekap->obat->dosis }}
                            ({{ $rekap->obat->jenisObat ? $rekap->obat->jenisObat->nama_jenis : '' }})
                        </td>
                        <td>{{ number_format($rekap->total_disetujui, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h2 style="font-weight: bold;">Detail Permintaan Disetujui</h2>

        <!-- Tabel Data -->
        <!-- Tabel Data -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama Obat</th>
                    <th>Jumlah</th>
                    {{-- <th>Status</th> --}}
                    <th>Instansi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rekapTransaksi as $index => $transaksi)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($transaksi->tanggal)->translatedFormat('d F Y') }}</td>
                        <td>{{ $transaksi->obat->nama_obat }} - {{ $transaksi->obat->dosis }}
                            ({{ $transaksi->obat->jenisObat->nama_jenis }})
                        </td>
                        <td>
                            @if ($transaksi->status == 'Disetujui')
                                @if ($transaksi->jumlah_akhir == $transaksi->jumlah_acc)
                                    {{ number_format($transaksi->jumlah_akhir, 0, ',', '.') }}
                                @else
                                    {{ number_format($transaksi->jumlah_akhir - $transaksi->jumlah_acc, 0, ',', '.') }}
                                @endif
                            @else
                                {{ number_format($transaksi->jumlah_akhir, 0, ',', '.') }}
                            @endif
                        </td>
                        {{-- <td>{{ $transaksi->status }}</td> --}}
                        <td>{{ $transaksi->user->ruangan }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p>Tegal, {{ \Carbon\Carbon::now()->formatLocalized('%d %B %Y') }}</p> <!-- Format tanggal Indonesia -->
            <div class="signature">
                <p><b>Operator {{ auth()->user()->ruangan }}</b></p> <!-- Menebalkan nama ruangan -->
                <br> <br> <br>
                <p>______________________</p>
                <p><b>{{ auth()->user()->nama_pegawai }}</b></p> <!-- Menebalkan nama pegawai -->
                <p><b>NIP {{ auth()->user()->nip }}</b></p> <!-- Menebalkan NIP -->
            </div>
        </div>


        <script>
            window.onload = function() {
                window.print();
            };
        </script>
</body>

</html>
