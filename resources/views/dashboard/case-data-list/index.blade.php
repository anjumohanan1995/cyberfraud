@extends('layouts.app')

@section('content')
    <!-- container -->
    <div class="container-fluid">
        <!-- breadcrumb -->
        <div class="breadcrumb-header justify-content-between">
            <div>
                <h4 class="content-title mb-2">
                    Hi, welcome back!
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="#">Case Data Management</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Case Data
                        </li>
                    </ol>
                </nav>
            </div>

        </div>
        <!-- /breadcrumb -->
        <!-- main-content-body -->
        <div class="main-content-body">



            <!-- row -->
            <div class="row row-sm">
                <div class="col-md-12 col-xl-12">
                    <div class="card overflow-hidden review-project">
                        <div class="card-body">
                            <div class=" m-4 d-flex justify-content-between">

                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show w-100" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                                <div class="alert alert-success-one alert-dismissible fade show w-100" role="alert"
                                    style="display:none">

                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>
                            <div class=" m-4 d-flex justify-content-between">
                                <h4 class="card-title mg-b-10">
                                    All Case Data
                                </h4>
                                <div class="col-md-1 col-6 text-center">
                                    <div class="task-box primary  mb-0">
                                        <a class="text-white" href="{{ route('modus.create') }}">
                                            <p class="mb-0 tx-12">Add </p>
                                            <h3 class="mb-0"><i class="fa fa-plus"></i></h3>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <form id="complaint-form">
                                <div class="row">
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="from-date">From Date:</label>
                                      <input type="date" class="form-control" id="from-date" name="from_date">
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="to-date">To Date:</label>
                                      <input type="date" class="form-control" id="to-date" name="to_date">
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="mobile">Complaint Mobile no: </label>
                                      <input type="text" class="form-control" id="mobile" name="mobile">
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="type">Transaction Type:</label><br>
                                        <select class="form-control" id="type">
                                            <option>--Select--</option>
                                            <option value="bank">Bank</option>
                                            <option value="wallet">Wallet/PG/PA</option>
                                            <option value="merchant">Merchant</option>
                                            <option value="insurance">Insurance</option>
                                        </select>
                                        <br>

                                    </div>
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="options">Bank/Wallet/Merchant/Insurance:</label>
                                        <select class="form-control" id="options"></select>
                                        </select>
                                      </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="filled-by">Filled by(within 24 hrs):</label>
                                      <select class="form-control" id="filled-by" name="filled-by">
                                        <option value="">All</option>
                                        <option value="citizen">Citizen</option>
                                        <option value="cyber">Cyber</option>
                                      </select>
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="acknowledgement_no">Acknowledgement No: </label>
                                      <input type="text" class="form-control" id="acknowledgement_no" name="acknowledgement_no">
                                    </div>
                                  </div>
                                  <div class="col-md-3" hidden>
                                    <div class="form-group">
                                      <label for="complaint-reported">Complaint Reported:</label>
                                      <select class="form-control" id="complaint-reported" name="complaint-reported">
                                        <option value="">All</option>
                                        <option value="#">Through Helpline(1930)</option>
                                        <option value="#">Cyber Crime Portal</option>
                                      </select>
                                    </div>
                                  </div>
                                  <div class="col-md-3" hidden>
                                    <div class="form-group">
                                      <label for="fir-lodge">FIR Lodge:</label>
                                      <select class="form-control" id="fir-lodge" name="fir-lodge">
                                        <option value="">--Select--</option>
                                        <option value="#">Yes</option>
                                        <option value="#">No</option>
                                      </select>
                                    </div>
                                  </div>
                                </div>

                                <div class="row">
                                  <div class="col-md-3" hidden>
                                    <div class="form-group">
                                      <label for="acknowledgement_no">Acknowledgement No: </label>
                                      <input type="text" class="form-control" id="acknowledgement_no" name="acknowledgement_no">
                                    </div>
                                  </div>
                                  <div class="col-md-3" hidden>
                                    <div class="form-group">
                                      <label for="sub-category">Sub category:</label>
                                      <select class="form-control" id="sub-category" name="sub-category">
                                        <option value="">--Select--</option>
                                        <option value="#">Yes</option>
                                        <option value="#">No</option>
                                      </select>
                                    </div>
                                  </div>
                                  <div class="col-md-3" hidden>
                                    <div class="form-group">
                                      <label for="search-by">Search by:</label>
                                      <select class="form-control" id="search-by" name="search-by">
                                        <option value="">--Select--</option>
                                        <option value="account_id">Account ID/Account Number/UPI ID</option>
                                        <option value="transaction_id">Transaction ID/UTR/RRN Number</option>
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

                            <div class="table-responsive mb-0">
                                <table id="example"
                                    class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap table-striped">
                                    <thead>
                                        <tr>
                                            <th>SL No</th>
                                            <th>Source Type</th>
                                            <th>Acknowledgement No</th>
                                            <th>District</th>
                                            <th>Police Station</th>
                                            <th>Complainant Name</th>
                                            <th>Complainant Mobile</th>
                                            <th>Transaction ID</th>
                                            <th>Bank Name</th>
                                            <th>Account ID</th>
                                            <th>Amount</th>
                                            <th>Entry Date</th>
                                            <th>Current Status</th>
                                            <th>Date of Action</th>
                                            <th>Action Taken By Name</th>
                                            <th>Action Taken By Designation</th>
                                            <th>Action Taken By Mobile</th>
                                            <th>Action Taken By Email</th>
                                            <th>Action Taken By Bank</th>

                                            <th>ACTION</th>
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
            <!-- /row -->


        </div>
        <!-- /row -->
    </div>

    <script>
        $("#type").on('change', function() {
            var type = document.getElementById("type").value;
            var dropdown = document.getElementById("options");

            dropdown.innerHTML = ""; // Clear previous options

            if (type === "bank") {
                // Assuming `banks` is defined somewhere in your code
                var banks = @json($banks); // Convert the PHP array to JSON

                banks.forEach(function(bank) {
                    var option = document.createElement("option");
                    option.text = bank.name; // Accessing the `name` property of each bank
                    dropdown.add(option);
                });
            }

            if (type === "wallet") {
                // Assuming `walet` is defined somewhere in your code
                var wallets = @json($wallets); // Convert the PHP array to JSON

                wallets.forEach(function(wallet) {
                    var option = document.createElement("option");
                    option.text = wallet.name; // Accessing the `name` property of each bank
                    dropdown.add(option);
                });
            }

            if (type === "merchant") {
                // Assuming `walet` is defined somewhere in your code
                var merchants = @json($merchants); // Convert the PHP array to JSON

                merchants.forEach(function(merchant) {
                    var option = document.createElement("option");
                    option.text = merchant.name; // Accessing the `name` property of each bank
                    dropdown.add(option);
                });
            }

            if (type === "insurance") {
                // Assuming `walet` is defined somewhere in your code
                var insurances = @json($insurances); // Convert the PHP array to JSON

                insurances.forEach(function(insurance) {
                    var option = document.createElement("option");
                    option.text = insurance.name; // Accessing the `name` property of each bank
                    dropdown.add(option);
                });
            }
            // Add similar logic for other types if needed
        });
    </script>

     <script>
