<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Pemakaian Obat</title>
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
            text-align: center;
        }

        .footer .signature {
            margin-top: 15px;
            font-weight: normal;
            line-height: 1.4;
        }

        .footer .signature p {
            margin: 3px 0;
            font-size: 14px;
        }

        @media print {

            button,
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="container">
        <!-- Kop Surat -->
        <div class="kop-surat">
            <img src="{{ asset('img/dinkes.png') }}" alt="Logo Dinkes">
            <div class="title">
                DINAS KESEHATAN KOTA TEGAL<br>
                INSTALASI FARMASI
            </div>
        </div>

        <!-- Judul -->
        <h1>
            Laporan Pemakaian Obat
            {{ $ruangan && $ruangan !== 'Semua Ruangan' ? $ruangan : 'Semua Ruangan' }}
        </h1>

        <p>
            Bulan:
            {{ $bulan ? \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') : 'Semua' }} |
            Tahun:
            {{ $tahun ?? 'Semua' }} |
            Ruangan:
            {{ $ruangan ?? 'Semua Ruangan' }} |
            Obat:
            @if ($obat_id && $pemakaianList->first() && $pemakaianList->first()->obat)
                {{ $pemakaianList->first()->obat->nama_obat }}
            @else
                Semua Obat
            @endif
        </p>

        <!-- Rekap Total -->
        @if (!empty($rekapTotal) && count($rekapTotal))
            <h2 style="font-weight: bold;">Rekap Total Pemakaian Obat</h2>
            <table class="rekap-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Obat</th>
                        <th>Total Pemakaian</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rekapTotal as $index => $rekap)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                {{ $rekap->obat->nama_obat ?? '-' }}
                                @if ($rekap->obat->dosis)
                                    - {{ $rekap->obat->dosis }}
                                @endif
                                @if ($rekap->obat->jenisObat)
                                    ({{ $rekap->obat->jenisObat->nama_jenis }})
                                @endif
                            </td>
                            <td>{{ number_format($rekap->total_disetujui, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- Detail Pemakaian -->
        <h2 style="font-weight: bold;">Detail Pemakaian Obat</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Ruangan</th>
                    <th>Pasien</th>
                    <th>Nama Obat</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pemakaianList->sortBy('tanggal') as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                        <td>{{ $item->user->ruangan }}</td>
                        <td>{{ $item->nama_pasien ?? '-' }}</td>
                        <td>
                            {{ $item->obat->nama_obat ?? '-' }}
                            @if ($item->obat->dosis)
                                - {{ $item->obat->dosis }}
                            @endif
                            @if ($item->obat->jenisObat)
                                ({{ $item->obat->jenisObat->nama_jenis }})
                            @endif
                        </td>
                        <td>{{ number_format($item->stok_keluar, 0, ',', '.') }}</td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data sesuai filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p>Tegal, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <div class="signature">
                <p><b>Operator {{ auth()->user()->ruangan }}</b></p>
                <br><br><br>
                <p>______________________</p>
                <p><b>{{ auth()->user()->nama_pegawai }}</b></p>
                <p><b>NIP {{ auth()->user()->nip }}</b></p>
            </div>
        </div>
    </div>
</body>

</html>
