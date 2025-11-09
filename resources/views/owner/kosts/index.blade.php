@extends('layouts.app')
@php
    use Illuminate\Support\Str;

    $activeFilters = array_filter([
        'search' => $filters['search'] ?? null,
        'price_min' => $filters['price_min'] ?? null,
        'price_max' => $filters['price_max'] ?? null,
        'facilities' => !empty($filters['facilities'] ?? []),
    ]);
    $sort = $filters['sort'] ?? 'updated_desc';
@endphp

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Kelola Kost</h1>
            <p class="text-muted mb-0">Gunakan filter, sortir, dan aksi bulk untuk mempercepat pengelolaan kost.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterKost">
                Filter Kost
                @if($activeFilters)
                    <span class="badge bg-primary-subtle text-primary-emphasis ms-2">{{ count(array_filter($activeFilters)) }}</span>
                @endif
            </button>
            <a href="{{ route('owner.kosts.create') }}" class="btn btn-primary">Tambah Kost</a>
        </div>
    </div>

    <div id="bulkActionBar" class="alert alert-secondary d-none justify-content-between align-items-center flex-column flex-md-row gap-3">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-semibold">Aksi Bulk</span>
            <span id="bulkSelectedCount" class="badge bg-dark-subtle text-dark-emphasis">0</span>
            <small class="text-muted">kost dipilih</small>
        </div>
        <div class="d-flex gap-2">
            <select id="bulkActionSelect" class="form-select form-select-sm">
                <option value="archive">Arsipkan</option>
                <option value="delete">Hapus Permanen</option>
            </select>
            <button id="applyBulkAction" class="btn btn-sm btn-danger" type="button">Jalankan</button>
        </div>
    </div>

    <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-3 mb-3">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="text-muted small text-uppercase">Urutkan</span>
            <select id="sortSelect" class="form-select form-select-sm w-auto">
                <option value="updated_desc" @selected($sort === 'updated_desc')>Terbaru diperbarui</option>
                <option value="price_asc" @selected($sort === 'price_asc')>Harga terendah</option>
                <option value="price_desc" @selected($sort === 'price_desc')>Harga tertinggi</option>
                <option value="name_asc" @selected($sort === 'name_asc')>Nama A-Z</option>
                <option value="popular_desc" @selected($sort === 'popular_desc')>Paling diminati</option>
            </select>
            <span class="text-muted small text-uppercase ms-0 ms-lg-4">Per halaman</span>
            <select id="perPageSelect" class="form-select form-select-sm w-auto">
                @foreach([9, 18, 30] as $option)
                    <option value="{{ $option }}" @selected((int)($filters['per_page'] ?? 9) === $option)>{{ $option }}</option>
                @endforeach
            </select>
        </div>
        @if($activeFilters)
            <div class="d-flex flex-wrap gap-2 small">
                <span class="text-muted">Filter aktif:</span>
                @foreach(($filters['facilities'] ?? []) as $facility)
                    <span class="badge bg-primary-subtle text-primary-emphasis">{{ $facility }}</span>
                @endforeach
                @if($filters['price_min'] ?? false)
                    <span class="badge bg-primary-subtle text-primary-emphasis">Min Rp {{ number_format($filters['price_min'], 0, ',', '.') }}</span>
                @endif
                @if($filters['price_max'] ?? false)
                    <span class="badge bg-primary-subtle text-primary-emphasis">Max Rp {{ number_format($filters['price_max'], 0, ',', '.') }}</span>
                @endif
                @if($filters['search'] ?? false)
                    <span class="badge bg-primary-subtle text-primary-emphasis">Cari: {{ $filters['search'] }}</span>
                @endif
                <a href="{{ route('owner.kosts.index') }}" class="badge bg-light text-decoration-none">Reset</a>
            </div>
        @endif
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="filterKost">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Filter Kost</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form id="kostFilterForm" method="GET" action="{{ route('owner.kosts.index') }}">
                <input type="hidden" name="sort" id="filterSortInput" value="{{ $sort }}">
                <input type="hidden" name="per_page" id="filterPerPageInput" value="{{ $filters['per_page'] ?? 9 }}">
                <div class="mb-3">
                    <label class="form-label">Cari nama/alamat</label>
                    <input type="text" name="search" class="form-control" value="{{ $filters['search'] ?? '' }}" placeholder="Contoh: dekat kampus">
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Harga minimal</label>
                        <input type="number" name="price_min" class="form-control" value="{{ $filters['price_min'] ?? '' }}" min="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Harga maksimal</label>
                        <input type="number" name="price_max" class="form-control" value="{{ $filters['price_max'] ?? '' }}" min="0">
                    </div>
                </div>
                <div class="mt-3">
                    <label class="form-label d-block">Fasilitas</label>
                    <div class="row">
                        @foreach ($facilityOptions as $facility)
                            <div class="col-sm-6">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="facilities[]" value="{{ $facility }}"
                                        @checked(in_array($facility, $filters['facilities'] ?? [])) id="facility-{{ Str::slug($facility) }}">
                                    <label class="form-check-label" for="facility-{{ Str::slug($facility) }}">{{ $facility }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary w-100">Terapkan</button>
                    <a href="{{ route('owner.kosts.index') }}" class="btn btn-light w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    @if($kosts->isEmpty())
        @include('components.empty', [
            'title' => 'Belum ada kost',
            'subtitle' => 'Mulai tambahkan kost pertama Anda.',
            'action' => view('components.add_kost_button')
        ])
    @else
        <div class="row g-3">
            @foreach($kosts as $kost)
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card h-100 shadow-sm border-0 position-relative kost-card" data-kost-id="{{ $kost->id }}">
                        <div class="position-absolute top-0 start-0 p-2">
                            <input class="form-check-input kost-select" type="checkbox" value="{{ $kost->id }}" aria-label="Pilih kost {{ $kost->name }}">
                        </div>
                        @if($kost->photo_url)
                            <img src="{{ $kost->photo_url }}" class="card-img-top" alt="{{ $kost->name }}">
                        @else
                            <div class="bg-light text-center py-5 fw-semibold text-muted">Belum ada foto</div>
                        @endif
                        <div class="card-body d-flex flex-column">
                            <div class="mb-3">
                                <h5 class="card-title fw-bold mb-1">{{ $kost->name }}</h5>
                                <p class="text-muted small mb-1">{{ $kost->address }}</p>
                                <p class="fw-semibold mb-1">Rp {{ number_format($kost->price_per_month, 0, ',', '.') }}/bulan</p>
                                <p class="text-muted small mb-0">Diperbarui {{ $kost->updated_at->diffForHumans() }}</p>
                            </div>
                            @if(!empty($kost->facilities))
                                <div class="mb-3">
                                    <span class="text-muted small text-uppercase">Fasilitas unggulan</span>
                                    <div class="d-flex flex-wrap gap-1 mt-1">
                                        @foreach(array_slice($kost->facilities, 0, 3) as $facility)
                                            <span class="badge bg-light text-muted">{{ $facility }}</span>
                                        @endforeach
                                        @if(count($kost->facilities) > 3)
                                            <span class="badge bg-light text-muted">+{{ count($kost->facilities) - 3 }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge bg-warning-subtle text-warning-emphasis text-uppercase small">
                                        {{ $kost->pending_bookings_count }} pending
                                    </span>
                                    <span class="badge bg-success-subtle text-success-emphasis text-uppercase small">
                                        {{ $kost->approved_bookings_count }} disetujui
                                    </span>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('owner.kosts.edit', $kost) }}" class="btn btn-sm btn-outline-primary w-100">Edit</a>
                                    <form action="{{ route('owner.kosts.destroy', $kost) }}" method="POST" onsubmit="return confirm('Hapus kost ini?')" class="w-100">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger w-100" type="submit">Hapus</button>
                                    </form>
                                </div>
                                <a href="{{ route('kosts.show', $kost) }}" class="btn btn-sm btn-link mt-2 px-0 text-decoration-none">Lihat halaman publik</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="text-muted small">
                Menampilkan {{ $kosts->firstItem() ?? 0 }} - {{ $kosts->lastItem() ?? 0 }} dari {{ $kosts->total() }} kost
            </div>
            {{ $kosts->withQueryString()->links() }}
        </div>
    @endif

    <form id="bulkActionForm" action="{{ route('owner.kosts.bulk') }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="action" id="bulkActionInput">
        <div id="bulkIdsContainer"></div>
    </form>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sortSelect = document.getElementById('sortSelect');
            const filterForm = document.getElementById('kostFilterForm');
            const filterSortInput = document.getElementById('filterSortInput');

            if (sortSelect && filterForm && filterSortInput) {
                sortSelect.addEventListener('change', () => {
                    filterSortInput.value = sortSelect.value;
                    filterForm.submit();
                });
            }

            const bulkBar = document.getElementById('bulkActionBar');
            const bulkCount = document.getElementById('bulkSelectedCount');
            const bulkSelectInputs = document.querySelectorAll('.kost-select');
            const applyBulkButton = document.getElementById('applyBulkAction');
            const bulkActionSelect = document.getElementById('bulkActionSelect');
            const bulkForm = document.getElementById('bulkActionForm');
            const bulkActionInput = document.getElementById('bulkActionInput');
            const bulkIdsContainer = document.getElementById('bulkIdsContainer');
            const perPageSelect = document.getElementById('perPageSelect');

            function updateBulkBar() {
                const selected = Array.from(bulkSelectInputs).filter(input => input.checked);
                const count = selected.length;
                bulkCount.textContent = count.toString();
                bulkBar.classList.toggle('d-none', count === 0);
            }

            bulkSelectInputs.forEach(input => {
                input.addEventListener('change', updateBulkBar);
            });

            if (applyBulkButton && bulkForm) {
                applyBulkButton.addEventListener('click', () => {
                    const selected = Array.from(bulkSelectInputs).filter(input => input.checked);
                    if (selected.length === 0) {
                        alert('Pilih minimal satu kost terlebih dahulu.');
                        return;
                    }

                    const action = bulkActionSelect.value;
                    const confirmMessage = action === 'archive'
                        ? 'Arsipkan kost terpilih?'
                        : 'Hapus permanen kost terpilih? Tindakan ini tidak dapat dibatalkan.';

                    if (!confirm(confirmMessage)) {
                        return;
                    }

                    bulkIdsContainer.innerHTML = '';
                    selected.forEach(input => {
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = 'ids[]';
                        hidden.value = input.value;
                        bulkIdsContainer.appendChild(hidden);
                    });

                    bulkActionInput.value = action;
                    bulkForm.submit();
                });
            }

            if (perPageSelect && filterForm) {
                perPageSelect.addEventListener('change', () => {
                    let perPageInput = document.getElementById('filterPerPageInput');
                    if (!perPageInput) {
                        perPageInput = document.createElement('input');
                        perPageInput.type = 'hidden';
                        perPageInput.name = 'per_page';
                        filterForm.appendChild(perPageInput);
                    }
                    perPageInput.value = perPageSelect.value;
                    filterForm.submit();
                });
            }
        });
    </script>
@endpush
