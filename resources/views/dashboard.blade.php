@extends('layouts.tabler')

{{-- Menambahkan FontAwesome CDN karena template default kemungkinan hanya memuat Feather Icons --}}
@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endpush

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
												<h5>Home</h5>
											</div>
											<ul class="breadcrumb">
												<li class="breadcrumb-item"><a href="index.html"><i
															class="feather icon-home"></i></a></li>
												<li class="breadcrumb-item"><a href="#!">Analytics Dashboard</a></li>
											</ul>
										</div>
									</div>
								</div>
							</div>
							<!-- [ breadcrumb ] end -->
							<!-- [ Main Content ] start -->
							<div class="row">

								<!-- product profit start -->
								<div class="col-xl-3 col-md-6">
									<div class="card prod-p-card bg-c-red">
										<div class="card-body">
											<div class="row align-items-center m-b-25">
												<div class="col">
													<h6 class="m-b-5 text-white">Total Vehicle</h6>
													<h3 class="m-b-0 text-white">{{ number_format($totalVehicles) }}</h3>
												</div>
												<div class="col-auto">
													<i class="fas fa-truck text-grey f-24"></i>
												</div>
											</div>
											<p class="m-b-0 text-white">Total armada terdaftar</p>
										</div>
									</div>
								</div>
								<div class="col-xl-3 col-md-6">
									<div class="card prod-p-card bg-c-blue">
										<div class="card-body">
											<div class="row align-items-center m-b-25">
												<div class="col">
													<h6 class="m-b-5 text-white">Total Fuel Consumed</h6>
													<h3 class="m-b-0 text-white">{{ number_format($totalFuelConsumed, 2) }} L</h3>
												</div>
												<div class="col-auto">
													<i class="fas fa-gas-pump text-grey f-24"></i>
												</div>
											</div>
											<p class="m-b-0 text-white">Estimasi konsumsi bulan ini
											</p>
										</div>
									</div>
								</div>
								<div class="col-xl-3 col-md-6">
									<div class="card prod-p-card bg-c-green">
										<div class="card-body">
											<div class="row align-items-center m-b-25">
												<div class="col">
													<h6 class="m-b-5 text-white">Total Fuel Filled</h6>
													<h3 class="m-b-0 text-white">{{ number_format($totalFuelFills, 2) }} L</h3>
												</div>
												<div class="col-auto">
													<i class="fas fa-fill-drip text-grey f-24"></i>
												</div>
											</div>
											<p class="m-b-0 text-white">Total liter pengisian bulan ini</p>
										</div>
									</div>
								</div>
								<div class="col-xl-3 col-md-6">
									<div class="card prod-p-card bg-c-yellow">
										<div class="card-body">
											<div class="row align-items-center m-b-25">
												<div class="col">
													<h6 class="m-b-5 text-white">Total Distance</h6>
													<h3 class="m-b-0 text-white">{{ number_format($totalDistance, 2) }} Km</h3>
												</div>
												<div class="col-auto">
													<i class="fas fa-road text-grey f-24"></i>
												</div>
											</div>
											<p class="m-b-0 text-white">Jarak tempuh bulan ini
											</p>
										</div>
									</div>
								</div>
								<!-- product profit end -->
								<div class="col-md-12 col-xl-4">
									<div class="card card-social">
										<div class="card-block border-bottom">
											<div class="row align-items-center justify-content-center">
												<div class="col-auto">
													<i class="fas fa-tachometer-alt text-c-blue f-36"></i>
												</div>
												<div class="col text-right">
													<h3>{{ number_format($totalDrivingHours, 1) }} <small>Jam</small></h3>
													<h5 class="text-c-blue mb-0">Total Driving <span class="text-muted">Bulan Ini</span></h5>
												</div>
											</div>
										</div>
										<div class="card-block">
											<div class="row align-items-center justify-content-center card-active">
												<div class="col-6">
													<h6 class="text-center m-b-10"><span class="text-muted m-r-5">Avg/Day:</span>
														{{ $totalDrivingHours > 0 ? number_format($totalDrivingHours / now()->day, 1) : 0 }} h
													</h6>
												</div>
												<div class="col-6">
													<h6 class="text-center m-b-10"><span class="text-muted m-r-5">Avg/Unit:</span>
														{{ $totalVehicles > 0 ? number_format($totalDrivingHours / $totalVehicles, 1) : 0 }} h
													</h6>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-6 col-xl-4">
									<div class="card card-social">
										<div class="card-block border-bottom">
											<div class="row align-items-center justify-content-center">
												<div class="col-auto">
													<i class="fas fa-history text-c-info f-36"></i>
												</div>
												<div class="col text-right">
													<h3>{{ number_format($totalIdleHours, 1) }} <small>Jam</small></h3>
													<h5 class="text-c-info mb-0">Total Idle <span class="text-muted">Bulan Ini</span></h5>
												</div>
											</div>
										</div>
										<div class="card-block">
											<div class="row align-items-center justify-content-center card-active">
												<div class="col-6">
													<h6 class="text-center m-b-10"><span class="text-muted m-r-5">Idle Rate:</span>
														{{ $totalDrivingHours > 0 ? number_format(($totalIdleHours / ($totalDrivingHours + $totalIdleHours)) * 100, 1) : 0 }}%
													</h6>
												</div>
												<div class="col-6">
													<h6 class="text-center m-b-10"><span class="text-muted m-r-5">Waste:</span>
														{{ number_format($totalIdleHours * 2.5, 1) }} L
													</h6>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-6 col-xl-4">
									<div class="card card-social">
										<div class="card-block border-bottom">
											<div class="row align-items-center justify-content-center">
												<div class="col-auto">
													<i class="fas fa-route text-c-green f-36"></i>
												</div>
												<div class="col text-right">
													<h3>{{ number_format($totalDistance, 1) }} <small>Km</small></h3>
													<h5 class="text-c-green mb-0">Total Jarak <span class="text-muted">Bulan Ini</span></h5>
												</div>
											</div>
										</div>
										<div class="card-block">
											<div class="row align-items-center justify-content-center card-active">
												<div class="col-6">
													<h6 class="text-center m-b-10">
														<span class="text-muted m-r-5">Avg/Day:</span>
														{{ $totalDistance > 0 ? number_format($totalDistance / now()->day, 1) : 0 }} Km
													</h6>
												</div>
												<div class="col-6">
													<h6 class="text-center m-b-10">
														<span class="text-muted m-r-5">Avg/Unit:</span>
														{{ $totalVehicles > 0 ? number_format($totalDistance / $totalVehicles, 1) : 0 }} Km
													</h6>
												</div>
											</div>
										</div>
									</div>
								</div>
								<!-- Section for Site Visitors Session Log -->
								<div class="col-xl-8 col-md-6">
									<div class="card table-card">
										<div class="card-header">
											<h5>Grafik Fuel Consumed Monthly</h5>
										</div>
										<div class="card-body">
											<canvas id="fuelChart" style="min-height: 320px;"></canvas>
										</div>
									</div>
								</div>
								<!-- end section -->
								<div class="col-md-6 col-xl-4">
									<div class="card user-card">
										<div class="card-header">
											<h5>Top 10 Pengisian BBM - {{ $currentMonthName }}</h5>
										</div>
										<div class="card-body">
											<canvas id="topFuelChart" style="min-height: 300px;"></canvas>
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

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('fuelChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            plugins: [ChartDataLabels],
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Konsumsi BBM (Liter)',
                    data: {!! json_encode($chartData) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        top: 30, // Memberi ruang agar label angka tidak terpotong bingkai atas
                        bottom: 10
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        offset: 4, // Jarak kecil antara angka dan ujung batang
                        formatter: function(value) {
                            return value > 0 ? new Intl.NumberFormat('id-ID').format(value) + ' L' : '';
                        },
                        font: {
                            weight: 'bold'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID').format(context.parsed.y) + ' Liter';
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grace: '15%', // Menambah 15% ruang kosong di atas batang tertinggi
                        grid: {
                            display: false
                        },
                        ticks: {
                            callback: function(value) {
                                return value + ' L';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });

    // Script untuk Horizontal Bar Chart Top 10 Fuel Fills
    document.addEventListener("DOMContentLoaded", function() {
        const topCtx = document.getElementById('topFuelChart').getContext('2d');
        new Chart(topCtx, {
            type: 'bar',
            plugins: [ChartDataLabels],
            data: {
                labels: {!! json_encode($topFuelLabels) !!},
                datasets: [{
                    label: 'Total Isi BBM (Liter)',
                    data: {!! json_encode($topFuelData) !!},
                    backgroundColor: 'rgba(40, 167, 69, 0.8)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y', // Membuat chart menjadi horizontal
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'right',
                        formatter: function(value) {
                            return value.toLocaleString('id-ID') + ' L';
                        },
                        font: {
                            size: 10,
                            weight: 'bold'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Total: ' + context.parsed.x.toLocaleString('id-ID') + ' Liter';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grace: '20%', // Ruang untuk label angka di kanan
                        grid: {
                            display: false
                        },
                        ticks: {
                            callback: function(value) {
                                return value + ' L';
                            }
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endpush