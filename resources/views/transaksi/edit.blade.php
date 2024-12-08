@extends('layouts.app')

@section('content')
    <div class="container py-3">
        <div class="row">
            <!-- Card untuk Menampilkan Detail Obat -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 id="obat-nama">Nama Obat</h4> <!-- Nama obat akan ditampilkan di sini -->
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <img id="obat-foto" src="" alt="Foto Obat" class="img-fluid" style="max-height: 200px;">
                        </div>
                        <div class="mt-2 border p-2">
                            <p><strong>Jenis:</strong> <span id="detail-jenis">-</span></p>
                            <p><strong>Dosis:</strong> <span id="detail-dosis">-</span></p>
                            <p><strong>Harga:</strong> <span id="detail-harga">-</span></p>
                            <p><strong>Stok:</strong> <span id="detail-stok">-</span></p>
                            <p><strong>Kedaluwarsa:</strong> <span id="detail-exp">-</span></p>
                            <p><strong>Keterangan:</strong> <span id="detail-keterangan">-</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card untuk Edit Transaksi -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Order</h4>
                    </div>

                    <div class="card-body">
                        @if ($errors->has('jumlah'))
                            <div class="alert alert-danger">
                                {{ $errors->first('jumlah') }}
                            </div>
                        @endif

                        <form action="{{ route('transaksi.update', $transaksi->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="tanggal">Tanggal Order:</label>
                                        <input type="date" name="tanggal" id="tanggal" class="form-control"
                                            value="{{ $transaksi->tanggal }}" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="obat_id">Nama Obat</label>
                                        <select name="obat_id" class="form-control" required id="obat_id">
                                            @foreach ($obat as $item)
                                                <option value="{{ $item->id }}" data-harga="{{ $item->harga }}"
                                                    data-dosis="{{ $item->dosis }}"
                                                    data-jenis="{{ $item->jenisObat->nama_jenis }}"
                                                    data-stok="{{ $item->stok }}" data-exp="{{ $item->exp }}"
                                                    data-keterangan="{{ $item->keterangan }}"
                                                    data-foto="{{ asset('storage/obat/' . $item->foto) }}"
                                                    {{ $item->id == $transaksi->obat_id ? 'selected' : '' }}>
                                                    {{ $item->nama_obat }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="jumlah">Jumlah</label>
                                        <input type="number" name="jumlah" class="form-control" required min="1"
                                            id="jumlah" value="{{ $transaksi->jumlah }}">
                                        @if ($errors->has('jumlah'))
                                            <div class="text-danger">
                                                {{ $errors->first('jumlah') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="harga">Harga (Rp)</label>
                                        <input type="text" name="harga" class="form-control" id="harga" readonly
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="total">Total (Rp)</label>
                                        <input type="text" name="total" class="form-control" readonly id="total"
                                            value="{{ number_format($transaksi->total, 0, ',', '.') }}">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Transaksi</button>
                            <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const obatSelect = document.getElementById('obat_id');
            const jumlahInput = document.getElementById('jumlah');
            const totalInput = document.getElementById('total');
            const detailDosis = document.getElementById('detail-dosis');
            const detailJenis = document.getElementById('detail-jenis');
            const detailStok = document.getElementById('detail-stok');
            const detailHarga = document.getElementById('detail-harga');
            const detailExp = document.getElementById('detail-exp');
            const detailKeterangan = document.getElementById('detail-keterangan');
            const obatNama = document.getElementById('obat-nama');
            const obatFoto = document.getElementById('obat-foto');
            const hargaInput = document.getElementById('harga');

            // Fungsi untuk memperbarui informasi obat
            const updateFields = () => {
                const selectedOption = obatSelect.options[obatSelect.selectedIndex];
                const harga = parseFloat(selectedOption.dataset.harga);
                const dosis = selectedOption.dataset.dosis;
                const jenis = selectedOption.dataset.jenis;
                const stok = selectedOption.dataset.stok;
                const exp = selectedOption.dataset.exp;
                const keterangan = selectedOption.dataset.keterangan;
                const foto = selectedOption.dataset.foto;
                const jumlah = parseInt(jumlahInput.value) || 0;

                // Update input fields
                obatNama.textContent = selectedOption.text;
                detailDosis.textContent = dosis;
                detailJenis.textContent = jenis;
                detailStok.textContent = stok;
                detailHarga.textContent = new Intl.NumberFormat('id-ID').format(harga);
                detailExp.textContent = exp;
                detailKeterangan.innerHTML = keterangan;
                obatFoto.src = foto;
                hargaInput.value = harga;

                // Update total
                totalInput.value = new Intl.NumberFormat('id-ID').format(harga * jumlah);
            };

            // Event listener untuk perubahan pada jumlah
            jumlahInput.addEventListener('input', () => {
                const selectedOption = obatSelect.options[obatSelect.selectedIndex];
                const harga = parseFloat(selectedOption.dataset.harga);
                const jumlah = parseInt(jumlahInput.value) || 0;

                // Update total
                totalInput.value = new Intl.NumberFormat('id-ID').format(harga * jumlah);
            });

            // Event listener untuk perubahan pada obat
            obatSelect.addEventListener('change', updateFields);

            // Set initial value on page load
            updateFields();
        });
    </script>
@endsection
