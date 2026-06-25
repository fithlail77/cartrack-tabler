@extends('layouts.tabler')

@section('content')
<div class="pcoded-main-container">
	<div class="pcoded-wrapper">
		<div class="pcoded-content">
			<div class="pcoded-inner-content">
				<div class="main-body">
					<div class="page-wrapper">
						<!-- [ breadcrumb ] start -->
						<div class="page-header">
							<div class="page-block">
								<div class="row align-items-center">
									<div class="col-md-12">
										<div class="page-header-title">
											<h5 class="m-b-10">API Cartrack</h5>
										</div>
										<ul class="breadcrumb">
											<li class="breadcrumb-item"><a href="index.html"><i class="feather icon-home"></i></a></li>
											<li class="breadcrumb-item"><a href="#!">Rest API Cartrack</a></li>
											<li class="breadcrumb-item"><a href="#!">Vehicle Activities</a></li>
										</ul>
									</div>
								</div>
							</div>
						</div>
						<!-- [ breadcrumb ] end -->
						<!-- [ Main Content ] start -->
						<!--<div class="row">
							<div class="col-sm-12">
								<div class="card">
									<div class="card-header">
										<h5>Tarik & Sinkronkan Data API Tanggal:</h5>
                                        @if(session('success'))
                                            <p class="text-success mb-1">
                                                {{ session('success') }}
                                            </p>
                                        @endif
                                        
                                        @if(session('error'))     
                                            <p class="text-danger mb-1">
                                                {{ session('error') }}
                                            </p>
                                        @endif
                                        
                                        @if ($errors->any())
                                            <p class="text-danger mb-1">
                                                <ul>
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </p>
                                        @endif
									</div>
                                    <div class="card-body">
                                        <form action="{{ route('cartrack.activity.sync') }}" method="POST" class="form-inline">
                                            @csrf
                                            <label class="sr-only">Tarik & Sinkronkan Data API Tanggal:</label>
                                            <div class="input-group">
                                                <input type="date" name="sync_date" value="{{ $selectedDate }}" class="form-control" >
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-primary">
                                                        ⚡ Jalankan_Sync
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
								</div>
							</div>
						</div> -->
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card">
									<div class="card-header">
										<h5>Tampilkan Data Database Tanggal:</h5>
									</div>
                                    <div class="card-body">
                                        <form action="{{ route('cartrack.activity.index') }}" method="GET" class="form-inline">
                                            <div class="input-group">
                                                <input type="date" name="date" value="{{ $selectedDate }}" class="form-control">
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-info">
                                                        <i class="feather icon-search mr-1"></i>
                                                        Cari Data
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div> 
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Data Aktivitas: {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}</h4>
                                    </div>
                                    <div class="card-body table-border-style">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th width="50">No</th>
                                                        <th>Vehicle ID</th>
                                                        <th>No. Registrasi</th>
                                                        <th>No. Rangka (Chassis)</th>
                                                        <th>Ignition ON / OFF</th>
                                                        <th>Driving / Idle (Detik)</th>
                                                        <th>Jam Kerja / Istirahat</th>
                                                        <th>Pengemudi (Driver)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($activities as $item)
                                                        <tr>
                                                            <td>{{ $activities->firstItem() + $loop->index }}</td>
                                                            <td>{{ $item->vehicle_id }}</td>
                                                            <td>{{ $item->registration }}</td>
                                                            <td>{{ $item->chassis_number ?? '-' }}</td>
                                                            <td>
                                                                <span class="text-success font-weight-bold">ON: </span>{{ $item->first_ignition_on ?? '-' }} <br>
                                                                <span class="text-danger font-weight-bold">OFF: </span>{{ $item->last_ignition_off ?? '-' }}
                                                            </td>
                                                            <td>
                                                                🚗 {{ number_format($item->driving_time_seconds) }} s <br>
                                                                ⏱️ {{ number_format($item->idle_time_seconds) }} s
                                                            </td>
                                                            <td>
                                                                🛠️ Kerja: <b>{{ $item->total_working_hours ?? '00:00' }}</b> <br>
                                                                💤 Istirahat: <b>{{ $item->total_break_hours ?? '00:00' }}</b> (Trimmed: {{ $item->total_break_time_trimmed ?? '00:00' }})
                                                            </td>
                                                            <td>
                                                                @if($item->driver_id)
                                                                    <span>{{ $item->first_name }} {{ $item->last_name }}</span><br>
                                                                    <span>ID: {{ $item->driver_id }}</span>
                                                                @else
                                                                    <span>Tidak ada driver</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="8" class="px-4 py-8 text-center text-gray-400 italic bg-gray-200/30">
                                                                Tidak ada data di database untuk tanggal ini. Silakan pilih tanggal pada form Filter Di Atas lalu klik tombol "Jalankan Sync" untuk mengambil data dari Cartrack.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="mt-4 d-flex justify-content-end">
                                            {{ $activities->links() }}
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
						<!-- [ Main Content ] end -->
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection