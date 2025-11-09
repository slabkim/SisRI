<div class="text-center py-5">
    <img src="https://cdn.jsdelivr.net/gh/tabler/tabler-icons/icons/ghost.svg" width="56" class="opacity-50 mb-3" alt="Empty state illustration">
    <h5 class="fw-bold mb-1">{{ $title ?? 'Belum ada data' }}</h5>
    <p class="text-muted mb-3">{{ $subtitle ?? 'Coba ubah filter atau tambahkan data baru.' }}</p>
    @isset($action)
        {{ $action }}
    @endisset
</div>

