@extends('layouts.app')

@section('content')
    <div class="container py-3">
        <div class="mb-3">
            <!-- Alert Section -->
            <div id="alert-container">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title mb-0">Data Permintaan Obat</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatablesSimple" class="table-hover table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Nama Obat</th>
                                    <th>Jumlah Order</th>
                                    <th>Acc</th>
                                    <th>Jumlah Retur</th>
                                    <th>Harga (Rp)</th>
                                    <th>Total (Rp)</th>
                                    <th>Puskesmas</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transaksi as $item)
                                    <tr id="transaksi-{{ $item->id }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                                        <td>
                                            {{ $item->obat->nama_obat }} - {{ $item->obat->dosis }}
                                            ({{ $item->obat->jenisObat->nama_jenis }})
                                        </td>
                                        <td>{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                        <td>{{ number_format($item->acc, 0, ',', '.') }}</td>
                                        <td>{{ isset($item->retur->jumlah) ? number_format($item->retur->jumlah, 0, ',', '.') : '-' }}
                                        <td>{{ number_format($item->obat->harga, 0, ',', '.') }}</td>
                                        <td>{{ number_format($item->total, 0, ',', '.') }}</td>
                                        <td>{{ $item->user->ruangan }}</td>
                                        <td class="status">
                                            @if ($item->status === 'Disetujui')
                                                <span class="badge bg-success">Disetujui</span>
                                            @elseif ($item->status === 'Ditolak')
                                                <span class="badge bg-danger">Ditolak</span>
                                            @elseif ($item->status === 'Diretur')
                                                <span class="badge bg-dark">Diretur</span>
                                            @else
                                                <span class="badge bg-warning">Menunggu</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->status === 'Diretur')
                                                <button type="button" class="btn btn-light btn-sm view-retur-btn"
                                                    data-id="{{ $item->id }}"
                                                    data-jumlah="{{ $item->retur->jumlah ?? 'Tidak Ada' }}"
                                                    data-alasan="{{ $item->retur->alasan_retur ?? 'Tidak Ada' }}">
                                                    Alasan Retur
                                                </button>
                                            @endif
                                            @if ($item->status === 'Ditolak')
                                                <button type="button" class="btn btn-sm btn-light view-reason-btn"
                                                    data-reason="{{ $item->alasan_penolakan }}">Alasan</button>
                                            @endif

                                            @if ($item->status === 'Menunggu')
                                                <button type="button" class="btn btn-sm btn-primary approve-btn"
                                                    data-id="{{ $item->id }}"
                                                    data-max-jumlah="{{ $item->jumlah }}">Setujui</button>
                                                <button type="button" class="btn btn-sm btn-danger reject-btn"
                                                    data-id="{{ $item->id }}">Tolak</button>
                                            @endif
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Alasan Penolakan -->
        <div class="modal fade" id="reasonModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Alasan Penolakan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p id="reasonText"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Lihat Alasan Retur -->
        <div class="modal fade" id="alasanReturModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Retur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Jumlah Retur:</strong> <span id="jumlahRetur"></span></p>
                        <p><strong>Alasan Retur:</strong> <span id="alasanRetur"></span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Konfirmasi Setuju -->
        <div class="modal fade" id="confirmApproveModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="mt-3">
                            <label for="jumlahAcc" class="form-label">Jumlah ACC</label>
                            <input type="number" class="form-control" id="jumlahAcc" name="jumlahAcc" min="1"
                                required>
                            <div id="errorAcc" class="text-danger" style="display: none;"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="confirmApproveButton">Setujui</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Konfirmasi Tolak -->
        <div class="modal fade" id="confirmRejectModal" tabindex="-1" aria-labelledby="confirmRejectModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Konfirmasi Penolakan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="rejectReason">Silakan isi alasan penolakan:</label>
                            <textarea class="form-control" id="rejectReason" rows="3" placeholder="Masukkan alasan"></textarea>
                            <div id="errorReason" class="text-danger mt-2" style="display: none;"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-danger" id="confirmRejectButton">Tolak</button>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // Tombol untuk melihat alasan penolakan
            $(document).on('click', '.view-reason-btn', function() {
                const reason = $(this).data('reason');
                $('#reasonText').text(reason);
                $('#reasonModal').modal('show');
            });

            // Tombol untuk Setujui
            $(document).on('click', '.approve-btn', function() {
                const id = $(this).data('id');
                const maxJumlah = $(this).data('max-jumlah');
                $('#confirmApproveModal').data('id', id);
                $('#jumlahAcc').val(maxJumlah);
                $('#errorAcc').hide();
                $('#confirmApproveModal').modal('show');
            });

            // Konfirmasi Setujui
            $(document).on('click', '#confirmApproveButton', function() {
                const jumlahAcc = parseInt($('#jumlahAcc').val());
                const transaksiId = $('#confirmApproveModal').data('id');

                // Validasi jumlah ACC
                if (!jumlahAcc || jumlahAcc <= 0) {
                    $('#errorAcc').text('Jumlah ACC tidak boleh kosong atau kurang dari 1').show();
                    return;
                }

                // Kirim data via AJAX
                $.ajax({
                    url: `/transaksi/approve/${transaksiId}`,
                    type: 'POST',
                    data: {
                        acc: jumlahAcc,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#confirmApproveModal').modal('hide');
                        $('#alert-container').html(`
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            ${response.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `);

                        // Tunggu 1 detik sebelum memperbarui tampilan UI
                        setTimeout(() => {
                            const transaksiRow = $(`#transaksi-${transaksiId}`);
                            transaksiRow.find('.status').html(
                                '<span class="badge bg-success">Disetujui</span>');
                            transaksiRow.find('.approve-btn, .reject-btn').remove();
                        }, 1000);
                    },
                    error: function() {
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                    }
                });
            });

            // Tombol untuk Tolak
            $(document).on('click', '.reject-btn', function() {
                const transaksiId = $(this).data('id');
                $('#confirmRejectModal').data('id', transaksiId);
                $('#rejectReason').val('');
                $('#errorReason').hide();
                $('#confirmRejectModal').modal('show');
            });

            // Konfirmasi Tolak
            $(document).on('click', '#confirmRejectButton', function() {
                const transaksiId = $('#confirmRejectModal').data('id');
                const reason = $('#rejectReason').val().trim();

                if (reason === '') {
                    $('#errorReason').show().text('Alasan penolakan tidak boleh kosong.');
                    return;
                }

                $('#errorReason').hide();

                $.ajax({
                    url: `/transaksi/reject/${transaksiId}`,
                    type: 'POST',
                    data: {
                        reason: reason,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#confirmRejectModal').modal('hide');
                        $('#alert-container').html(`
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            ${response.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `);

                        // Reload atau perbarui halaman sesuai kebutuhan
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    },
                    error: function(xhr) {
                        const errorMessage = xhr.responseJSON?.error || 'Terjadi kesalahan.';
                        $('#alert-container').html(`
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            ${errorMessage}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `);
                    }
                });
            });

            // Fungsi untuk Retur transaksi
            $('.retur-btn').on('click', function() {
                const idTransaksi = $(this).data('id');
                const alasanRetur = $(this).data('alasan');
                $.ajax({
                    url: '/transaksi/retur',
                    type: 'POST',
                    data: {
                        id_transaksi: idTransaksi, // ID transaksi yang ingin diretur
                        alasan: alasanRetur, // Alasan retur
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert(response.message);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", error);
                        console.log(xhr.responseText);
                    }
                });
            });

            // Tombol untuk melihat alasan retur
            $('.view-retur-btn').on('click', function() {
                const id = $(this).data('id');
                const jumlah = $(this).data('jumlah');
                const alasan = $(this).data('alasan');
                console.log(`ID: ${id}, Jumlah: ${jumlah}, Alasan: ${alasan}`);
                $('#jumlahRetur').text(jumlah);
                $('#alasanRetur').text(alasan);
                $('#alasanReturModal').modal('show');
            });


        });
    </script>
