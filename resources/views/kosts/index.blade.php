@extends('layouts.app')
@php
    use Illuminate\Support\Str;

    $popularFacilities = [
        'WiFi',
        'Kamar mandi dalam',
        'AC',
    ];
    $selectedFacilities = $filters['facilities'] ?? [];
    $sort = $filters['sort'] ?? 'newest';
    $perPage = $filters['per_page'] ?? 9;
@endphp

@section('content')
    <section class="hero rounded-4 bg-white shadow-sm p-4 p-md-5 mb-4 position-relative overflow-hidden">
        <div class="position-absolute top-0 end-0 opacity-25 pe-5 pt-4 d-none d-md-block">
            <i class="bi bi-buildings display-1 text-primary"></i>
        </div>
        <div class="position-relative">
            <span class="badge bg-primary-subtle text-primary-emphasis text-uppercase mb-2">Temukan Hunianmu</span>
            <h1 class="display-6 fw-bold mb-3">Kost nyaman dekat kampus & pusat aktivitas</h1>
            <p class="lead text-muted mb-4">Filter berdasarkan lokasi, fasilitas favorit, dan budget. Klik sekali untuk booking cepat.</p>
        </div>
        <div class="position-relative">
            <form id="kostSearchForm" method="GET" action="{{ route('kosts.index') }}" class="bg-white rounded-4 shadow-sm p-3 p-md-4 sticky-search">
                <input type="hidden" name="sort" id="heroSortInput" value="{{ $sort }}">
                <input type="hidden" name="per_page" id="heroPerPageInput" value="{{ $perPage }}">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-4">
                        <label class="form-label">Cari nama atau lokasi</label>
                        <input type="text" class="form-control" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Contoh: dekat Universitas A">
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <label class="form-label">Harga minimal</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" name="price_min" value="{{ $filters['price_min'] ?? '' }}" min="0">
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <label class="form-label">Harga maksimal</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" name="price_max" value="{{ $filters['price_max'] ?? '' }}" min="0">
                        </div>
                    </div>
                    <div class="col-lg-2 d-grid">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-search me-1"></i>Cari Kost</button>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-muted small text-uppercase d-block mb-2">Fasilitas Populer</span>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($popularFacilities as $facility)
                            @php $id = 'facility-chip-' . Str::slug($facility); @endphp
                            <input type="checkbox" class="d-none" name="facilities[]" value="{{ $facility }}" id="{{ $id }}" @checked(in_array($facility, $selectedFacilities))>
                            <button class="btn btn-sm btn-outline-primary facility-chip {{ in_array($facility, $selectedFacilities) ? 'active' : '' }}"
                                type="button"
                                data-target="{{ $id }}">
                                <i class="bi bi-star me-1"></i>{{ $facility }}
                            </button>
                        @endforeach
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFacilities">
                            <i class="bi bi-sliders me-1"></i>Lainnya
                        </button>
                        @if(!empty($selectedFacilities))
                            <a href="{{ route('kosts.index') }}" class="btn btn-sm btn-light">Reset</a>
                        @endif
                    </div>
                    <div class="collapse mt-3" id="advancedFacilities">
                        <div class="row g-2">
                            @foreach ($facilityOptions as $facility)
                                @continue(in_array($facility, $popularFacilities))
                                <div class="col-sm-4 col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="facilities[]" value="{{ $facility }}"
                                            id="facility-{{ Str::slug($facility) }}"
                                            @checked(in_array($facility, $selectedFacilities))>
                                        <label class="form-check-label small" for="facility-{{ Str::slug($facility) }}">{{ $facility }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
        <div class="text-muted small">
            @if($kosts->total())
                Menampilkan {{ $kosts->firstItem() }} - {{ $kosts->lastItem() }} dari {{ $kosts->total() }} kost
            @else
                Tidak ada kost untuk ditampilkan
            @endif
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="text-muted small text-uppercase">Urutkan</span>
            <select id="listSortSelect" class="form-select form-select-sm w-auto">
                <option value="newest" @selected($sort === 'newest')>Terbaru</option>
                <option value="price_asc" @selected($sort === 'price_asc')>Harga terendah</option>
                <option value="price_desc" @selected($sort === 'price_desc')>Harga tertinggi</option>
                <option value="popular_desc" @selected($sort === 'popular_desc')>Paling diminati</option>
            </select>
            <span class="text-muted small text-uppercase">Per halaman</span>
            <select id="listPerPageSelect" class="form-select form-select-sm w-auto">
                @foreach([9, 18, 30] as $option)
                    <option value="{{ $option }}" @selected((int) $perPage === $option)>{{ $option }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div id="kostSkeleton" class="row g-3 mb-4">
        @for($i = 0; $i < 3; $i++)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm placeholder-wave">
                    <div class="placeholder" style="height: 180px;"></div>
                    <div class="card-body">
                        <h5 class="card-title placeholder col-8"></h5>
                        <p class="card-text placeholder col-6"></p>
                        <div class="placeholder col-4 mb-2"></div>
                        <span class="placeholder col-3"></span>
                    </div>
                </div>
            </div>
        @endfor
    </div>

    <div id="kostResults" class="row g-3 d-none">
        @forelse ($kosts as $kost)
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="position-relative">
                        @if ($kost->created_at && $kost->created_at->greaterThan(now()->subDays(7)))
                            <span class="badge bg-success position-absolute top-0 start-0 m-3">Baru</span>
                        @elseif(($kost->approved_bookings_count ?? 0) > 4)
                            <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-3">Populer</span>
                        @endif
                        <div class="ratio ratio-16x9">
                            @if($kost->photo_url)
                                <img src="{{ $kost->photo_url }}" class="rounded-top object-fit-cover" alt="{{ $kost->name }}">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center rounded-top">
                                    <span class="fw-semibold text-muted">Belum ada foto</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="mb-3">
                            <h5 class="card-title fw-bold mb-1">{{ $kost->name }}</h5>
                            <p class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i>{{ Str::limit($kost->address, 60) }}</p>
                            <p class="fs-5 fw-semibold mb-0 text-primary">Rp {{ number_format($kost->price_per_month, 0, ',', '.') }} <span class="fs-6 text-muted">/bulan</span></p>
                        </div>
                        @if(!empty($kost->facilities))
                            <div class="mb-3">
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach(array_slice($kost->facilities, 0, 3) as $facility)
                                        <span class="badge bg-light text-muted border">{{ $facility }}</span>
                                    @endforeach
                                    @if(count($kost->facilities) > 3)
                                        <span class="badge bg-light text-muted border">+{{ count($kost->facilities) - 3 }}</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted small"><i class="bi bi-person-check me-1"></i>Owner: {{ $kost->owner?->name }}</span>
                                @if(($kost->approved_bookings_count ?? 0) > 0)
                                    <span class="badge bg-info-subtle text-info-emphasis small">
                                        {{ $kost->approved_bookings_count }} disetujui
                                    </span>
                                @endif
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('kosts.show', $kost) }}" class="btn btn-outline-primary w-50">Lihat Detail</a>
                                @auth
                                    @if(auth()->user()->isMahasiswa())
                                        <button type="button"
                                            class="btn btn-primary w-50 booking-modal-trigger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#quickBookingModal"
                                            data-action="{{ route('bookings.store', $kost) }}"
                                            data-name="{{ $kost->name }}">
                                            Booking Cepat
                                        </button>
                                    @else
                                        <a href="{{ route('kosts.show', $kost) }}" class="btn btn-primary w-50">Booking</a>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-primary w-50">Booking Cepat</a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                @include('components.empty', [
                    'title' => 'Belum ada kost yang sesuai filter',
                    'subtitle' => 'Coba ubah kata kunci atau hilangkan beberapa filter untuk melihat pilihan lain.',
                    'action' => view('components.reset_filters_button')
                ])
            </div>
        @endforelse
    </div>

    <div class="mt-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
        <div class="text-muted small">
            Menampilkan {{ $kosts->firstItem() ?? 0 }} - {{ $kosts->lastItem() ?? 0 }} dari {{ $kosts->total() }} kost
        </div>
        {{ $kosts->withQueryString()->links() }}
    </div>

    @auth
    @if(auth()->user()->isMahasiswa())
        <div class="modal fade" id="quickBookingModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" id="quickBookingForm">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="quickBookingTitle">Booking Kost</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Masuk</label>
                                <input type="date" name="move_in_date" class="form-control" required data-range="future">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Telepon</label>
                                <input type="text" name="tenant_phone" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Catatan Tambahan</label>
                                <textarea name="tenant_notes" class="form-control" rows="3" placeholder="Opsional"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Kirim Booking</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    @endauth
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const skeleton = document.getElementById('kostSkeleton');
            const results = document.getElementById('kostResults');
            if (skeleton && results) {
                setTimeout(() => {
                    skeleton.classList.add('d-none');
                    results.classList.remove('d-none');
                }, 350);
            }

            const searchForm = document.getElementById('kostSearchForm');
            const sortSelect = document.getElementById('listSortSelect');
            const perPageSelect = document.getElementById('listPerPageSelect');
            const sortInput = document.getElementById('heroSortInput');
            const perPageInput = document.getElementById('heroPerPageInput');

            if (sortSelect && sortInput && searchForm) {
                sortSelect.addEventListener('change', () => {
                    sortInput.value = sortSelect.value;
                    searchForm.submit();
                });
            }

            if (perPageSelect && perPageInput && searchForm) {
                perPageSelect.addEventListener('change', () => {
                    perPageInput.value = perPageSelect.value;
                    searchForm.submit();
                });
            }

            document.querySelectorAll('.facility-chip').forEach(chip => {
                chip.addEventListener('click', () => {
                    const targetId = chip.getAttribute('data-target');
                    const checkbox = document.getElementById(targetId);
                    if (!checkbox) return;
                    checkbox.checked = !checkbox.checked;
                    chip.classList.toggle('active', checkbox.checked);
                    chip.classList.toggle('btn-outline-primary', !checkbox.checked);
                    chip.classList.toggle('btn-primary', checkbox.checked);
                    chip.classList.toggle('text-white', checkbox.checked);
                });
            });

            const bookingModal = document.getElementById('quickBookingModal');
            if (bookingModal) {
                bookingModal.addEventListener('show.bs.modal', event => {
                    const trigger = event.relatedTarget;
                    const action = trigger.getAttribute('data-action');
                    const name = trigger.getAttribute('data-name');
                    const form = document.getElementById('quickBookingForm');
                    const title = document.getElementById('quickBookingTitle');
                    if (form && action) {
                        form.setAttribute('action', action);
                    }
                    if (title && name) {
                        title.textContent = `Booking ${name}`;
                    }
                });
            }
        });
    </script>
@endpush
