@extends('layouts.app')

@section('content')
    <div class="text-center py-5">
        <h1 class="display-4 fw-bold">419</h1>
        <p class="lead text-muted">Sesi Anda berakhir. Silakan muat ulang halaman dan coba lagi.</p>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary mt-3">Muat Ulang</a>
    </div>
@endsection
