@extends('layouts.tabler')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
@endpush

@section('content')
<div class="pcoded-main-container">
    <div class="pcoded-wrapper">
        <div class="pcoded-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <h5 class="m-b-10">Report Vehicle Trips</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <form id="filterForm">
                                <div class="row mb-4 align-items-end">
                                    <div class="col-md-3">
                                        <label for="start_date" class="font-weight-bold small">Dari Tanggal</label>
                                        <input type="date" id="start_date" name="start_date" class="form-control" value="{{ \Carbon\Carbon::yesterday()->toDateString() }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="end_date" class="font-weight-bold small">Sampai Tanggal</label>
                                        <input type="date" id="end_date" name="end_date" class="form-control" value="{{ \Carbon\Carbon::yesterday()->toDateString() }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="registration" class="font-weight-bold small">Unit (Registration)</label>
                                        <select id="registration" name="registration" class="form-control select2">
                                            <option value="">-- Semua Unit --</option>
                                            @foreach($vehicles as $v)
                                                <option value="{{ $v->registration }}">{{ $v->registration }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" id="filterBtn" class="btn btn-primary"><i class="feather icon-filter"></i> Filter</button>
                                        <button type="button" id="exportBtn" class="btn btn-success"><i class="feather icon-download"></i> Export Excel</button>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-hover table-striped" id="trips-table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Registrasi</th>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th>Duration (seconds)</th>
                                            <th>Trip Distance (km)</th>
                                            <th>Start Odometer</th>
                                            <th>End Odometer</th>
                                            <th>Max Speed</th>
                                            <th>Idle Time (seconds)</th>
                                            <th>Driver Name</th>
                                            <th>Start Location</th>
                                            <th>End Location</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('js')
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<script>

$(function() {
    var table = $('#trips-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('reports.trips.data') }}",
            error: function (xhr, error, thrown) {
                console.error("DataTable Error: ", xhr.responseText);
                alert("Terjadi kesalahan saat memuat data. Cek Console!");
            },
            data: function (d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
                d.registration = $('#registration').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'registration', name: 'registration'},
            {data: 'start_timestamp', name: 'start_timestamp'},
            {data: 'end_timestamp', name: 'end_timestamp'},
            {data: 'trip_duration_seconds', name: 'trip_duration_seconds'},
            {data: 'trip_distance', name: 'trip_distance'},
            {data: 'start_odometer', name: 'start_odometer'},
            {data: 'end_odometer', name: 'end_odometer'},
            {data: 'max_speed', name: 'max_speed'},
            {data: 'idle_time_seconds', name: 'idle_time_seconds'},
            {data: 'driver_name', name: 'driver_name'},
            {data: 'start_location', name: 'start_location'},
            {data: 'end_location', name: 'end_location'},
        ]
    });

    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        table.draw();
    });

    $('#exportBtn').click(function() {
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        var registration = $('#registration').val();
        
        var url = "{{ route('reports.trips.export') }}?" + 
                  "start_date=" + start_date + 
                  "&end_date=" + end_date + 
                  "&registration=" + registration;
        
        window.location.href = url;
    });
});
</script>
@endpush
@endsection