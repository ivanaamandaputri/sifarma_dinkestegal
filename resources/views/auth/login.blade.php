<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Login - SI Farmasi</title>
    <link href="{{ asset('backend/dist/css/styles.css') }}" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
</head>

<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card mt-5 rounded-lg border-0 shadow-lg">
                                <div class="card-header">
                                    <h2 class="font-weight-light my-4 text-center">Login</h2>
                                </div>
                                <div class="card-body">
                                    <!-- Error Message -->
                                    @if (session()->has('msgError'))
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <strong>{{ session('msgError') }}</strong>
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif


                                    <form action="{{ url('/login') }}" method="post" onsubmit="return validateForm()">
                                        @csrf
                                        <div class="form-floating mb-3">
                                            <input type="text" name="nip" id="inputNIP"
                                                value="{{ old('nip') }}"
                                                class="form-control @error('nip') is-invalid @enderror"
                                                placeholder="Masukkan NIP" />
                                            <label for="inputNIP">NIP</label>
                                            @error('nip')
                                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-floating mb-3">
                                            <input type="password" name="password" id="inputPassword"
                                                value="{{ old('password') }}"
                                                class="form-control @error('password') is-invalid @enderror"
                                                placeholder="Password" />
                                            <label for="inputPassword">Password</label>
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-check mb-3">
                                            <input class="form-check-input" id="inputRememberPassword"
                                                type="checkbox" />
                                            <label class="form-check-label" for="inputRememberPassword">Remember
                                                Password</label>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-between mb-0 mt-4">
                                            <a class="small" href="/forgot-password">Forgot Password?</a>
                                            <button type="submit" class="btn btn-primary">Login</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer py-3 text-center">
                                    <img src="{{ asset('img/2.png') }}" alt="logo"
                                        style="width: 100px; height: auto; transform: scale(2);">
                                    <h6>Selamat Datang di SIFARMA !</h6>
                                    <p>Mari bersama membangun kesehatan masyarakat yang lebih baik dengan sistem yang
                                        terintegrasi</p>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
            <footer class="bg-light mt-auto py-4">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">2024, made with by ♥️ Umpeg Dinkes Tegal</div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script>
        function validateForm() {
            var nip = document.getElementById('inputNIP').value;
            var password = document.getElementById('inputPassword').value;

            if (nip === "" || password === "") {
                alert('NIP atau Password tidak boleh kosong.');
                return false;
            }
            return true;
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>
    <script src="{{ asset('js/scripts.js') }}"></script>
</body>

</html>
