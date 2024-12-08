<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Dashboard SIFARMA</title>
    <!-- Memuat CSS DataTables -->
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />

    <!-- Memuat Custom Styles -->
    <link href="{{ asset('backend/dist/css/styles.css') }}" rel="stylesheet" />

    <!-- Memuat Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>

    <!-- Memuat RowGroup (jika diperlukan) -->
    <script src="https://cdn.datatables.net/rowgroup/1.1.3/js/dataTables.rowGroup.min.js"></script>

    <!-- Memuat CKEditor (jika diperlukan) -->
    <script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>

    <!-- Memuat CSS RowGroup -->
    <link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.3.1/css/rowGroup.dataTables.min.css">

    <!-- Memuat Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Memuat Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Memuat jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Memuat DataTables -->
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>

    <!-- Memuat DataTables untuk Bootstrap 4 -->
    <script src="https://cdn.datatables.net/v/bs4/dt-2.1.8/datatables.min.js"></script>

    <!-- Memuat Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable();
        });
    </script>
    <style>
        .custom-card {
            background-color: #f8f9fa;
            /* Warna latar belakang soft */
            border-radius: 8px;
            /* Sudut membulat */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* Efek bayangan */
        }
    </style>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand -->
        <a class="navbar-brand ps-3" href="#">
            <img src="{{ asset('img/1.png') }}" alt="Logo SIFARMA" style="height: 150px; width: auto;">
        </a>

        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-lg-0 me-lg-0 order-1 me-4" id="sidebarToggle" href="#!"><i
                class="fas fa-bars"></i></button>

        <!-- Navbar -->
        <ul class="navbar-nav ms-auto">
            <ul class="navbar-nav ms-md-0 me-lg-4 me-3 ms-auto">
                <li class="nav-item dropdown d-flex align-items-center"> <!-- Menggunakan Flexbox untuk penyusunan -->
                    <!-- Lonceng Notifikasi -->
                    <a href="{{ route('dashboard.notifikasi') }}" class="nav-link d-flex align-items-center ms-3">
                        <i class="fas fa-bell" style="font-size: 20px;"></i> <!-- Ikon lonceng -->
                    </a>
                    <!-- Foto Profil -->
                    <a class="nav-link dropdown-toggle d-flex align-items-center" id="navbarDropdown" href="#"
                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="avatar-sm me-2">
                            @if (Auth::user()->foto)
                                <!-- Menampilkan gambar profil yang diupload -->
                                <img src="{{ asset('storage/user/' . Auth::user()->foto) }}" alt="User Avatar"
                                    class="avatar-img rounded-circle"
                                    style="width: 35px; height: 35px; object-fit: cover;" />
                            @else
                                <!-- Menampilkan gambar default jika foto belum diupload -->
                                <img src="{{ asset('img/profil.jpg') }}" alt="Default Avatar"
                                    class="avatar-img rounded-circle"
                                    style="width: 35px; height: 35px; object-fit: cover;" />
                            @endif
                        </div>
                        <!-- Nama dan Jabatan -->
                        <div class="profile-username d-flex flex-column" style="color: rgba(255, 255, 255, 0.8);">
                            <!-- Menggunakan rgba untuk efek redup -->
                            <span class="fw-bold" style="font-size: 14px">{{ Auth::user()->nama_pegawai }}</span>
                            <small style="font-size: 12px">{{ Auth::user()->jabatan }}</small>
                        </div>
                    </a>
                    <!-- Dropdown -->
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        @if (Auth::user()->level == 'operator')
                            <!-- Pastikan ini sesuai dengan nama role pada aplikasi Anda -->
                            <li><a class="dropdown-item"
                                    href="{{ route('profile.index', Auth::user()->id) }}">Profil</a></li>
                        @endif
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                data-bs-target="#logoutModal">
                                Keluar
                            </a>
                        </li>
                    </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading"></div>

                        <!-- Tautan Dashboard untuk Admin -->
                        @if (Auth::user()->level == 'admin')
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                                href="{{ route('dashboard') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>
                        @endif

                        <!-- Tautan Dashboard untuk Operator -->
                        @if (Auth::user()->level == 'operator')
                            <a class="nav-link {{ request()->routeIs('dashboard.operator') ? 'active' : '' }}"
                                href="{{ route('dashboard.operator') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>
                        @endif

                        <div class="sb-sidenav-menu-heading">Menu</div>

                        <!-- Hanya tampil untuk Admin -->
                        @if (Auth::user()->level == 'admin')
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                                data-bs-target="#collapseMasterData" aria-expanded="false"
                                aria-controls="collapseMasterData">
                                <div class="sb-nav-link-icon"><i class="fas fa-database"></i></div>
                                Data Master
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseMasterData" aria-labelledby="headingMasterData"
                                data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link {{ request()->is('user') ? 'active' : '' }}" href="/user">
                                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                        User
                                    </a>
                                    <a class="nav-link {{ request()->is('obat') ? 'active' : '' }}" href="/obat">
                                        <div class="sb-nav-link-icon"><i class="fas fa-pills"></i></div>
                                        Data Obat
                                    </a>
                                    <a class="nav-link {{ request()->is('jenis_obat') ? 'active' : '' }}"
                                        href="{{ route('jenis_obat.index') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-pills"></i></div>
                                        Jenis Obat
                                    </a>

                                </nav>
                            </div>
                            <a class="nav-link {{ request()->is('pengajuan') ? 'active' : '' }}"
                                href="{{ route('pengajuan.index') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
                                Permintaan
                            </a>
                            <a class="nav-link {{ request()->is('pembelian') ? 'active' : '' }}" href=" ">
                                <div class="sb-nav-link-icon"><i class="fa fa-cart-plus"></i></div>
                                Stok Obat
                            </a>
                            <a class="nav-link {{ request()->is('retur') ? 'active' : '' }}" href=" ">
                                <div class="sb-nav-link-icon"><i class="fa fa-undo"></i></div>
                                Retur
                            </a>
                            <a class="nav-link {{ request()->is('laporan') ? 'active' : '' }}" href="/laporan">
                                <div class="sb-nav-link-icon"><i class="fas fa-print"></i></div>
                                Laporan
                            </a>
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                <div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt"></i></div>
                                Keluar
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                style="display: none;">
                                @csrf
                            </form>
                    </div>
                </div>
                @endif

                <!-- Menu Transaksi untuk Operator -->
                @if (Auth::user()->level == 'operator')
                    <a class="nav-link {{ request()->is('operator') ? 'active' : '' }}"
                        href="{{ route('operator.dataobat') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-pills"></i></div>
                        Data Obat
                    </a>
                    <a class="nav-link {{ request()->is('transaksi') ? 'active' : '' }}" href="/transaksi">
                        <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
                        Order Obat
                    </a>
                    <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                        <div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt"></i></div>
                        Keluar
                    </a>
                @endif

                <div class="sb-sidenav-footer" style="color: white;">
                    <div class="small">Masuk Sebagai</div>
                    <span class="fw-bold" style="font-size: 20px">{{ Auth::user()->level }}</span>
                </div>

            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <br>
                    @yield('content')
                </div>
            </main>
            <footer class="bg-light mt-auto py-4">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Umpeg Dinkes 2024</div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Keluar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin keluar?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <button type="button" class="btn btn-primary"
                        onclick="document.getElementById('logout-form').submit();">Keluar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let alert = document.querySelector('.alert');
            if (alert) {
                setTimeout(() => {
                    alert.classList.add('fade');
                    setTimeout(() => alert.remove(), 500); // Delay for complete fade out
                }, 3000); // 3000ms = 3 seconds
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>
    <script src="{{ asset('backend/dist/js/scripts.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('backend/dist/assets/demo/chart-area-demo.js') }}"></script>
    <script src="{{ asset('backend/dist/assets/demo/chart-bar-demo.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
    <script src="{{ asset('backend/dist/js/datatables-simple-demo.js') }}"></script>

</body>

</html>