$(document).ready(function() {

    var table = $('#example').DataTable({
        processing: true,
        serverSide: true,
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ],
        ajax: {
            url: "{{ route('get.datalist') }}",
            data: function(d) {
                return $.extend({}, d, {});
            }
        },
        columns: [
            { data: 'id' },
            { data: 'source_type' },
            {
                        data: 'acknowledgement_no'
                    },
                    {
                        data: 'district'
                    },
                    {
                        data: 'police_station'
                    },
                    {
                        data: 'complainant_name'
                    },
                    {
                        data: 'complainant_mobile'
                    },
                    {
                        data: 'transaction_id'
                    },
                    {
                        data: 'bank_name'
                    },
                    {
                        data: 'account_id'
                    },
                    {
                        data: 'amount'
                    },
                    {
                        data: 'entry_date'
                    },
                    {
                        data: 'current_status'
                    },
                    {
                        data: 'date_of_action'
                    },
                    {
                        data: 'action_taken_by_name'
                    },
                    {
                        data: 'action_taken_by_designation'
                    },
                    {
                        data: 'action_taken_by_mobile'
                    },
                    {
                        data: 'action_taken_by_email'
                    },
                    {
                        data: 'action_taken_by_bank'
                    },
                    {
                        data: 'edit'
                    }
        ],
        order: [0, 'desc'],
        ordering: true
    });

    // Form submission event handler
    $('#complaint-form').submit(function(event) {
        event.preventDefault(); // Prevent default form submission

        var from_date = $("#from-date").val();
        var to_date = $("#to-date").val();
        var mobile = $("#mobile").val();
        var acknowledgement_no = $("#acknowledgement_no").val();
        var filled_by = $("#filled-by").val();
        var search_by = $("#search-by").val();
        var options = $("#options").val();

        // Construct the URL with query parameters
        var url = "{{ route('get.datalist') }}?from_date=" + from_date + "&to_date=" + to_date + "&mobile=" + mobile + "&acknowledgement_no=" + acknowledgement_no + "&filled_by=" + filled_by + "&search_by=" + search_by + "&options=" + options;

        // Reload DataTable with new data based on selected filters
        table.ajax.url(url).load();
    });



});

</script>
@endsection





