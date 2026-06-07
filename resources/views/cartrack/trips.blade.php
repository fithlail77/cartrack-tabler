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
                                            <h5 class="m-b-10">Trips Vehicle History</h5>
                                        </div>
                                        <ul class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="/"><i class="feather icon-home"></i></a></li>
                                            <li class="breadcrumb-item"><a href="#!">Rest API Cartrack</a></li>
                                            <li class="breadcrumb-item"><a href="#!">Trips Vehicle History</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-header border-bottom-0">
                                        <h5 class="m-b-0"><i class="feather icon-download-cloud mr-2"></i> Data Trip Kenderaan</h5>
                                        
                                        @if(session('success'))
                                            <div class="alert alert-success mt-3 mb-0">{{ session('success') }}</div>
                                        @endif
                                        @if(session('error'))     
                                            <div class="alert alert-danger mt-3 mb-0">{{ session('error') }}</div>
                                        @endif
                                        @if($errors->any())
                                            <div class="alert alert-danger mt-3 mb-0">
                                                <ul class="mb-0">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-body pt-2 pb-3">
                                        <form action="{{ route('trips.sync.process') }}" method="POST">
                                            @csrf
                                            <div class="row align-items-end">
                                                <div class="col-md-4">
                                                    <div class="form-group mb-0">
                                                        <label class="font-weight-bold text-muted small">Start Date & Time (WIB)</label>
                                                        <input type="datetime-local" name="start_timestamp" class="form-control" value="{{ \Carbon\Carbon::now()->startOfDay()->format('Y-m-d\TH:i') }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group mb-0">
                                                        <label class="font-weight-bold text-muted small">End Date & Time (WIB)</label>
                                                        <input type="datetime-local" name="end_timestamp" class="form-control" value="{{ \Carbon\Carbon::now()->endOfDay()->format('Y-m-d\TH:i') }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <button type="submit" class="btn btn-primary w-100 mb-0 d-flex align-items-center justify-content-center" style="height: 43px;" id="syncBtn">
                                                        <span id="btnText">Sync Data API Trips</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-sm-12">
                                <div class="card shadow-sm">
                                    <div class="card-header pb-0 border-bottom-0">
                                        @php
                                            $displayStart = request('filter_start', \Carbon\Carbon::today()->format('Y-m-d'));
                                            $displayEnd = request('filter_end', \Carbon\Carbon::today()->format('Y-m-d'));
                                        @endphp
                                        <h4>Riwayat Trips: {{ \Carbon\Carbon::parse($displayStart)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($displayEnd)->translatedFormat('d M Y') }}</h4>
                                    </div>
                                    <div class="card-body pt-3 table-border-style">
                                        
                                        <form action="{{ route('trips.sync.index') }}" method="GET" class="mb-4 bg-light p-3 rounded">
                                            <div class="row align-items-end">
                                                <div class="col-md-3">
                                                    <div class="form-group mb-0">
                                                        <label class="font-weight-bold small">Filter Dari Tanggal</label>
                                                        <input type="date" name="filter_start" class="form-control" value="{{ $displayStart }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group mb-0">
                                                        <label class="font-weight-bold small">Sampai Tanggal</label>
                                                        <input type="date" name="filter_end" class="form-control" value="{{ $displayEnd }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <button type="submit" class="btn btn-info mb-0 mr-2" style="height: 43px;">
                                                        <i class="feather icon-filter"></i> Tampilkan
                                                    </button>
                                                    @if(request()->has('filter_start'))
                                                        <a href="{{ route('trips.sync.index') }}" class="btn btn-secondary mb-0" style="height: 43px; display: inline-flex; align-items: center;">
                                                            <i class="feather icon-x-circle mr-1"></i> Reset
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </form>

                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped">
                                                <thead>
                                                    <tr>
                                                        <th width="50">No</th>
                                                        <th>Registrasi</th>
                                                        <th>Waktu Mulai</th>
                                                        <th>Waktu Selesai</th>
                                                        <th>Durasi</th>
                                                        <th>Jarak (m)</th>
                                                        <th>Lokasi Mulai</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($trips as $trip)
                                                        <tr>
                                                            <td>{{ $trips->firstItem() + $loop->index }}</td>
                                                            <td class="font-weight-bold">{{ $trip->registration }}</td>
                                                            <td>{{ optional($trip->start_timestamp)->format('d M Y H:i:s') }}</td>
                                                            <td>{{ optional($trip->end_timestamp)->format('d M Y H:i:s') }}</td>
                                                            <td>{{ $trip->trip_duration }}</td>
                                                            <td>{{ number_format($trip->trip_distance, 0, ',', '.') }}</td>
                                                            <td>{{ Str::limit($trip->start_location, 50) }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="7" class="text-center py-4 text-muted font-italic">
                                                                Belum ada data trips. Pilih tanggal dan klik Sync Data API Trips.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        <div class="mt-4 d-flex justify-content-end">
                                            {{ $trips->withQueryString()->links() }}
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
<script>
    function showLoading() {
        document.getElementById('syncBtn').classList.add('cursor-not-allowed', 'opacity-75');
        document.getElementById('loadingIcon').classList.remove('hidden');
        document.getElementById('btnText').innerText = 'Menarik Data...';
        return true;
    }
</script>
@endsection