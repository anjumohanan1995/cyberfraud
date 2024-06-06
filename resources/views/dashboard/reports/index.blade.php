@extends('layouts.app')

@section('content')
<style>
    .tabs-menu1 ul li a {
        padding: 10px 20px 11px 20px;
        display: block;
        color: #282f53;
        text-decoration: none;
    }
    .tabs-menu1 ul li a.active {
        border-bottom: 3px solid #3858f9;
        color: #3858f9; /* Optional: Change color when active */
    }
</style>
<link rel="stylesheet" href="path_to_bootstrap_css">
<link rel="stylesheet" href="path_to_font_awesome">


    <!-- container -->
    <div class="container-fluid">
        <!-- breadcrumb -->
        <div class="breadcrumb-header justify-content-between">
            <div>
                <h4 class="content-title mb-2">Hi, welcome back!</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Reports</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Reports</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- /breadcrumb -->

        <!-- main-content-body -->
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
                            <h4 class="card-title mg-b-10">All Case Data</h4>
                        </div>
                        <div class="main-content-body">
                            <div class="row row-sm">
                                <div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
                                    <div class="card">
                                        <div class="card-body table-new">
                                            <div id="success_message" class="ajax_response" style="display: none;"></div>
                                            <div class="panel panel-primary">
                                                <div class="tab-menu-heading">
                                                    <div class="tabs-menu1">
                                                        <ul class="nav panel-tabs">
                                                            <li><a href="#tabNew" class="active" data-bs-toggle="tab" data-bs-target="#tabNew">NCRP Case Data</a></li>
                                                            <li><a href="#tabReturned" data-bs-toggle="tab" data-bs-target="#tabReturned">Others Case Data</a></li>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <div class="panel-body tabs-menu-body">
                                                    <div class="tab-content">
                                                        <div class="tab-pane active" id="tabNew">
                                                            <form id="complaint-form-ncrp">
                                                                <div class="row">
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label for="from-date-new">From Date:</label>
                                                                            <input type="date" class="form-control" id="from-date-new" name="from_date">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label for="to-date-new">To Date:</label>
                                                                            <input type="date" class="form-control" id="to-date-new" name="to_date">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label for="current_date">Today:</label>
                                                                            <select class="form-control" id="current_date" name="current_date">
                                                                                <option value="">All</option>
                                                                                <option value="today">Today</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label for="bank_action_status">Bank Action </label>
                                                                            <select class="form-control" id="bank_action_status">
                                                                                <option value="">--select--</option>
                                                                                <option value="moneytransferto">Money Transfer to</option>
                                                                                <option value="cashwithdrawalthroughcheque">Cash Withdrawal through Cheque</option>
                                                                                <option value="transactionputonhold">Transaction put on hold</option>
                                                                                <option value="other">Other</option>
                                                                                <option value="withdrawalthroughatm">Withdrawal through ATM</option>
                                                                                <option value="withdrawalthroughpos">Withdrawal through POS</option>
                                                                                <option value="wrongtransaction">Wrong Transaction</option>
                                                                                <option value="closedcomplaint">Closed Complaint</option>
                                                                                <option value="oldtransaction">Old Transaction</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label for="evidence_type">Evidence Type:</label>
                                                                            <select class="form-control" id="evidence_type" name="evidence_type">
                                                                                <option value="">--select--</option>
                                                                                @foreach($evidenceTypes as $evidenceType)
                                                                                    <option value="{{ $evidenceType->id }}">{{ $evidenceType->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            @error('evidence_type')
                                                                                <div class="text-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label for="options">urls:</label>
                                                                            <select class="form-control" id="options"></select>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12 text-right">
                                                                        <button type="submit" class="btn btn-primary">Submit</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                            <div class="table-responsive">
                                                                <table id="example" class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap table-striped">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>SL No</th>
                                                                            <th>Acknowledgement No</th>
                                                                            <th>District/Police Station</th>
                                                                            <th>Complainant Name & Mobile number</th>
                                                                            <th>Transaction ID/UTR Number</th>
                                                                            <th>Bank/Wallet/Merchant</th>
                                                                            <th>Account ID</th>
                                                                            <th>Amount</th>
                                                                            <th>Entry Date</th>
                                                                            <th>Current Status</th>
                                                                            <th>Date of Action</th>
                                                                            <th>Action Taken By</th>
                                                                            <th>Action</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <!-- Data will be populated here -->
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane" id="tabReturned">
                                                            <form id="complaint-form-others">
                                                                <div class="row">
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label for="from-date-others">From Date:</label>
                                                                            <input type="date" class="form-control" id="from-date-others" name="from_date">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label for="to-date-others">To Date:</label>
                                                                            <input type="date" class="form-control" id="to-date-others" name="to_date">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label for="current_value">Today:</label>
                                                                            <select class="form-control" id="current_value" name="current_value">
                                                                                <option value="">All</option>
                                                                                <option value="today">Today</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label for="evidence_type">Evidence Type:</label>
                                                                            <select class="form-control" id="evidence_type" name="evidence_type">
                                                                                <option value="">--select--</option>
                                                                                @foreach($evidenceTypes as $evidenceType)
                                                                                    <option value="{{ $evidenceType->id }}">{{ $evidenceType->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            @error('evidence_type')
                                                                                <div class="text-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label for="options">urls:</label>
                                                                            <select class="form-control" id="options"></select>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12 text-right">
                                                                        <button type="submit" class="btn btn-primary">Submit</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                            <div class="table-responsive">
                                                                <table id="example1" class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap table-striped" style="width:100%">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>SL No</th>
                                                                            <th>Source type</th>
                                                                            <th>Case Number</th>
                                                                            <th>URL</th>
                                                                            <th>Domain</th>
                                                                            <th>IP</th>
                                                                            <th>Registrar</th>
                                                                            <th>Remarks</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <!-- Data will be populated here -->
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /row -->
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="path_to_bootstrap_js"></script>

    <link rel="stylesheet" href="{{ asset('css/toastr.min.css') }}">

    <script src="{{ asset('js/toastr.js') }}"></script>

    @if (session('status'))
    <script>
        toastr.success('{{ session('status') }}', 'Success!')
    </script>
@endif

    <script>
        $(document).ready(function() {
            $('.tabs-menu1 ul li a').on('click', function(e) {
                e.preventDefault();
                $('.tabs-menu1 ul li a').removeClass('active');
                $(this).addClass('active');

                // Show the corresponding tab content
                $('.tab-pane').removeClass('active');
                $($(this).attr('data-bs-target')).addClass('active');
            });

            $('#complaint-form-ncrp').on('submit', function(e) {
                e.preventDefault();
                // Add your AJAX code here to filter the NCRP Case Data table
            });

            $('#complaint-form-others').on('submit', function(e) {
                e.preventDefault();
                // Add your AJAX code here to filter the Others Case Data table
            });
        });
    </script>

<script>
$(document).ready(function() {
    // Initialize DataTable for #example
    var tableNew = $('#example').DataTable({
        processing: true,
        serverSide: true,
        layout: {
                topStart: {
                buttons: [ 'csv', 'excel','print']
                }
                },
       
        ajax: {
            url: "{{ route('get.datalist.ncrp') }}",
            data: function(d) {
                return $.extend({}, d, {
                    from_date: $('#from-date-new').val(),
                    to_date: $('#to-date-new').val(),
                    current_date: $('#current_date').val(),
                    bank_action_status: $('#bank_action_status').val(),
                });
            }
        },
        columns: [
            { data: 'id' },
            { data: 'acknowledgement_no' },
            { data: 'district' },
            { data: 'complainant_name' },
            { data: 'transaction_id' },
            { data: 'bank_name' },
            { data: 'account_id' },
            { data: 'amount' },
            { data: 'entry_date' },
            { data: 'current_status' },
            { data: 'date_of_action' },
            { data: 'action_taken_by_name' },
            { data: 'edit' }
        ],
        order: [0, 'desc'],
        ordering: true
    });

    // Initialize DataTable for #example1
    var tableReturned = $('#example1').DataTable({
        processing: true,
        serverSide: true,
        layout: {
                topStart: {
                buttons: [ 'csv', 'excel','print']
                }
                },
     
        ajax: {
            url: "{{ route('get.datalist.othersourcetype') }}",
            data: function(d) {
                return $.extend({}, d, {
                    from_date: $('#from-date-others').val(),
                    to_date: $('#to-date-others').val(),
                    current_value: $('#current_value').val(),
                });
            }
        },
        columns: [
            { data: 'id' },
            { data: 'source_type' },
            { data: 'case_number' },
            { data: 'url' },
            { data: 'domain' },
            { data: 'ip' },
            { data: 'registrar' },
            { data: 'remarks' }
        ],
        order: [0, 'desc'],
        ordering: true
    });

    // Form submission handler for #complaint-form-ncrp
    $('#complaint-form-ncrp').on('submit', function(e) {
        e.preventDefault();
        tableNew.ajax.reload();
    });

    // Form submission handler for #complaint-form-others
    $('#complaint-form-others').on('submit', function(e) {
        e.preventDefault();
        tableReturned.ajax.reload();
    });

    // Tab navigation handler
    $('.tabs-menu1 ul li a').on('click', function(e) {
        e.preventDefault();
        $('.tabs-menu1 ul li a').removeClass('active');
        $(this).addClass('active');

        // Show the corresponding tab content
        $('.tab-pane').removeClass('active');
        $($(this).attr('data-bs-target')).addClass('active');
    });
});

</script>

@endsection
