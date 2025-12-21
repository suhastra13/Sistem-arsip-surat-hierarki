<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Arsip Dinas Kehutanan</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            /* Background Gradasi Biru Dinas */
            background: linear-gradient(135deg, #004085 0%, #002752 100%);
            height: 100vh;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
            /* Shadow lebih tebal */
            overflow: hidden;
            width: 100%;
            max-width: 500px;
            /* DIPERLEBAR JADI 500PX */
        }

        .card-header {
            background-color: white;
            border-bottom: none;
            text-align: center;
            padding-top: 50px;
            /* Jarak atas lebih lega */
            padding-bottom: 20px;
        }

        .logo-icon {
            font-size: 3.5rem;
            /* Icon lebih besar */
            color: #004085;
            background: #e9ecef;
            width: 100px;
            /* Lingkaran lebih besar */
            height: 100px;
            line-height: 100px;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: block;
        }

        .btn-login {
            background: #004085;
            border: none;
            font-weight: 700;
            letter-spacing: 1px;
            transition: all 0.3s;
            border-radius: 8px;
        }

        .btn-login:hover {
            background: #002752;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 64, 133, 0.3);
        }

        .form-control {
            padding: 14px;
            /* Input lebih tinggi */
            border-radius: 8px;
            background-color: #f8f9fa;
            font-size: 1rem;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #004085;
            background-color: white;
        }

        .input-group-text {
            background: white;
            border-right: none;
            color: #6c757d;
            padding-left: 15px;
        }

        .input-group .form-control {
            border-left: none;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">

            <div class="col-md-8 col-lg-6 d-flex justify-content-center">

                <div class="card login-card">
                    <div class="card-header">
                        <div class="logo-icon text-center">
                            <i class="fas fa-tree"></i>
                        </div>
                        <h3 class="fw-bold text-dark mb-2">E-ARSIP DISHUT</h3>
                        <p class="text-muted">Silakan login untuk masuk ke sistem</p>
                    </div>

                    <div class="card-body p-5 pt-2">

                        @if ($errors->any())
                        <div class="alert alert-danger py-2 small shadow-sm border-0 mb-4 text-center">
                            <i class="fas fa-exclamation-circle me-1"></i>
                            {{ $errors->first() }}
                        </div>
                        @endif

                        <form action="{{ route('login') }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted ps-1">EMAIL ADDRESS</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control bg-light border-start-0 ps-0" placeholder="nama@dinas.go.id" required autofocus>
                                </div>
                            </div>

                            <div class="mb-5">
                                <label class="form-label small fw-bold text-muted ps-1">PASSWORD</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control bg-light border-start-0 ps-0" placeholder="••••••••" required>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-login text-white py-3">
                                    MASUK SISTEM <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>

                        </form>
                    </div>

                    <div class="card-footer bg-light text-center py-4 border-0">
                        <small class="text-muted d-block">&copy; 2024 Dinas Kehutanan.</small>
                        <small class="text-muted" style="font-size: 0.75rem;">Sistem Arsip Digital Terpadu</small>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>

</html>