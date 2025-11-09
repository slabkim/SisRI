@extends('layouts.app')

@php
    $carouselId = 'kost-photos-' . $kost->id;
    $facilityIcons = [
        'wifi' => 'bi-wifi',
        'internet' => 'bi-wifi',
        'kamar mandi dalam' => 'bi-droplet',
        'ac' => 'bi-thermometer-snow',
        'parkir' => 'bi-car-front',
        'keamanan' => 'bi-shield-check',
        'laundry' => 'bi-basket',
        'dapur' => 'bi-egg-fried',
        'air panas' => 'bi-fire',
        'lemari' => 'bi-box',
    ];
@endphp

@section('content')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-3">
                @if($kost->photos->isNotEmpty())
                    <div id="{{ $carouselId }}" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner ratio ratio-16x9">
                            @foreach($kost->photos as $index => $photo)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                    <img src="{{ $photo->url }}" class="rounded-top object-fit-cover w-100 h-100" alt="Foto {{ $kost->name }}">
                                </div>
                            @endforeach
                        </div>
                        @if($kost->photos->count() > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#{{ $carouselId }}" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Sebelumnya</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#{{ $carouselId }}" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Selanjutnya</span>
                            </button>
                        @endif
                    </div>
                    @if($kost->photos->count() > 1)
                        <div class="d-flex gap-2 mt-3 overflow-auto">
                            @foreach($kost->photos as $index => $photo)
                                <button type="button"
                                    class="btn btn-outline-secondary p-0 thumbnail-trigger {{ $index === 0 ? 'active' : '' }}"
                                    data-bs-target="#{{ $carouselId }}"
                                    data-bs-slide-to="{{ $index }}">
                                    <img src="{{ $photo->url }}" class="object-fit-cover rounded" style="width: 80px; height: 60px;" alt="Thumbnail {{ $index + 1 }}">
                                </button>
                            @endforeach
                        </div>
                    @endif
                @elseif($kost->photo_url)
                    <div class="ratio ratio-16x9">
                        <img src="{{ $kost->photo_url }}" class="rounded-top object-fit-cover" alt="{{ $kost->name }}">
                    </div>
                @else
                    <div class="bg-light text-center py-5 fw-semibold text-muted">Belum ada foto</div>
                @endif
                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                        <div>
                            <h1 class="h4 mb-1">{{ $kost->name }}</h1>
                            <p class="text-muted mb-0"><i class="bi bi-geo-alt me-1"></i>{{ $kost->address }}</p>
                        </div>
                        <div class="text-md-end">
                            <div class="fs-3 fw-bold text-primary">Rp {{ number_format($kost->price_per_month, 0, ',', '.') }}</div>
                            <div class="text-muted small">per bulan</div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 h-100">
                                <span class="text-muted small text-uppercase">Update Terakhir</span>
                                <p class="fw-semibold mb-0">{{ $kost->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 h-100">
                                <span class="text-muted small text-uppercase">Status Booking</span>
                                <p class="fw-semibold mb-0">
                                    @php $approvedCount = $kost->approved_bookings_count ?? 0; @endphp
                                    {{ $approvedCount > 0 ? $approvedCount . ' disetujui' : 'Belum ada booking' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 h-100">
                                <span class="text-muted small text-uppercase">Pemilik</span>
                                <p class="fw-semibold mb-0">{{ $kost->owner?->name ?? 'Tidak diketahui' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="fw-bold mb-2">Deskripsi Kost</h5>
                        <p class="text-muted mb-0">{{ $kost->description ?? 'Belum ada deskripsi.' }}</p>
                    </div>

                    <div class="mb-4">
                        <h5 class="fw-bold mb-2">Fasilitas</h5>
                        <div class="row g-2">
                            @forelse($kost->facilities ?? [] as $facility)
                                @php
                                    $key = Str::lower($facility);
                                    $icon = collect($facilityIcons)->first(fn ($icon, $name) => Str::contains($key, $name));
                                @endphp
                                <div class="col-6 col-md-4">
                                    <div class="border rounded-3 p-2 d-flex align-items-center gap-2">
                                        <i class="bi {{ $icon ?? 'bi-check-circle' }} text-primary"></i>
                                        <span class="small">{{ $facility }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <span class="text-muted">Belum ada data fasilitas.</span>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    @if($kost->map_embed || (!empty($kost->latitude) && !empty($kost->longitude)))
                        <div class="mb-4">
                            <h5 class="fw-bold mb-2">Lokasi</h5>
                            @if($kost->map_embed)
                                <div class="ratio ratio-16x9 rounded overflow-hidden mb-3">
                                    {!! $kost->map_embed !!}
                                </div>
                            @endif
                            @if(!empty($kost->latitude) && !empty($kost->longitude))
                                <div id="kostLeafletMap" style="height: 320px;" class="rounded overflow-hidden border"></div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-1">Pemilik Kost</h5>
                    <p class="mb-1">{{ $kost->owner?->name }}</p>
                    <p class="text-muted small mb-0"><i class="bi bi-envelope me-1"></i>{{ $kost->owner?->email }}</p>
                </div>
            </div>

            @auth
                @if(auth()->user()->isMahasiswa())
                    <div class="card shadow-sm border-0" id="bookingCard">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3">Ajukan Booking</h5>
                            <form action="{{ route('bookings.store', $kost) }}" method="POST" id="bookingForm">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Masuk</label>
                                    <input type="date" name="move_in_date" class="form-control" value="{{ old('move_in_date') }}" required data-range="future">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Telepon</label>
                                    <input type="text" name="tenant_phone" class="form-control" value="{{ old('tenant_phone') }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Catatan Tambahan</label>
                                    <textarea name="tenant_notes" class="form-control" rows="3" placeholder="Opsional">{{ old('tenant_notes') }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Kirim Permintaan</button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">Pemilik kost tidak dapat melakukan booking.</div>
                @endif
            @else
                <div class="alert alert-warning">
                    Silakan <a href="{{ route('login') }}">masuk</a> sebagai mahasiswa untuk mengajukan booking.
                </div>
            @endauth
        </div>
    </div>

    <div class="d-lg-none d-flex position-fixed bottom-0 start-0 end-0 bg-white border-top shadow py-3 px-3 align-items-center justify-content-between gap-3 mobile-cta-bar">
        <div>
            <div class="fw-bold text-primary">Rp {{ number_format($kost->price_per_month, 0, ',', '.') }}/bulan</div>
            <small class="text-muted">{{ $kost->name }}</small>
        </div>
        @auth
            @if(auth()->user()->isMahasiswa())
                <button class="btn btn-primary" id="mobileBookingTrigger">Booking Sekarang</button>
            @else
                <a href="{{ route('kosts.show', $kost) }}" class="btn btn-primary">Detail Lengkap</a>
            @endif
        @else
            <a href="{{ route('login') }}" class="btn btn-primary">Masuk untuk Booking</a>
        @endauth
    </div>
    <div class="d-lg-none" style="height: 90px;"></div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const carouselElement = document.getElementById('{{ $carouselId }}');
            if (carouselElement) {
                const carouselInstance = new bootstrap.Carousel(carouselElement);
                document.querySelectorAll('.thumbnail-trigger').forEach(button => {
                    button.addEventListener('click', () => {
                        document.querySelectorAll('.thumbnail-trigger').forEach(btn => btn.classList.remove('active'));
                        button.classList.add('active');
                    });
                });

                carouselElement.addEventListener('slid.bs.carousel', event => {
                    const index = event.to;
                    document.querySelectorAll('.thumbnail-trigger').forEach((btn, idx) => {
                        btn.classList.toggle('active', idx === index);
                    });
                });
            }

            const mobileTrigger = document.getElementById('mobileBookingTrigger');
            if (mobileTrigger) {
                mobileTrigger.addEventListener('click', () => {
                    const bookingForm = document.getElementById('bookingForm');
                    if (bookingForm) {
                        bookingForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
            }
        });
    </script>

    @if(!empty($kost->latitude) && !empty($kost->longitude))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const initMap = () => {
                    const map = L.map('kostLeafletMap').setView([{{ $kost->latitude }}, {{ $kost->longitude }}], 16);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(map);
                    L.marker([{{ $kost->latitude }}, {{ $kost->longitude }}]).addTo(map).bindPopup(@json($kost->name));
                };

                if (window.L) {
                    initMap();
                } else {
                    const link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                    document.head.appendChild(link);

                    const script = document.createElement('script');
                    script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                    script.onload = initMap;
                    document.body.appendChild(script);
                }
            });
        </script>
    @endif
@endpush
