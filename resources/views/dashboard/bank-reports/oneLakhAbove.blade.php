@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2">Hi, welcome back!</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Reports</a></li>
                    <li class="breadcrumb-item active" aria-current="page">1 Lakh Above Cases</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row row-sm">
        <div class="col-md-12 col-xl-12">
            <div class="card overflow-hidden review-project">
                <div class="card-body">
                    <div class="m-4 d-flex justify-content-between">
                        <div id="alert_ajaxx" style="display:none"></div>
                        @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show w-100" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif
                        <div class="alert alert-success-one alert-dismissible fade show w-100" role="alert" style="display:none">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                    <div class="m-4 d-flex justify-content-between">
                        <h4 class="card-title mg-b-10">1 Lakh Above Cases</h4>
                    </div>
                    <div class="main-content-body">
                        <div class="row row-sm">
                            <div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
                                <div class="card">
                                    <div class="card-body table-new">
                                        <div id="success_message" class="ajax_response" style="display: none;"></div>
                                        <div class="panel panel-primary">
                                            <div class="panel-body tabs-menu-body">
                                                <div class="tab-content">
                                                    <div class="tab-pane active" id="tabNew">
                                                        <form id="complaint-form-ncrp">
                                                            <div class="row">
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <label for="from-date-new">From Date:</label>
                                                                        <input type="date" class="form-control" id="from-date-new"  value="{{ $today }}" name="from_date">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <label for="to-date-new">To Date:</label>
                                                                        <input type="date" class="form-control" id="to-date-new"  value="{{ $today }}" name="to_date" onchange="setFromDatencrp()">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12 text-right">
                                                                    <button type="submit" class="btn btn-primary">Submit</button>
                                                                    <a href="#" class="btn btn-success" id="csvDownload">Download CSV</a>
                                                                    <a href="#" class="btn btn-info" id="excelDownload">Download Excel</a>
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <div class="table-responsive">
                                                            <table id="example" class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap table-striped">
                                                                <thead>
                                                                    <tr>
                                                                        <th>SL No</th>
                                                                        <th>Acknowledgement No</th>
                                                                        <th>District</th>
                                                                        <th>Reported Date & Time</th>
                                                                        <th>Amount Reported</th>
                                                                        <th>TXN Date</th>
                                                                        <th>Lien Amount</th>
                                                                        <th>Amount Lost</th>
                                                                        <th>Amount Pending</th>
                                                                        <th>Pending Bank</th>
                                                                        <th>Modus</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <!-- Data will be dynamically added here by DataTables -->
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- main-content closed -->
                            </div>
                        </div>
                    </div>
                    <!-- container-closed -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        var table = $('#example').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('aboveReport') }}",
                data: function (d) {
                    d.from_date = $('#from-date-new').val();
                    d.to_date = $('#to-date-new').val();
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'acknowledgement_no', name: 'acknowledgement_no' },
                { data: 'district', name: 'district' },
                { data: 'reported_date', name: 'reported_date' },
                // { data: 'total', name: 'total' },
                // { data: 'actual_amount', name: 'actual_amount' },
                // { data: 'actual_amount_lost_on', name: 'actual_amount_lost_on' },
                // { data: 'actual_amount_hold_on', name: 'actual_amount_hold_on' },
                // { data: 'hold_amount_otherthan', name: 'hold_amount_otherthan' },
                // { data: 'total_hold', name: 'total_hold' },
                // { data: 'total_amount_lost_from_eco', name: 'total_amount_lost_from_eco' },
                // { data: 'amount_for_pending_action', name: 'amount_for_pending_action' }
            ],
            order: [[0, 'desc']],
            // Add any additional options you need
        });

        $('#complaint-form-ncrp').on('submit', function (e) {
            e.preventDefault();
            table.draw();
        });
    });
</script>
@endsection
