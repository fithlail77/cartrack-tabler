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
                                            <h5 class="m-b-10">Manajemen Pengguna</h5>
                                        </div>
                                        <ul class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="/"><i class="feather icon-home"></i></a></li>
                                            <li class="breadcrumb-item"><a href="#!">Settings</a></li>
                                            <li class="breadcrumb-item"><a href="#!">Manajemen Pengguna</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card shadow-sm">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="m-b-0"><i class="feather icon-users mr-2"></i> Daftar Pengguna</h5>
                                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                                            <i class="feather icon-plus mr-1"></i> Tambah User
                                        </a>
                                    </div>
                                    <div class="card-body pt-2 pb-3">

                                        @if(session('success'))
                                            <div class="alert alert-success mt-2 mb-3">{{ session('success') }}</div>
                                        @endif
                                        @if(session('error'))
                                            <div class="alert alert-danger mt-2 mb-3">{{ session('error') }}</div>
                                        @endif

                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped">
                                                <thead>
                                                    <tr>
                                                        <th width="50">No</th>
                                                        <th>Nama</th>
                                                        <th>Email</th>
                                                        <th>Role</th>
                                                        <th width="160">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($users as $user)
                                                        <tr>
                                                            <td>{{ $users->firstItem() + $loop->index }}</td>
                                                            <td class="font-weight-bold">{{ $user->name }}</td>
                                                            <td>{{ $user->email }}</td>
                                                            <td>
                                                                @foreach($user->getRoleNames() as $role)
                                                                    <span class="badge badge-{{ $role === 'admin' ? 'primary' : 'secondary' }}">{{ ucfirst($role) }}</span>
                                                                @endforeach
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary mr-1">
                                                                    <i class="feather icon-edit-2"></i> Edit
                                                                </a>
                                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline"
                                                                      onsubmit="return confirm('Hapus user {{ $user->name }}?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                        <i class="feather icon-trash-2"></i> Hapus
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center py-4 text-muted font-italic">
                                                                Belum ada data pengguna.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="mt-4 d-flex justify-content-end">
                                            {{ $users->links() }}
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
</div>
@endsection
