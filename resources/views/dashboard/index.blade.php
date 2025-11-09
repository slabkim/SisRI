@extends('layouts.app')
@php
    use Illuminate\Support\Str;

    $iconMap = [
        'total_kost' => 'bi-building',
        'total_booking' => 'bi-journal-check',
        'pending_booking' => 'bi-hourglass-split',
        'approved_booking' => 'bi-patch-check',
    ];
@endphp

@section('content')
    <div class="row gy-3 align-items-center mb-4">
        <div class="col-lg-8">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width:56px;height:56px;">
                    <i class="bi bi-person-badge fs-4"></i>
                </div>
                <div>
                    <p class="text-muted small mb-1">Selamat datang kembali</p>
                    <h1 class="h4 fw-bold mb-1">{{ $user->name }}</h1>
                    <p class="text-muted mb-0">{{ $user->isOwner() ? 'Pantau performa kost dan booking terbaru' : 'Kelola booking Anda dan temukan kost favorit' }}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 text-lg-end">
            <div class="d-inline-flex gap-2 flex-wrap">
                @if($user->isOwner())
                    <a href="{{ route('owner.kosts.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Tambah Kost</a>
                    <a href="{{ route('owner.bookings.index') }}" class="btn btn-outline-primary"><i class="bi bi-calendar-check me-1"></i>Lihat Booking</a>
                @else
                    <a href="{{ route('kosts.index') }}" class="btn btn-primary"><i class="bi bi-search-heart me-1"></i>Cari Kost</a>
                    <a href="{{ route('bookings.index') }}" class="btn btn-outline-primary"><i class="bi bi-journal-text me-1"></i>Booking Saya</a>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach ($stats as $label => $value)
            <div class="col-sm-6 col-lg-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-uppercase small text-muted">{{ str_replace('_', ' ', $label) }}</span>
                            <span class="badge bg-primary-subtle text-primary"><i class="bi {{ $iconMap[$label] ?? 'bi-bar-chart' }}"></i></span>
                        </div>
                        <h2 class="fw-bold mb-0">{{ $value }}</h2>
                        <p class="text-muted small mb-0">{{ $user->isOwner() ? 'Update terakhir ' . now()->diffForHumans() : 'Data real-time' }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">{{ $user->isOwner() ? 'Booking Terbaru' : 'Riwayat Booking' }}</h5>
                        <a href="{{ $user->isOwner() ? route('owner.bookings.index') : route('bookings.index') }}" class="text-decoration-none small">Lihat semua</a>
                    </div>
                    @if($recentBookings->isEmpty())
                        @include('components.empty', ['title' => 'Belum ada booking', 'subtitle' => 'Booking terbaru akan tampil di sini.'])
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($recentBookings as $booking)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="fw-semibold mb-1">{{ $booking->kost->name }}</h6>
                                            <p class="text-muted small mb-1">
                                                {{ $user->isOwner() ? 'Tenant: ' . ($booking->tenant->name ?? '-') : 'Owner: ' . ($booking->owner->name ?? '-') }}
                                            </p>
                                            <span class="badge bg-light text-muted border text-uppercase">{{ $booking->status }}</span>
                                        </div>
                                        <div class="text-end">
                                            <span class="small text-muted">{{ $booking->move_in_date->format('d M Y') }}</span>
                                            <p class="text-muted small mb-0">{{ $booking->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Tips & Pengumuman</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 h-100">
                                <h6 class="fw-semibold">{{ $user->isOwner() ? 'Optimalkan profil kost' : 'Lengkapi profil Anda' }}</h6>
                                <p class="text-muted small mb-0">
                                    {{ $user->isOwner() ? 'Tambahkan foto terbaru agar kost Anda tampil lebih menarik di hasil pencarian.' : 'Profil lengkap membantu owner memverifikasi calon penghuni dengan lebih cepat.' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 h-100">
                                <h6 class="fw-semibold">Bantuan cepat</h6>
                                <p class="text-muted small mb-2">Hubungi tim SisRI untuk pertanyaan transaksi atau teknis.</p>
                                <a href="mailto:support@sisri.id" class="btn btn-sm btn-outline-primary">support@sisri.id</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">{{ $user->isOwner() ? 'Kost Unggulan Anda' : 'Rekomendasi Kost' }}</h5>
                    </div>
                    @if($spotlight->isEmpty())
                        @include('components.empty', ['title' => 'Belum ada data', 'subtitle' => 'Data kost akan tampil di sini.'])
                    @else
                        @foreach($spotlight as $kost)
                            <div class="border rounded-3 p-3 mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded bg-light overflow-hidden" style="width:64px;height:64px;">
                                        @if($kost->photo_url ?? false)
                                            <img src="{{ $kost->photo_url }}" class="object-fit-cover w-100 h-100" alt="{{ $kost->name }}">
                                        @else
                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">No Foto</div>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-0">{{ $kost->name }}</h6>
                                        <p class="text-muted small mb-0">{{ Str::limit($kost->address, 50) }}</p>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="fw-semibold text-primary">Rp {{ number_format($kost->price_per_month, 0, ',', '.') }}</span>
                                    <a href="{{ route('kosts.show', $kost) }}" class="text-decoration-none small">Detail</a>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold mb-2">Support</h5>
                    <p class="text-muted small mb-3">Butuh bantuan? Tim kami siap membantu setiap hari kerja pukul 08.00-20.00 WIB.</p>
                    <div class="d-flex flex-column gap-2">
                        <a href="mailto:support@sisri.id" class="btn btn-outline-primary btn-sm"><i class="bi bi-envelope me-1"></i>Email Support</a>
                        <a href="https://wa.me/6281234567890" class="btn btn-outline-success btn-sm"><i class="bi bi-whatsapp me-1"></i>WhatsApp</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
