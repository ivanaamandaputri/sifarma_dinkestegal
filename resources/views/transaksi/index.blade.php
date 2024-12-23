@extends('layouts.app')

@section('content')
    <div class="container py-3">
        <!-- Judul dan tombol tambah -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('transaksi.create') }}" class="btn btn-primary">Tambah Transaksi</a>
        </div>

        <!-- Notifikasi Sukses -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Notifikasi Error -->
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif


        <!-- Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title mb-0">Data Order Obat {{ Auth::user()->ruangan }}</h4>
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
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $number = 1; @endphp
                            @foreach ($transaksi as $item)
                                <tr>
                                    <td>{{ $number++ }}</td>
                                    <td>{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d M Y') : '-' }}
                                    </td>
                                    <td>{{ $item->obat->nama_obat }} - {{ $item->obat->dosis }}
                                        ({{ $item->obat->jenisObat->nama_jenis }})
                                    </td>
                                    <td>{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                    <td>{{ $item->acc !== null ? number_format($item->acc, 0, ',', '.') : '-' }}</td>
                                    <td>{{ isset($item->retur->jumlah) ? number_format($item->retur->jumlah, 0, ',', '.') : '-' }}
                                    </td>
                                    <td>
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
                                        @if ($item->status === 'Ditolak')
                                            <button type="button" class="btn btn-sm btn-light view-reason-btn"
                                                data-reason="{{ $item->alasan_penolakan }}">Alasan</button>
                                        @elseif ($item->status === 'Menunggu')
                                            <a href="{{ route('transaksi.edit', $item->id) }}"
                                                class="btn btn-warning btn-sm">Edit</a>
                                            <form class="d-inline delete-form" data-id="{{ $item->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="btn btn-danger btn-sm delete-btn">Hapus</button>
                                            </form>
                                        @elseif ($item->status === 'Disetujui')
                                            <button type="button" class="btn btn-warning btn-sm retur-btn"
                                                data-id="{{ $item->id }}" data-obat="{{ $item->obat->id }}"
                                                data-max="{{ $item->jumlah }}" data-acc="{{ $item->acc ?? 0 }}"
                                                data-nama="{{ $item->obat->nama_obat }}" data-bs-toggle="modal"
                                                data-bs-target="#returModal{{ $item->id }}">Retur</button>
                                        @elseif ($item->status === 'Diretur')
                                            <button type="button" class="btn btn-light btn-sm alasan-retur-btn"
                                                data-id="{{ $item->id }}">Alasan Retur</button>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Modal Retur -->
                                <div class="modal fade" id="returModal{{ $item->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Retur Obat - {{ $item->obat->nama_obat }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form id="returForm{{ $item->id }}" method="POST"
                                                    action="{{ route('transaksi.retur', $item->id) }}">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label for="jumlah" class="form-label">Jumlah Retur</label>
                                                        <input type="number" name="jumlah" id="jumlah{{ $item->id }}"
                                                            class="form-control" min="1" required>
                                                        <span id="errorJumlah{{ $item->id }}"
                                                            class="text-danger"></span>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="alasan" class="form-label">Alasan Retur</label>
                                                        <textarea name="alasan" id="alasan{{ $item->id }}" class="form-control" rows="3" required></textarea>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Kirim Retur</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Konfirmasi Hapus -->
        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        Yakin ingin menghapus transaksi ini?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
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


        <!-- Script Modal Retur -->
        @if ($errors->any())
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    var returModal = new bootstrap.Modal(document.getElementById('returModal'));
                    returModal.show();
                });
            </script>
        @endif



    </div>

    <!-- Include JS for DataTable -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#transaksiTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
            });

            let deleteId = null;

            // Klik tombol hapus
            $(document).on('click', '.delete-btn', function() {
                deleteId = $(this).closest('.delete-form').data('id');
                $('#confirmDeleteModal').modal('show');
            });

            // Konfirmasi hapus
            $('#confirmDeleteBtn').on('click', function() {
                if (deleteId) {
                    const form = $(`form.delete-form[data-id="${deleteId}"]`);
                    const url = form.attr('action');

                    $.ajax({
                        url: `/transaksi/${deleteId}`,
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $('#confirmDeleteModal').modal('hide');
                            alert('Transaksi berhasil dihapus.');
                            location.reload();
                        },
                        error: function(xhr) {
                            $('#confirmDeleteModal').modal('hide');
                            alert('Terjadi kesalahan saat menghapus transaksi.');
                        }
                    });
                }
            });

            // Lihat alasan penolakan
            $(document).on('click', '.view-reason-btn', function() {
                const reason = $(this).data('reason');
                $('#reasonText').text(reason);
                $('#reasonModal').modal('show');
            });

            // Tampilkan modal alasan retur
            $(document).on('click', '.alasan-retur-btn', function() {
                const id = $(this).data('id'); // Ambil ID transaksi dari tombol

                // Lakukan request AJAX untuk mendapatkan data retur
                $.ajax({
                    url: `/transaksi/${id}/retur`, // Pastikan URL sesuai dengan rute yang benar
                    type: 'GET',
                    success: function(data) {
                        if (data.error) {
                            alert(data.error); // Jika data tidak ditemukan
                        } else {
                            // Menampilkan data jumlah retur dan alasan retur ke dalam modal
                            $('#jumlahRetur').text(data.jumlah); // Set jumlah retur
                            $('#alasanRetur').text(data.alasan_retur); // Set alasan retur

                            const modal = new bootstrap.Modal(document.getElementById(
                                'alasanReturModal'));
                            modal.show(); // Tampilkan modal
                        }
                    },
                    error: function() {
                        alert('Gagal mengambil data retur.');
                    }
                });
            });


            document.addEventListener('DOMContentLoaded', function() {
                const returButtons = document.querySelectorAll('.retur-btn');
                const jumlahInput = document.getElementById('jumlah');

                returButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        // Ambil nilai 'acc' dari tombol yang diklik
                        const accValue = this.getAttribute('data-acc');

                        // Set nilai pada input 'jumlah'
                        if (jumlahInput) {
                            jumlahInput.value = accValue;
                        }

                        // Jika Anda menggunakan modal, pastikan modal terbuka
                        const modal = document.getElementById('modalRetur');
                        if (modal) {
                            modal.classList.add('show');
                            modal.style.display = 'block';
                        }
                    });
                });
            });


            // Menangani tombol retur untuk memvalidasi dan mengirim data
            $(document).on('click', '.retur-btn', function() {
                const transaksiId = $(this).data('id');
                const maxJumlah = $(this).data('max');
                $('#returModal').modal('show');
                $('#errorJumlah').text('');
                $('#jumlah').val('');
                $('#jumlah').attr('max',
                    maxJumlah); // Set batas jumlah retur sesuai jumlah transaksi yang sudah disetujui
                $('#returForm').off('submit').on('submit', function(e) {
                    e.preventDefault();
                    const jumlah = parseInt($('#jumlah').val());
                    const alasan = $('#alasan').val();

                    // Validasi input
                    if (jumlah <= 0 || jumlah > maxJumlah || !alasan.trim()) {
                        $('#errorJumlah').text(
                            'Jumlah retur harus lebih besar dari 0 dan tidak boleh melebihi jumlah yang disetujui. Alasan retur wajib diisi.'
                        );
                        return;
                    }

                    // Kirim data retur ke server
                    $.ajax({
                        url: `/transaksi/${transaksiId}/retur`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            jumlah: jumlah,
                            alasan: alasan,
                        },
                        success: function(response) {
                            if (response.success) {
                                alert(response.success);
                                location
                                    .reload(); // Reload halaman setelah retur berhasil
                            } else {
                                alert(response.error ||
                                    'Terjadi kesalahan saat memproses retur.');
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            alert('Terjadi kesalahan. Silakan coba lagi.');
                        }
                    });

                    $('#returModal').modal('hide');
                });
            });

        });

        document.addEventListener("DOMContentLoaded", function() {
            const modalId = "returModal";
            const modalElement = document.getElementById(modalId);
            if (modalElement) {
                new bootstrap.Modal(modalElement).show();
            }
        });

    </script>
@endsection
