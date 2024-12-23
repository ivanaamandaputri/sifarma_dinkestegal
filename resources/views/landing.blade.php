<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Farmasi</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Hero Section Styling */
        .hero-section {
            height: 100vh;
            /* Penuh 1 layar */
            width: 100%;
            background-image: url('{{ asset('img/bg3.png') }}');
            background-size: cover;
            /* Gambar menyesuaikan layar */
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            margin: 0;
            padding: 0;
        }

        /* Button Styling */
        .btn-login {
            font-size: 1.2rem;
            font-weight: bold;
            /* Tulisan tebal */
            padding: 12px 30px;
            background-color: #007bff;
            /* Warna tombol */
            color: white;
            border: none;
            border-radius: 50px;
            /* Tombol oval */
            text-decoration: none;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            /* Efek bayangan halus */
            transition: all 0.3s ease;
            /* Efek animasi */
            position: absolute;
            top: 60%;
            /* Turun sedikit ke bawah */
            left: 85px;
            /* Geser ke kanan */
            transform: translateY(-50%);
            /* Koreksi posisi tengah */
        }

        .btn-login:hover {
            background-color: #3399ff;
            /* Hover lebih terang */
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.3);
            transform: translateY(-50%) translateX(-3px);
            /* Efek hover ke kanan sedikit */
        }
    </style>
</head>

<body>

    <!-- Hero Section -->
    <section class="hero-section">
        <a href="{{ route('login') }}" class="btn-login">Login</a>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

</html>
