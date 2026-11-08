<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - StockFlow Warehouse Management</title>

    <!-- Google Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .login-card {
            background: #ffffff;
            border-radius: 18px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
            max-width: 440px;
            width: 100%;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .brand-box {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            color: #fff;
            box-shadow: 0 8px 16px rgba(37, 99, 235, 0.3);
            margin: 0 auto 1rem;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border-color: #e2e8f0;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        }

        .demo-chip {
            cursor: pointer;
            transition: all 0.2s;
            border-radius: 8px;
            padding: 0.4rem 0.65rem;
            font-size: 0.75rem;
            border: 1px dashed #cbd5e1;
            background-color: #f8fafc;
        }

        .demo-chip:hover {
            background-color: #eff6ff;
            border-color: #3b82f6;
            color: #1d4ed8;
        }
    </style>
</head>
<body>
    <div class="login-card p-4 p-md-5">
        <div class="text-center mb-4">
            <div class="brand-box">
                <i class="bi bi-boxes"></i>
            </div>
            <h4 class="fw-bold mb-1">StockFlow</h4>
            <p class="text-muted small mb-0">Sistem Manajemen Inventaris & Pergudangan</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show p-2 px-3 small mb-3 border-0" role="alert">
                <i class="bi bi-exclamation-circle-fill me-1"></i>
                {{ $errors->first() }}
                <button type="button" class="btn-close p-2" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show p-2 px-3 small mb-3 border-0" role="alert">
                <i class="bi bi-info-circle-fill me-1"></i>
                {{ session('info') }}
                <button type="button" class="btn-close p-2" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label small fw-semibold text-secondary">Alamat Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" id="email" class="form-control border-start-0 ps-0" value="{{ old('email', 'admin@stockflow.test') }}" required autofocus placeholder="nama@perusahaan.com">
                </div>
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="password" class="form-label small fw-semibold text-secondary mb-0">Kata Sandi</label>
                </div>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="password" class="form-control border-start-0 border-end-0 ps-0" value="password" required placeholder="••••••••">
                    <button class="btn btn-light border border-start-0 text-muted" type="button" onclick="togglePassword()">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                <label class="form-check-label small text-muted" for="remember">
                    Ingat saya di perangkat ini
                </label>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Sistem
            </button>
        </form>

        <!-- Quick Demo Switcher -->
        <div class="mt-4 pt-3 border-top">
            <div class="text-muted small fw-semibold mb-2 text-center" style="font-size: 0.75rem;">
                <i class="bi bi-lightning-charge-fill text-warning me-1"></i>Akun Demo Cepat (Klik untuk mengisi):
            </div>
            <div class="d-flex flex-wrap gap-1 justify-content-center">
                <div class="demo-chip" onclick="fillLogin('admin@stockflow.test', 'password')">
                    <span class="fw-bold text-dark">Admin</span> (admin@stockflow.test)
                </div>
                <div class="demo-chip" onclick="fillLogin('staff@stockflow.test', 'password')">
                    <span class="fw-bold text-dark">Staff Gudang</span> (staff@stockflow.test)
                </div>
                <div class="demo-chip" onclick="fillLogin('manager@stockflow.test', 'password')">
                    <span class="fw-bold text-dark">Manager</span> (manager@stockflow.test)
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passInput = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (passInput.type === 'password') {
                passInput.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                passInput.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }

        function fillLogin(email, pass) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = pass;
        }
    </script>
</body>
</html>
