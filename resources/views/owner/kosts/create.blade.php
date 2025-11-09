@extends('layouts.app')

@section('content')
    <div class="mb-3">
        <h1 class="h4 fw-bold">Tambah Kost</h1>
        <p class="text-muted mb-0">Lengkapi informasi kost baru Anda.</p>
    </div>

    <form action="{{ route('owner.kosts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('owner.kosts._form', ['kost' => null])
        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ route('owner.kosts.index') }}" class="btn btn-light">Batal</a>
            <button type="submit" class="btn btn-primary kost-submit-button">Simpan</button>
        </div>
    </form>
@endsection
