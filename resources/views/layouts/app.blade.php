<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - StockFlow Warehouse Management</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary-navy: #0f172a;
            --primary-sidebar: #1e293b;
            --sidebar-hover: #334155;
            --accent-blue: #3b82f6;
            --accent-teal: #0ea5e9;
            --bg-light: #f8fafc;
            --card-border: #e2e8f0;
            --text-dark: #0f172a;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* Layout wrapper */
        .app-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 260px;
            background-color: var(--primary-sidebar);
            color: #f1f5f9;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1030;
        }

        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            text-decoration: none;
            color: #fff;
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #3b82f6 0%, #0ea5e9 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: #fff;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .sidebar-menu {
            padding: 1rem 0.75rem;
            flex-grow: 1;
            overflow-y: auto;
        }

        .menu-header {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            padding: 0.75rem 0.75rem 0.25rem;
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 0.85rem;
            color: #cbd5e1;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            margin-bottom: 0.2rem;
        }

        .nav-link-custom:hover {
            background-color: var(--sidebar-hover);
            color: #fff;
        }

        .nav-link-custom.active {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
        }

        .nav-link-custom i {
            font-size: 1.1rem;
            width: 22px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            background-color: var(--bg-light);
        }

        /* Top Navbar */
        .top-navbar {
            background-color: #ffffff;
            border-bottom: 1px solid var(--card-border);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        .content-body {
            padding: 1.75rem;
            flex-grow: 1;
        }

        /* Modern Card Styling */
        .card-custom {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-custom:hover {
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
        }

        /* Stat Cards */
        .stat-card {
            border-radius: 14px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid var(--card-border);
            background: #fff;
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .stat-card.primary::after { background: #3b82f6; }
        .stat-card.success::after { background: #10b981; }
        .stat-card.danger::after { background: #ef4444; }
        .stat-card.warning::after { background: #f59e0b; }
        .stat-card.info::after { background: #06b6d4; }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .bg-light-blue { background-color: #eff6ff; color: #2563eb; }
        .bg-light-green { background-color: #ecfdf5; color: #059669; }
        .bg-light-red { background-color: #fef2f2; color: #dc2626; }
        .bg-light-yellow { background-color: #fffbeb; color: #d97706; }
        .bg-light-cyan { background-color: #ecfeff; color: #0891b2; }

        /* Custom Table */
        .table-custom {
            width: 100%;
            vertical-align: middle;
        }

        .table-custom th {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            background-color: #f8fafc;
            border-top: none;
            padding: 0.85rem 1rem;
        }

        .table-custom td {
            padding: 0.85rem 1rem;
            font-size: 0.875rem;
            border-color: #f1f5f9;
        }

        /* Badges */
        .badge-soft-success {
            background-color: #d1fae5;
            color: #065f46;
            font-weight: 600;
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
        }

        .badge-soft-danger {
            background-color: #fee2e2;
            color: #991b1b;
            font-weight: 600;
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
        }

        .badge-soft-warning {
            background-color: #fef3c7;
            color: #92400e;
            font-weight: 600;
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
        }

        .badge-soft-info {
            background-color: #e0f2fe;
            color: #0369a1;
            font-weight: 600;
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
        }

        .badge-soft-secondary {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 600;
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
        }

        /* Responsive sidebar toggle */
        @media (max-width: 991.98px) {
            .sidebar {
                position: fixed;
                top: 0;
                bottom: 0;
                left: -260px;
            }

            .sidebar.show {
                left: 0;
            }

            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(15, 23, 42, 0.5);
                z-index: 1025;
                display: none;
            }

            .sidebar-overlay.show {
                display: block;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <div class="app-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="sidebar" id="sidebar">
            <a href="{{ route('dashboard') }}" class="sidebar-brand">
                <div class="brand-icon">
                    <i class="bi bi-boxes"></i>
                </div>
                <div>
                    <h5 class="m-0 fw-bold tracking-tight">StockFlow</h5>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.65rem;">Gudang v1.0</span>
                </div>
            </a>

            <div class="sidebar-menu">
                <div class="menu-header">Menu Utama</div>
                <a href="{{ route('dashboard') }}" class="nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i>
                    <span>Dashboard</span>
                    @php
                        $lowStockWarning = \App\Models\Product::lowStock()->count();
                    @endphp
                    @if($lowStockWarning > 0)
                        <span class="badge bg-danger ms-auto rounded-pill">{{ $lowStockWarning }}</span>
                    @endif
                </a>

                <div class="menu-header">Mutasi & Transaksi</div>
                <a href="{{ route('transactions.create-in') }}" class="nav-link-custom {{ request()->routeIs('transactions.create-in') ? 'active' : '' }}">
                    <i class="bi bi-box-arrow-in-down text-success"></i>
                    <span>Barang Masuk (In)</span>
                </a>
                <a href="{{ route('transactions.create-out') }}" class="nav-link-custom {{ request()->routeIs('transactions.create-out') ? 'active' : '' }}">
                    <i class="bi bi-box-arrow-up-right text-danger"></i>
                    <span>Barang Keluar (Out)</span>
                </a>
                <a href="{{ route('transactions.index') }}" class="nav-link-custom {{ request()->routeIs('transactions.index', 'transactions.show') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i>
                    <span>Riwayat Transaksi</span>
                </a>

                <div class="menu-header">Master Inventaris</div>
                <a href="{{ route('products.index') }}" class="nav-link-custom {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i>
                    <span>Data Barang / Stok</span>
                </a>
                <a href="{{ route('categories.index') }}" class="nav-link-custom {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i>
                    <span>Kategori Barang</span>
                </a>
                <a href="{{ route('suppliers.index') }}" class="nav-link-custom {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                    <i class="bi bi-truck"></i>
                    <span>Data Pemasok</span>
                </a>

                <div class="menu-header">Kontrol & Laporan</div>
                <a href="{{ route('adjustments.index') }}" class="nav-link-custom {{ request()->routeIs('adjustments.*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-check"></i>
                    <span>Stock Opname</span>
                </a>
                <a href="{{ route('reports.index') }}" class="nav-link-custom {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-line"></i>
                    <span>Laporan & Ekspor</span>
                </a>

                <div class="menu-header">Pengaturan</div>
                <a href="{{ route('profile') }}" class="nav-link-custom {{ request()->routeIs('profile') ? 'active' : '' }}">
                    <i class="bi bi-person-gear"></i>
                    <span>Profil Pengguna</span>
                </a>
            </div>

            <!-- User footer card in sidebar -->
            <div class="p-3 border-top border-white-10" style="background: rgba(0,0,0,0.15);">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px; font-size: 0.9rem;">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="overflow-hidden flex-grow-1">
                        <div class="fw-semibold text-truncate text-white" style="font-size: 0.85rem;">{{ Auth::user()->name ?? 'Petugas' }}</div>
                        <span class="badge bg-light text-dark text-capitalize" style="font-size: 0.65rem;">{{ Auth::user()->role ?? 'Staff' }}</span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-light border-0 p-1" title="Keluar">
                            <i class="bi bi-box-arrow-right fs-6"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="main-content">
            <!-- Top Navbar -->
            <nav class="top-navbar">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-outline-secondary btn-sm d-lg-none" type="button" onclick="toggleSidebar()">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <div class="d-none d-md-flex align-items-center text-muted small">
                        <i class="bi bi-building-gear me-1"></i>
                        <span class="fw-medium text-dark">Gudang Utama StockFlow</span>
                        <span class="mx-2">•</span>
                        <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <!-- Quick action buttons -->
                    <a href="{{ route('transactions.create-in') }}" class="btn btn-sm btn-success d-none d-sm-inline-flex align-items-center gap-1">
                        <i class="bi bi-plus-lg"></i>
                        <span>Barang Masuk</span>
                    </a>
                    <a href="{{ route('transactions.create-out') }}" class="btn btn-sm btn-danger d-none d-sm-inline-flex align-items-center gap-1">
                        <i class="bi bi-dash-lg"></i>
                        <span>Barang Keluar</span>
                    </a>

                    <!-- User dropdown -->
                    <div class="dropdown ms-2">
                        <button class="btn btn-light btn-sm border dropdown-toggle d-flex align-items-center gap-2 px-2 py-1" type="button" data-bs-toggle="dropdown">
                            <span class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 26px; height: 26px; font-size: 0.75rem;">
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                            </span>
                            <span class="d-none d-sm-inline small fw-medium">{{ Auth::user()->name ?? 'User' }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                            <li><h6 class="dropdown-header">{{ Auth::user()->email ?? '' }}</h6></li>
                            <li><a class="dropdown-item small" href="{{ route('profile') }}"><i class="bi bi-person me-2"></i>Edit Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item small text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Page Body Content -->
            <main class="content-body">
                <!-- Session Alerts -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4 border-0 shadow-sm" role="alert">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4 border-0 shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show d-flex align-items-center gap-2 mb-4 border-0 shadow-sm" role="alert">
                        <i class="bi bi-info-circle-fill fs-5"></i>
                        <div>{{ session('info') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-top py-3 px-4 text-center text-muted small mt-auto">
                StockFlow &copy; {{ date('Y') }} — Sistem Manajemen Inventaris & Pergudangan Modern. Dirancang dengan Laravel 12 & Bootstrap 5.
            </footer>
        </div>
    </div>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        // Helper formatted currency format in JS
        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(number);
        }
    </script>
    @stack('scripts')
</body>
</html>
