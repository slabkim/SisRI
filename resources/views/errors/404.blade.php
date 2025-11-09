@extends('layouts.app')

@section('content')
    <div class="text-center py-5">
        <h1 class="display-4 fw-bold">404</h1>
        <p class="lead text-muted">Halaman yang Anda cari tidak ditemukan.</p>
        <a href="{{ route('kosts.index') }}" class="btn btn-primary mt-3">Kembali ke beranda</a>
    </div>
@endsection
