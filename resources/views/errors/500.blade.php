@extends('layouts.app')

@section('content')
    <div class="text-center py-5">
        <h1 class="display-4 fw-bold">500</h1>
        <p class="lead text-muted">Waduh, terjadi kesalahan pada sistem. Tim kami sudah diberi tahu.</p>
        <a href="{{ route('kosts.index') }}" class="btn btn-primary mt-3">Kembali ke beranda</a>
    </div>
@endsection
