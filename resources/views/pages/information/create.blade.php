@extends('layouts.admin')

@section('title')
    Tambah Informasi
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Tambah Informasi</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.informations.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="title" class="required">Title</label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                                            id="title" placeholder="Masukkan title" name="title" required autofocus>
                                        @error('title')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="description" class="required">Deskripsi</label>
                                        <input type="text" class="form-control @error('description') is-invalid @enderror"
                                            id="description" placeholder="Masukkan deskripsi" name="description" required autofocus>
                                        @error('description')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
