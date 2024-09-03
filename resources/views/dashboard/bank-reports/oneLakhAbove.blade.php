@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2">Reports !</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Reports</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Amount wise Report</li>
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
                        <h4 class="card-title mg-b-10">Amount wise Report</h4>
                    </div>
                    <div class="main-content-body">
                        <div class="row row-sm">
                            <div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5>Filter Reports</h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="complaint-form-ncrp">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="from-date-new">From Date:</label>
                                                        <input type="date" class="form-control" id="from-date-new" value="{{ $today }}" name="from_date">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="to-date-new">To Date:</label>
                                                        <input type="date" class="form-control" id="to-date-new" value="{{ $today }}" name="to_date" onchange="setFromDatencrp()">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="amount-fil">Amount:</label>
                                                        <input type="text" class="form-control" id="amount-fil" name="amount">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="amount-operator">Operator:</label>
                                                        <select name="amount-operator" id="amount-operator" class="form-control">
                                                            <option value="">Select Operator</option>
                                                            <option value="=">=</option>
                                                            <option value=">">></option>
                                                            <option value="<"><</option>
                                                            <option value=">=">>=</option>
                                                            <option value="<="><=</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 text-right">
                                                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body table-new">
                                        <div id="success_message" class="ajax_response" style="display: none;"></div>
                                        <div class="panel panel-primary">
                                            <div class="panel-body tabs-menu-body">
                                                <div class="tab-content">
                                                    <div class="tab-pane active" id="tabNew">
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

@section('scripts')
<script>
    $(document).ready(function () {
        var table = $('#example').DataTable({
            // "pageLength": 10, // Number of rows per page
            // "lengthMenu": [10, 25, 50, 75, 100],
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('aboveReport') }}",
                data: function (d) {
                    d.from_date = $('#from-date-new').val();
                    d.to_date = $('#to-date-new').val();
                    d.amount = $('#amount-fil').val();  // Capture amount filter
                    d.amount_operator = $('#amount-operator').val();  // Capture operator filter
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
                { data: 'modus', name: 'modus' }
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

<style>
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 0.75rem 1.25rem;
        font-weight: bold;
    }

    .card-body {
        padding: 1.25rem;
    }

    .form-group label {
        font-weight: bold;
        font-size: 14px;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        padding: 10px 20px;
        font-size: 14px;
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
