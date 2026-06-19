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
											<li class="breadcrumb-item"><a href="#!">Vehicle List</a></li>
										</ul>
									</div>
								</div>
							</div>
						</div>
						<!-- [ breadcrumb ] end -->
						<!-- [ Main Content ] start -->
						<div class="row">
							<!-- [ badge ] start -->
							<div class="col-sm-12">
								<div class="card">
									<div class="card-header">
										<h5>Sinkron Data Vehicle:</h5>
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
                                        <form action="{{ route('vehicles.sync') }}" method="POST" class="form-inline">
                                            @csrf
                                            <div class="input-group">
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-primary">
                                                        🔄 Tarik Data Vehicle
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
								</div>
							</div>
							<!-- [ badge ] end -->
						</div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Data Vehicle List</h4>
                                    </div>
                                    <div class="card-body table-border-style">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th width="50">No</th>
                                                        <th>Vehicle ID</th>
                                                        <th>Registrasi</th>
                                                        <th>Nama Kenderaan</th>
                                                        <th>Merek</th>
                                                        <th>Tipe</th>
                                                        <th>Tahun</th>
                                                        <th>Kelompok</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($vehicles as $vehicle)
                                                        <tr>
                                                            <td>{{ $vehicles->firstItem() + $loop->index}}</td>
                                                            <td>{{ $vehicle->vehicle_id }}</td>
                                                            <td>{{ $vehicle->registration ?? '-' }}</td>
                                                            <td>{{ $vehicle->vehicle_name ?? '-' }}</td>
															<td>{{ $vehicle->manufacturer ?? '-' }}</td>
															<td>{{ $vehicle->vehicle_type ?? '-' }}</td>
															<td>{{ $vehicle->model_year ?? '-' }}</td>
                                                            <td>{{ $vehicle->client_vehicle_description ?? '-' }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="8" class="px-4 py-8 text-center text-gray-400 italic bg-gray-200/30">
                                                                Belum ada data kendaraan. Silakan tarik data API.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
										<div class="mt-4 d-flex justify-content-end">
            								{{ $vehicles->links() }}
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