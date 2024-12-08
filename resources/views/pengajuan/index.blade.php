@extends('layouts.app')

@section('content')
    <div class="container py-3">
        <div class="container-fluid d-flex justify-content-between">
            <h4 class="card-title">Data Permintaan Obat</h4>
        </div>

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


        <br>
        <div class="card mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatablesSimple" class="table-hover table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Nama Obat</th>
                                <th>Dosis</th>
                                <th>Jenis</th>
                                <th>Jumlah</th>
                                <th>Acc</th>
                                <th>Harga (Rp)</th>
                                <th>Total (Rp)</th>
                                <th>Puskesmas</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transaksi as $transaksi)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d M Y') }}</td>
                                    <td>{{ $transaksi->obat->nama_obat }}</td>
                                    <td>{{ $transaksi->obat->dosis }}</td>
                                    <td>{{ $transaksi->obat->jenisObat->nama_jenis }}</td>
                                    <td>{{ $transaksi->jumlah }}</td>
                                    <td>{{ $transaksi->acc }}</td>
                                    <td>{{ number_format($transaksi->obat->harga, 0, ',', '.') }}</td>
                                    <td>{{ number_format($transaksi->total, 0, ',', '.') }}</td>
                                    <td>{{ $transaksi->user->ruangan }}</td>
                                    <td>
                                        @if ($transaksi->status === 'Disetujui')
                                            <span class="badge bg-success">{{ $transaksi->status }}</span>
                                        @elseif ($transaksi->status === 'Ditolak')
                                            <span class="badge bg-danger">{{ $transaksi->status }}</span>
                                        @elseif ($transaksi->status === 'Selesai')
                                            <span class="badge bg-dark">{{ $transaksi->status }}</span>
                                        @else
                                            <span class="badge bg-warning">{{ $transaksi->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($transaksi->status === 'Ditolak')
                                            <button type="button" class="btn btn-sm btn-light view-reason-btn"
                                                data-reason="{{ $transaksi->alasan_penolakan }}">Alasan</button>
                                        @elseif ($transaksi->status === 'Menunggu')
                                            <button type="button" class="btn btn-sm btn-primary approve-btn"
                                                data-id="{{ $transaksi->id }}"
                                                data-max-jumlah="{{ $transaksi->jumlah }}">Setujui</button>
                                            <button type="button" class="btn btn-sm btn-danger reject-btn"
                                                data-id="{{ $transaksi->id }}">Tolak</button>
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

    <!-- Modal Konfirmasi Setuju -->
    <div class="modal fade" id="confirmApproveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Konfirmasi Persetujuan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mt-3">
                        <label for="jumlahAcc" class="form-label">Jumlah ACC</label>
                        <input type="number" class="form-control" id="jumlahAcc" min="1" required>
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
        $('.view-reason-btn').on('click', function() {
            const reason = $(this).data('reason');
            $('#reasonText').text(reason);
            $('#reasonModal').modal('show');
        });

        // Tombol untuk Setujui
        $('.approve-btn').on('click', function() {
            const id = $(this).data('id'); // Mendapatkan ID transaksi yang akan disetujui
            const maxJumlah = $(this).data('max-jumlah'); // Mendapatkan jumlah yang diminta
            $('#confirmApproveModal').data('id', id); // Menyimpan ID transaksi di modal
            $('#confirmApproveModal').data('max-jumlah',
                maxJumlah); // Menyimpan jumlah yang diminta di modal
            $('#jumlahAcc').val(''); // Reset input jumlah ACC
            $('#errorAcc').hide(); // Menyembunyikan pesan error
            $('#confirmApproveModal').modal('show');
        });


        // Konfirmasi Setujui
        $('#confirmApproveButton').on('click', function() {
            const jumlahAcc = parseInt($('#jumlahAcc').val());
            const maxJumlah = parseInt($('#confirmApproveModal').data('max-jumlah'));
            const transaksiId = $('#confirmApproveModal').data('id');

            if (!jumlahAcc || jumlahAcc <= 0) {
                $('#errorAcc').text('Jumlah ACC tidak boleh kosong atau kurang dari 1').show();
                return;
            }
            if (jumlahAcc > maxJumlah) {
                $('#errorAcc').text('Jumlah ACC tidak boleh melebihi jumlah permintaan.').show();
                return;
            }

            $('#errorAcc').hide();
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

                    // Tambahkan alert sukses ke dalam alert-container
                    $('#alert-container').html(`
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ${response.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `);

                    // Reload atau perbarui halaman sesuai kebutuhan
                    setTimeout(() => {
                        location.reload();
                    }, 3000); // Reload setelah 3 detik
                },
                error: function(xhr) {
                    const errorMessage = xhr.responseJSON?.error || 'Terjadi kesalahan.';

                    // Tambahkan alert error ke dalam alert-container
                    $('#alert-container').html(`
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ${errorMessage}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `);
                }
            });
        });

        // Tombol untuk Tolak
        $('.reject-btn').on('click', function() {
            const transaksiId = $(this).data('id');
            $('#confirmRejectModal').data('id', transaksiId);
            $('#rejectReason').val('');
            $('#errorReason').hide();
            $('#confirmRejectModal').modal('show');
        });

        // Konfirmasi Tolak

        $('#confirmRejectButton').on('click', function() {
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

                    // Tambahkan alert sukses ke dalam alert-container
                    $('#alert-container').html(`
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ${response.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `);

                    // Reload atau perbarui halaman sesuai kebutuhan
                    setTimeout(() => {
                        location.reload();
                    }, 3000); // Reload setelah 3 detik
                },
                error: function(xhr) {
                    const errorMessage = xhr.responseJSON?.error || 'Terjadi kesalahan.';

                    // Tambahkan alert error ke dalam alert-container
                    $('#alert-container').html(`
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ${errorMessage}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `);
                }
            });
        });

    });
</script>
