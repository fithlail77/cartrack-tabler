@extends('layouts.tabler')

@section('content')
<div class="pcoded-main-container">
    <div class="pcoded-wrapper">
        <div class="pcoded-content">
            <div class="pcoded-inner-content">
                <div class="main-body">
                    <div class="page-wrapper">

                        <div class="page-header">
                            <div class="page-block">
                                <div class="row align-items-center">
                                    <div class="col-md-12">
                                        <div class="page-header-title">
                                            <h5 class="m-b-10">Edit Pengguna</h5>
                                        </div>
                                        <ul class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="/"><i class="feather icon-home"></i></a></li>
                                            <li class="breadcrumb-item"><a href="#!">Settings</a></li>
                                            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Manajemen Pengguna</a></li>
                                            <li class="breadcrumb-item"><a href="#!">Edit User</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 col-md-8 offset-md-2">
                                <div class="card shadow-sm">
                                    <div class="card-header border-bottom-0">
                                        <h5 class="m-b-0"><i class="feather icon-edit mr-2"></i> Edit Pengguna: {{ $user->name }}</h5>
                                    </div>
                                    <div class="card-body pt-2">

                                        @if($errors->any())
                                            <div class="alert alert-danger">
                                                <ul class="mb-0">
                                                    @foreach($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        <form action="{{ route('admin.users.update', $user) }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <div class="form-group">
                                                <label class="font-weight-bold">Nama <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                                       value="{{ old('name', $user->name) }}" placeholder="Nama lengkap" required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label class="font-weight-bold">Email <span class="text-danger">*</span></label>
                                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                                       value="{{ old('email', $user->email) }}" placeholder="contoh@email.com" required>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label class="font-weight-bold">Password Baru <small class="text-muted">(Kosongkan jika tidak ingin mengubah)</small></label>
                                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                                       placeholder="Minimal 8 karakter">
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label class="font-weight-bold">Konfirmasi Password Baru</label>
                                                <input type="password" name="password_confirmation" class="form-control"
                                                       placeholder="Ulangi password baru">
                                            </div>

                                            <div class="form-group">
                                                <label class="font-weight-bold">Role</label>
                                                <select name="role" class="form-control @error('role') is-invalid @enderror">
                                                    <option value="">-- Pilih Role --</option>
                                                    @foreach($roles as $role)
                                                        <option value="{{ $role->name }}"
                                                            {{ old('role', $userRole) === $role->name ? 'selected' : '' }}>
                                                            {{ ucfirst($role->name) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('role')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="d-flex justify-content-between mt-4">
                                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                                    <i class="feather icon-arrow-left mr-1"></i> Kembali
                                                </a>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="feather icon-save mr-1"></i> Perbarui
                                                </button>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
