@extends('layouts.app')

@section('content')
    <div class="mb-3">
        <h1 class="h4 fw-bold">Edit Kost</h1>
        <p class="text-muted mb-0">{{ $kost->name }}</p>
    </div>

    <form action="{{ route('owner.kosts.update', $kost) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('owner.kosts._form', ['kost' => $kost])
        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ route('owner.kosts.index') }}" class="btn btn-light">Batal</a>
            <button type="submit" class="btn btn-primary kost-submit-button">Perbarui</button>
        </div>
    </form>
@endsection
