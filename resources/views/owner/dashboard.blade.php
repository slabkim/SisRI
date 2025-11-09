@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Ringkasan Kost Anda</h1>
            <p class="text-muted mb-0">Pantau performa kost, booking terbaru, dan data penting lainnya.</p>
        </div>
        <a href="{{ route('owner.kosts.create') }}" class="btn btn-primary">
            Tambah Kost Baru
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <span class="text-muted text-uppercase small">Total Kost</span>
                    <h3 class="fw-bold mt-2 mb-0">{{ $stats['kost'] }}</h3>
                    <p class="small text-muted mb-0">Seluruh kost aktif yang Anda kelola.</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <span class="text-muted text-uppercase small">Kamar Tersedia</span>
                    <h3 class="fw-bold mt-2 mb-0">{{ $stats['available'] }}</h3>
                    <p class="small text-muted mb-0">Perkiraan slot kosong siap di-booking.</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <span class="text-muted text-uppercase small">Booking Pending</span>
                    <h3 class="fw-bold mt-2 mb-0">{{ $stats['pending'] }}</h3>
                    <p class="small text-muted mb-0">Menunggu tindak lanjut Anda.</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <span class="text-muted text-uppercase small">Approval Rate (30 hari)</span>
                    <h3 class="fw-bold mt-2 mb-0">
                        {{ $stats['approvalRate'] !== null ? $stats['approvalRate'] . '%' : 'N/A' }}
                    </h3>
                    <p class="small text-muted mb-0">Perbandingan persetujuan vs penolakan.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-2">Aksi Cepat</h5>
                    <p class="text-muted small mb-3">Kelola operasional harian lebih cepat dengan pintasan berikut.</p>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <a href="{{ route('owner.kosts.create') }}" class="btn btn-light w-100 text-start">
                                <i class="bi bi-plus-circle me-2 text-primary"></i>Tambah Kost
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('owner.bookings.index') }}" class="btn btn-light w-100 text-start">
                                <i class="bi bi-calendar-check me-2 text-primary"></i>Kelola Booking
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('owner.bookings.export') }}" class="btn btn-light w-100 text-start">
                                <i class="bi bi-filetype-csv me-2 text-primary"></i>Export Data
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('owner.kosts.index') }}" class="btn btn-light w-100 text-start">
                                <i class="bi bi-pencil-square me-2 text-primary"></i>Update Tarif
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="#" class="btn btn-light w-100 text-start">
                                <i class="bi bi-bar-chart-line me-2 text-primary"></i>Laporan Bulan Ini
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="mailto:support@sisri.id" class="btn btn-light w-100 text-start">
                                <i class="bi bi-headset me-2 text-primary"></i>Hubungi Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-2">Rating & Keamanan</h5>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div>
                            <span class="fs-3 fw-bold">4.8/5</span>
                            <p class="text-muted small mb-0">Rata-rata ulasan penghuni.</p>
                        </div>
                        <div class="text-warning fs-4">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-half"></i>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-secondary-subtle text-dark">SSL Secure</span>
                        <span class="badge bg-secondary-subtle text-dark">ISO 27001</span>
                        <span class="badge bg-secondary-subtle text-dark">PCI DSS</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Tren Booking 30 Hari</h5>
                            <p class="text-muted small mb-0">Jumlah permintaan booking per hari.</p>
                        </div>
                    </div>
                    <div class="chart-container" style="height:260px;">
                        <canvas id="bookingTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Booking Pending Terbaru</h5>
                            <p class="text-muted small mb-0">Tindak lanjuti agar calon penyewa tidak menunggu lama.</p>
                        </div>
                        <a href="{{ route('owner.bookings.index') }}" class="btn btn-sm btn-outline-primary">Lihat semua</a>
                    </div>
                    @if ($latestPending->isEmpty())
                        <div class="text-center text-muted py-5">
                            <span class="fw-semibold d-block mb-1">Belum ada permintaan terbaru</span>
                            <small>Anda akan melihat daftar booking menunggu persetujuan di sini.</small>
                        </div>
                    @else
                        <div class="list-group list-group-flush flex-grow-1">
                            @foreach ($latestPending as $booking)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="mb-1 fw-semibold">
                                                {{ $booking->kost?->name ?? 'Kost tidak tersedia' }}</h6>
                                            <p class="text-muted small mb-1">Oleh
                                                {{ $booking->tenant?->name ?? 'Mahasiswa' }}</p>
                                            <span
                                                class="badge bg-warning-subtle text-warning-emphasis text-uppercase small">Pending</span>
                                        </div>
                                        <div class="text-end small text-muted">
                                            <span>{{ $booking->created_at?->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-1">Kost Terbaru</h5>
                    <p class="text-muted small mb-0">Ikhtisar singkat kost yang baru Anda kelola atau perbarui.</p>
                </div>
                <a href="{{ route('owner.kosts.index') }}" class="btn btn-sm btn-outline-secondary">Kelola Kost</a>
            </div>
            @if ($recentKosts->isEmpty())
                <div class="text-center text-muted py-5">
                    <span class="fw-semibold d-block mb-1">Belum ada kost</span>
                    <small>Tambahkan kost pertama Anda untuk mulai menerima booking.</small>
                </div>
            @else
                <div class="row g-3">
                    @foreach ($recentKosts as $kost)
                        <div class="col-md-4">
                            <div class="border rounded h-100 p-3">
                                @if ($kost->photo_url)
                                    <img src="{{ $kost->photo_url }}" class="img-fluid rounded mb-3"
                                        alt="{{ $kost->name }}">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3"
                                        style="height: 120px;">
                                        <span class="text-muted small fw-semibold">Belum ada foto</span>
                                    </div>
                                @endif
                                <h6 class="fw-bold mb-1">{{ $kost->name }}</h6>
                                <p class="text-muted small mb-2">{{ $kost->address }}</p>
                                <p class="fw-semibold mb-1">Rp
                                    {{ number_format($kost->price_per_month, 0, ',', '.') }}/bulan</p>
                                <p class="small text-muted mb-3">Diperbarui {{ $kost->updated_at->diffForHumans() }}</p>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('owner.kosts.edit', $kost) }}"
                                        class="btn btn-sm btn-outline-primary">Edit</a>
                                    <a href="{{ route('kosts.show', $kost) }}" class="btn btn-sm btn-light">Lihat
                                        publik</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('bookingTrendChart');
            if (!ctx) return;

            // Destroy any existing chart instance
            if (window.bookingTrendChartInstance) {
                window.bookingTrendChartInstance.destroy();
            }

            const labels = @json($chart['labels']);
            const values = @json($chart['values']);

            const hasData = values.some(value => value > 0);
            const datasetColor = hasData ? '#4c6ef5' : '#ced4da';

            window.bookingTrendChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Booking',
                        data: values,
                        borderColor: datasetColor,
                        backgroundColor: datasetColor + '33',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2,
                        pointRadius: 3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: context => `${context.parsed.y} booking`
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            },
                            grid: {
                                color: '#f1f3f5'
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
