@extends('layouts.app')
@php
    $exportQuery = array_filter($filters ?? []);
    $highlightId = session('highlight_booking');
    $perPage = $filters['per_page'] ?? 10;
@endphp

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-column flex-md-row gap-3 mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1">{{ $mode === 'owner' ? 'Booking Kost Anda' : 'Booking Saya' }}</h1>
            <p class="text-muted mb-0">Pantau status booking secara real-time.</p>
        </div>
        @if($mode === 'owner')
            <div class="d-flex gap-2">
                <a href="{{ route('owner.bookings.export', $exportQuery) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-filetype-csv me-1"></i>Export CSV
                </a>
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="offcanvas" data-bs-target="#bookingFilterOffcanvas">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
            </div>
        @endif
    </div>

    @php
        $perPageAction = $mode === 'owner' ? route('owner.bookings.index') : route('bookings.index');
    @endphp

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div class="text-muted small">
            @if($bookings->total())
                Menampilkan {{ $bookings->firstItem() }} - {{ $bookings->lastItem() }} dari {{ $bookings->total() }} booking
            @else
                Tidak ada booking untuk ditampilkan
            @endif
        </div>
        <form id="perPageForm" method="GET" action="{{ $perPageAction }}" class="d-flex align-items-center gap-2">
            @foreach($filters as $key => $value)
                @continue($key === 'per_page' || $value === null || $value === '')
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
            <label for="perPageSelect" class="text-muted small text-uppercase mb-0">Per halaman</label>
            <select id="perPageSelect" name="per_page" class="form-select form-select-sm w-auto">
                @foreach([10, 20, 50] as $option)
                    <option value="{{ $option }}" @selected((int) $perPage === $option)>{{ $option }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @if($mode === 'owner')
        <div class="offcanvas offcanvas-end" tabindex="-1" id="bookingFilterOffcanvas">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title">Filter Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body">
                <form id="bookingFilterForm" method="GET" action="{{ route('owner.bookings.index') }}">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua</option>
                            @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rentang tanggal masuk</label>
                        <div class="input-group">
                            <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}" id="dateFromInput" data-range="future">
                            <span class="input-group-text">s/d</span>
                            <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}" id="dateToInput" data-range="future">
                        </div>
                        <div class="d-flex gap-2 mt-2">
                            <button type="button" class="btn btn-sm btn-outline-primary quick-range" data-range="7">7 hari</button>
                            <button type="button" class="btn btn-sm btn-outline-primary quick-range" data-range="30">30 hari</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary quick-range" data-range="all">Semua</button>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary w-100" type="submit">Terapkan</button>
                        <a href="{{ route('owner.bookings.index') }}" class="btn btn-light w-100">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kost</th>
                        @if($mode === 'owner')
                            <th>Mahasiswa</th>
                            <th>Telepon</th>
                        @else
                            <th>Owner</th>
                        @endif
                        <th>Tanggal Masuk</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        <tr data-booking-row="{{ $booking->id }}" @class(['row-highlight' => (int) $highlightId === $booking->id])>
                            <td>
                                <div class="fw-semibold">{{ $booking->kost->name }}</div>
                                <div class="text-muted small">{{ $booking->kost->address }}</div>
                            </td>
                            @if($mode === 'owner')
                                <td>
                                    <div class="fw-semibold">{{ $booking->tenant->name }}</div>
                                    <div class="text-muted small">{{ $booking->tenant->email }}</div>
                                </td>
                                <td>{{ $booking->tenant_phone }}</td>
                            @else
                                <td>{{ $booking->owner->name }}</td>
                            @endif
                            <td>{{ $booking->move_in_date->format('d M Y') }}</td>
                            <td>
                                @php
                                    $badgeMap = [
                                        'pending' => 'bg-warning text-dark',
                                        'approved' => 'bg-success',
                                        'rejected' => 'bg-danger',
                                    ];
                                    $badgeClass = $badgeMap[$booking->status] ?? 'bg-secondary';
                                @endphp
                                <span class="badge rounded-pill {{ $badgeClass }}">
                                    {{ strtoupper($booking->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                @php
                                    $detailPayload = [
                                        'kost' => $booking->kost->name,
                                        'address' => $booking->kost->address,
                                        'user' => $mode === 'owner' ? $booking->tenant->name : $booking->owner->name,
                                        'email' => $mode === 'owner' ? $booking->tenant->email : $booking->owner->email,
                                        'phone' => $booking->tenant_phone,
                                        'move_in_date' => $booking->move_in_date->format('d M Y'),
                                        'tenant_notes' => $booking->tenant_notes,
                                        'owner_notes' => $booking->owner_notes,
                                        'status' => strtoupper($booking->status),
                                    ];
                                @endphp
                                <button type="button"
                                    class="btn btn-sm btn-outline-secondary me-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#bookingDetailModal"
                                    data-booking='@json($detailPayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)'>
                                    Detail
                                </button>
                                @if($mode === 'owner' && $booking->status === \App\Models\Booking::STATUS_PENDING)
                                    <button type="button"
                                        class="btn btn-sm btn-success me-1 booking-action-trigger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#bookingActionModal"
                                        data-action="approve"
                                        data-url="{{ route('owner.bookings.status', $booking) }}"
                                        data-name="{{ $booking->tenant->name }}">
                                        Approve
                                    </button>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-danger booking-action-trigger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#bookingActionModal"
                                        data-action="reject"
                                        data-url="{{ route('owner.bookings.status', $booking) }}"
                                        data-name="{{ $booking->tenant->name }}">
                                        Reject
                                    </button>
                                @endif
                                @if($mode === 'tenant' && $booking->tenant_notes)
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#notes-{{ $booking->id }}">Catatan</button>
                                @endif
                            </td>
                        </tr>
                        @if($mode === 'tenant' && $booking->tenant_notes)
                            <tr class="collapse" id="notes-{{ $booking->id }}">
                                <td colspan="6" class="text-muted">
                                    Catatan Anda: {{ $booking->tenant_notes }}
                                </td>
                            </tr>
                        @endif
                        @if($mode === 'tenant' && $booking->owner_notes)
                            <tr>
                                <td colspan="6" class="text-muted small">
                                    Catatan dari pemilik: {{ $booking->owner_notes }}
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="6">
                                @include('components.empty', [
                                    'title' => 'Belum ada booking',
                                    'subtitle' => 'Booking baru akan tampil di sini setelah diajukan.',
                                ])
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
        <div class="text-muted small">
            Menampilkan {{ $bookings->firstItem() ?? 0 }} - {{ $bookings->lastItem() ?? 0 }} dari {{ $bookings->total() }} booking
        </div>
        {{ $bookings->withQueryString()->links() }}
    </div>

    <div class="modal fade" id="bookingDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Kost</dt>
                        <dd class="col-sm-8" id="detailKost">-</dd>
                        <dt class="col-sm-4">Alamat</dt>
                        <dd class="col-sm-8" id="detailAddress">-</dd>
                        <dt class="col-sm-4">Pengguna</dt>
                        <dd class="col-sm-8" id="detailUser">-</dd>
                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8" id="detailEmail">-</dd>
                        <dt class="col-sm-4">Telepon</dt>
                        <dd class="col-sm-8" id="detailPhone">-</dd>
                        <dt class="col-sm-4">Tanggal Masuk</dt>
                        <dd class="col-sm-8" id="detailDate">-</dd>
                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8" id="detailStatus">-</dd>
                        <dt class="col-sm-4">Catatan Tenant</dt>
                        <dd class="col-sm-8" id="detailTenantNotes">Tidak ada catatan.</dd>
                        <dt class="col-sm-4">Catatan Owner</dt>
                        <dd class="col-sm-8" id="detailOwnerNotes">Belum ada catatan.</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    @if($mode === 'owner')
        <div class="modal fade" id="bookingActionModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" id="bookingActionForm">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="bookingActionTitle">Konfirmasi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="action" id="bookingActionInput">
                            <p id="bookingActionMessage" class="mb-3"></p>
                            <div class="mb-3">
                                <label class="form-label">Catatan untuk tenant (opsional)</label>
                                <textarea name="owner_note" class="form-control" rows="3" placeholder="Contoh: Mohon datang membawa bukti pembayaran DP."></textarea>
                                <div class="form-text">Pesan ini akan terlihat oleh tenant pada riwayat booking mereka.</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Kirim</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const detailModal = document.getElementById('bookingDetailModal');
            const perPageSelect = document.getElementById('perPageSelect');
            const perPageForm = document.getElementById('perPageForm');

            if (perPageSelect && perPageForm) {
                perPageSelect.addEventListener('change', () => perPageForm.submit());
            }

            const highlightedRow = document.querySelector('[data-booking-row].row-highlight');
            if (highlightedRow) {
                highlightedRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            if (detailModal) {
                detailModal.addEventListener('show.bs.modal', event => {
                    const trigger = event.relatedTarget;
                    if (!trigger) return;
                    const data = trigger.getAttribute('data-booking');
                    if (!data) return;
                    const parsed = JSON.parse(data);
                    detailModal.querySelector('#detailKost').textContent = parsed.kost ?? '-';
                    detailModal.querySelector('#detailAddress').textContent = parsed.address ?? '-';
                    detailModal.querySelector('#detailUser').textContent = parsed.user ?? '-';
                    detailModal.querySelector('#detailEmail').textContent = parsed.email ?? '-';
                    detailModal.querySelector('#detailPhone').textContent = parsed.phone ?? '-';
                    detailModal.querySelector('#detailDate').textContent = parsed.move_in_date ?? '-';
                    detailModal.querySelector('#detailStatus').textContent = parsed.status ?? '-';
                    detailModal.querySelector('#detailTenantNotes').textContent = parsed.tenant_notes ?? 'Tidak ada catatan.';
                    detailModal.querySelector('#detailOwnerNotes').textContent = parsed.owner_notes ?? 'Belum ada catatan.';
                });
            }

            const actionModal = document.getElementById('bookingActionModal');
            if (actionModal) {
                actionModal.addEventListener('show.bs.modal', event => {
                    const trigger = event.relatedTarget;
                    const action = trigger.getAttribute('data-action');
                    const url = trigger.getAttribute('data-url');
                    const name = trigger.getAttribute('data-name');

                    document.getElementById('bookingActionTitle').textContent = action === 'approve' ? 'Setujui Booking' : 'Tolak Booking';
                    document.getElementById('bookingActionMessage').textContent = action === 'approve'
                        ? `Setujui permintaan booking dari ${name}?`
                        : `Tolak permintaan booking dari ${name}?`;
                    document.getElementById('bookingActionInput').value = action;
                    document.getElementById('bookingActionForm').setAttribute('action', url);
                });
            }

            document.querySelectorAll('.quick-range').forEach(button => {
                button.addEventListener('click', () => {
                    const range = button.getAttribute('data-range');
                    const dateFrom = document.getElementById('dateFromInput');
                    const dateTo = document.getElementById('dateToInput');
                    if (!dateFrom || !dateTo) {
                        return;
                    }
                    const today = new Date();

                    if (range === 'all') {
                        dateFrom.value = '';
                        dateTo.value = '';
                        return;
                    }

                    const start = new Date();
                    start.setDate(today.getDate() - parseInt(range, 10) + 1);

                    dateFrom.value = start.toISOString().slice(0, 10);
                    dateTo.value = today.toISOString().slice(0, 10);
                });
            });
        });
    </script>
@endpush
