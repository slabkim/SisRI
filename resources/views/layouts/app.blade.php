<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SisRI - Sistem Rekomendasi Indekost</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --brand: #3B82F6;
            --brand-dark: #1D4ED8;
            --brand-light: #EFF6FF;
        }

        body {
            background-color: #f5f6fa;
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        .nav-link.active {
            font-weight: 600;
        }

        .badge-status {
            text-transform: uppercase;
        }

        .card {
            border-radius: 1rem;
        }

        .btn-primary {
            background-color: var(--brand);
            border-color: var(--brand);
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: var(--brand-dark);
            border-color: var(--brand-dark);
        }

        a {
            color: var(--brand);
        }

        a:hover {
            color: var(--brand-dark);
        }

        [data-bs-theme="dark"] body {
            background-color: #0f172a;
            color: #e2e8f0;
        }

        [data-bs-theme="dark"] .navbar,
        [data-bs-theme="dark"] .card,
        [data-bs-theme="dark"] .offcanvas,
        [data-bs-theme="dark"] .dropdown-menu {
            background-color: #111c32;
            color: #e2e8f0;
        }

        [data-bs-theme="dark"] .card {
            border-color: rgba(148, 163, 184, 0.2);
        }

        .facility-chip.active {
            background-color: var(--brand);
            border-color: var(--brand);
            color: #fff;
        }

        .mobile-cta-bar {
            z-index: 1070;
        }

        .chart-container {
            position: relative;
            width: 100%;
        }

        #backToTop {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 1090;
            display: none;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.2);
        }

        .row-highlight {
            animation: rowFlash 2s ease-in-out;
        }

        @keyframes rowFlash {
            0% {
                background-color: rgba(59, 130, 246, 0.25);
            }

            100% {
                background-color: transparent;
            }
        }

        .toast-container {
            z-index: 1080;
        }

        footer {
            background: #0f172a;
            color: #e2e8f0;
        }

        footer .text-muted {
            color: #cbd5f5 !important;
        }

        footer a.text-muted {
            color: #cbd5f5 !important;
        }

        footer .form-control,
        footer .form-select {
            background-color: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        footer .btn-outline-light {
            border-color: rgba(255, 255, 255, 0.4);
            color: #fff;
        }

        .sticky-search {
            position: sticky;
            top: 1rem;
            z-index: 1020;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-white border-bottom">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">SisRI</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('kosts.index') ? 'active' : '' }}"
                            href="{{ route('kosts.index') }}">Daftar Kost</a>
                    </li>
                    @auth
                        @if (auth()->user()->isOwner())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}"
                                    href="{{ route('owner.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('owner.kosts.*') ? 'active' : '' }}"
                                    href="{{ route('owner.kosts.index') }}">Kelola Kost</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('owner.bookings.*') ? 'active' : '' }}"
                                    href="{{ route('owner.bookings.index') }}">Booking</a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                                    href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('bookings.index') ? 'active' : '' }}"
                                    href="{{ route('bookings.index') }}">Booking Saya</a>
                            </li>
                        @endif
                    @endauth
                </ul>
                <ul class="navbar-nav ms-auto">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Masuk</a>
                        </li>
                        <li class="nav-item mt-1">
                            <a class="btn btn-sm btn-primary" href="{{ route('register') }}">Daftar</a>
                        </li>
                    @else
                        <li class="nav-item d-flex align-items-center me-3">
                            <span class="text-muted small">{{ auth()->user()->name }}
                                ({{ ucfirst(auth()->user()->role) }})
                            </span>
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="btn btn-outline-danger btn-sm" type="submit">Logout</button>
                            </form>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>

    <footer class="mt-5 pt-5 pb-4">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="d-flex align-items-center mb-3">
                        <span class="fw-bold fs-4">SisRI</span>
                    </div>
                    <p class="text-muted">Sistem rekomendasi kost yang membantu mahasiswa menemukan hunian nyaman dekat
                        kampus.</p>
                    <div class="d-flex gap-2">
                        <img src="https://developer.apple.com/assets/elements/badges/download-on-the-app-store.svg"
                            alt="App Store" height="40">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg"
                            alt="Play Store" height="40">
                    </div>
                </div>
                <div class="col-md-2">
                    <h6 class="text-uppercase mb-3">Navigasi</h6>
                    <ul class="list-unstyled text-muted">
                        <li><a href="#" class="text-decoration-none text-muted">Tentang</a></li>
                        <li><a href="#" class="text-decoration-none text-muted">Fitur</a></li>
                        <li><a href="#" class="text-decoration-none text-muted">Harga</a></li>
                        <li><a href="#" class="text-decoration-none text-muted">Blog</a></li>
                    </ul>
                </div>
                <div class="col-md-2">
                    <h6 class="text-uppercase mb-3">Bantuan</h6>
                    <ul class="list-unstyled text-muted">
                        <li><a href="#" class="text-decoration-none text-muted">FAQ</a></li>
                        <li><a href="#" class="text-decoration-none text-muted">Pusat Bantuan</a></li>
                        <li><a href="#" class="text-decoration-none text-muted">Panduan Owner</a></li>
                        <li><a href="#" class="text-decoration-none text-muted">Karier</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6 class="text-uppercase mb-3">Kontak</h6>
                    <p class="mb-1 text-muted"><i class="bi bi-envelope me-2"></i>support@sisri.id</p>
                    <p class="mb-1 text-muted"><i class="bi bi-whatsapp me-2"></i>+62 812-3456-7890</p>
                    <p class="mb-3 text-muted"><i class="bi bi-geo-alt me-2"></i>Jl. Merdeka No. 123, Jakarta</p>
                    <div class="d-flex gap-2 mb-3">
                        <a href="#" class="btn btn-outline-light btn-sm"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="btn btn-outline-light btn-sm"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="btn btn-outline-light btn-sm"><i class="bi bi-youtube"></i></a>
                        <a href="#" class="btn btn-outline-light btn-sm"><i class="bi bi-linkedin"></i></a>
                    </div>
                    <form class="d-flex">
                        <input type="email" class="form-control form-control-sm me-2" placeholder="Email Anda">
                        <button class="btn btn-primary btn-sm" type="submit">Berlangganan</button>
                    </form>
                    <small class="text-muted d-block mt-2">Kami hanya mengirim info penting. Bisa berhenti kapan
                        saja.</small>
                    <div class="mt-4">
                        <div class="d-flex align-items-center gap-3">
                            <div>
                                <strong class="fs-3">4.8/5</strong>
                                <p class="text-muted small mb-0">Berdasarkan 1.200+ ulasan.</p>
                            </div>
                            <div class="text-warning fs-5">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-half"></i>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <span class="badge bg-secondary-subtle text-dark">SSL Secure</span>
                            <span class="badge bg-secondary-subtle text-dark">ISO 27001</span>
                            <span class="badge bg-secondary-subtle text-dark">PCI DSS</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-3 mt-4">
                <div class="col-md-6">
                    <h6 class="text-uppercase text-muted small mb-2">Bahasa & Mata Uang</h6>
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm">
                            <option>Bahasa Indonesia</option>
                            <option>English</option>
                        </select>
                        <select class="form-select form-select-sm">
                            <option>IDR (Rp)</option>
                            <option>USD ($)</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-uppercase text-muted small mb-2">Metode Pembayaran</h6>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/4/41/Visa_Logo.png" alt="Visa"
                            height="22">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/0/04/Mastercard-logo.png"
                            alt="Mastercard" height="22">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/5/5b/GoPay_logo.svg" alt="GoPay"
                            height="18">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/9/95/Logo_ovo_purple.svg"
                            alt="OVO" height="18">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/6/6a/Logo_BCA.svg" alt="BCA"
                            height="18">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/a/a3/Logo_Bank_BRI.png"
                            alt="BRI" height="18">
                    </div>
                </div>
            </div>
            <hr class="border-secondary my-4">
            <div class="d-flex flex-column flex-md-row justify-content-between text-muted small">
                <span>© {{ now()->year }} SisRI. Semua hak dilindungi.</span>
                <div class="d-flex gap-3">
                    <a href="#" class="text-decoration-none text-muted">Kebijakan Privasi</a>
                    <a href="#" class="text-decoration-none text-muted">Syarat & Ketentuan</a>
                    <a href="#" class="text-decoration-none text-muted">Kebijakan Cookie</a>
                </div>
            </div>
        </div>
    </footer>
    <button id="backToTop" class="btn btn-primary rounded-circle"><i class="bi bi-arrow-up"></i></button>

    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080">
        <div id="appToast" class="toast align-items-center border-0" role="alert" aria-live="assertive"
            aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastMsg">Berhasil.</div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Tutup"></button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            window.showToast = (message, type = 'success') => {
                const toastEl = document.getElementById('appToast');
                if (!toastEl) return;
                const toastMsg = document.getElementById('toastMsg');
                toastMsg.textContent = message || 'Berhasil.';
                toastEl.classList.remove('text-bg-success', 'text-bg-danger', 'text-bg-info');
                toastEl.classList.add(type === 'error' ? 'text-bg-danger' : (type === 'info' ? 'text-bg-info' :
                    'text-bg-success'));
                new bootstrap.Toast(toastEl, {
                    delay: 3500
                }).show();
            };
        })();

        document.addEventListener('DOMContentLoaded', function() {
            const currencyInputs = document.querySelectorAll('[data-currency-input]');
            const formatValue = (value) => {
                const numeric = value.replace(/\D/g, '');
                if (!numeric) return '';
                return new Intl.NumberFormat('id-ID').format(Number(numeric));
            };

            currencyInputs.forEach(input => {
                if (input.value) {
                    input.value = formatValue(input.value);
                }

                input.addEventListener('input', () => {
                    input.value = formatValue(input.value);
                });

                const form = input.closest('form');
                if (form) {
                    form.addEventListener('submit', () => {
                        input.value = input.value.replace(/\D/g, '');
                    });
                }
            });

            const today = new Date();
            const maxDate = new Date();
            maxDate.setDate(today.getDate() + 180);
            const minStr = today.toISOString().slice(0, 10);
            const maxStr = maxDate.toISOString().slice(0, 10);

            document.querySelectorAll('input[type="date"][data-range="future"]').forEach(input => {
                input.min = minStr;
                input.max = maxStr;
            });

            const backToTop = document.getElementById('backToTop');
            if (backToTop) {
                window.addEventListener('scroll', () => {
                    backToTop.style.display = window.scrollY > 400 ? 'inline-flex' : 'none';
                });
                backToTop.addEventListener('click', () => {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }
        });
    </script>
    @if (session('success'))
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', () => showToast(@json(session('success')), 'success'));
            </script>
        @endpush
    @endif
    @if (session('error'))
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', () => showToast(@json(session('error')), 'error'));
            </script>
        @endpush
    @endif
    @stack('scripts')
</body>

</html>
