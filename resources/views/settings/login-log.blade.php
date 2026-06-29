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
                                            <h5 class="m-b-10">Log Login</h5>
                                        </div>
                                        <ul class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="/"><i class="feather icon-home"></i></a></li>
                                            <li class="breadcrumb-item"><a href="#!">Settings</a></li>
                                            <li class="breadcrumb-item"><a href="#!">Log Login</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card shadow-sm">
                                    <div class="card-header">
                                        <h5 class="m-b-0"><i class="feather icon-shield-check mr-2"></i> Log Autentikasi Pengguna</h5>
                                    </div>
                                    <div class="card-body pt-2 pb-3">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped">
                                                <thead>
                                                    <tr>
                                                        <th width="50">No</th>
                                                        <th>User</th>
                                                        <th>IP Address</th>
                                                        <th>User Agent</th>
                                                        <th>Waktu Login</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($logs as $log)
                                                    <tr>
                                                        <td>{{ $logs->firstItem() + $loop->index }}</td>
                                                        <td class="font-weight-bold">{{ $log->user->name ?? 'N/A' }}</td>
                                                        <td>{{ $log->ip_address }}</td>
                                                        <td class="text-muted">{{ Str::limit($log->user_agent, 70) }}</td>
                                                        <td class="text-muted">{{ $log->login_at->format('d M Y, H:i:s') }}</td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center py-4 text-muted font-italic">
                                                            Belum ada data log login.
                                                        </td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="mt-4 d-flex justify-content-end">
                                            {{ $logs->links() }}
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