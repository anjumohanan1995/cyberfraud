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
                                                                        <input type="date" class="form-control" id="from-date-new" value="{{ $today }}" name="from_date">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <label for="to-date-new">To Date:</label>
                                                                        <input type="date" class="form-control" id="to-date-new" value="{{ $today }}" name="to_date" onchange="setFromDatencrp()">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2 fil-btn">
                                                            <div class="form-group">
                                                                <button type="submit" class="btn btn-primary">Submit</button>
                                                            </div>
                                                                </div>
                                                            </div>
                                                            {{-- <div class="row">
                                                                <div class="col-md-12  text-right">
                                                                </div>
                                                            </div> --}}
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
                                                                        <th>Transaction Date</th>
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
<style>
    .fil-btn{
    padding-top: 30px;
}
    .csv-btn {
    color: #fff!important;
    background-color: #28a745!important;
    border-color: #28a745!important;
}
.excel-btn {
    color: #fff!important;
    background-color: #17a2b8!important;
    border-color: #17a2b8!important;
}
</style>
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
                },
                error: function(xhr, error, thrown) {
                    let response = xhr.responseJSON;
                    let errorMessage = response && response.error ? response.error : 'An error occurred. Please try again.';
                    $('#alert_ajaxx').html('<div class="alert alert-danger">' + errorMessage + '</div>').show();
                }
            },
            dom: 'Bfrtip',
 buttons: [
    { extend: 'csv', className: 'csv-btn', text: 'Download CSV' },
    { extend: 'excel', className: 'excel-btn', text: 'Download Excel' },
 ],
            columns: [
                {
                    "data": null, "render": function (data, type, full, meta) {
                        return meta.row + 1;
                    }
                },
                { data: 'acknowledgement_no', name: 'acknowledgement_no' },
                { data: 'district', name: 'district' },
                { data: 'reported_date', name: 'reported_date' },
                { data: 'total_amount', name: 'total_amount' },
                { data: 'transaction_period', name: 'transaction_period' },
                { data: 'lien_amount', name: 'lien_amount' },
                { data: 'amount_lost', name: 'amount_lost' },
                { data: 'amount_pending', name: 'amount_pending' },
                { data: 'pending_banks', name: 'pending_banks' },
                // { data: 'modus', name: 'modus' }
                // Additional columns can be added here
            ],
            order: [[0, 'desc']],
        });

        $('#complaint-form-ncrp').on('submit', function (e) {
            e.preventDefault();
            $('#alert_ajaxx').hide();  // Hide any previous error messages
            table.draw();
        });
    });
</script>
@endsection
