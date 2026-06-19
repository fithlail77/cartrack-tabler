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
                            <h5 class="m-b-10">Report Vehicle Fuel Consumed</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <!-- Filter Kustom -->
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
                                <table class="table table-hover table-striped" id="fuel-consumed-table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Registrasi</th>
                                            <th>Start Period (Jakarta)</th>
                                            <th>Start Liters</th>
                                            <th>End Period (Jakarta)</th>
                                            <th>End Liters</th>
                                            <th>Estimated Fuel Used (L)</th>
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
