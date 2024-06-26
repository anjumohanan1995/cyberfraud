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
                                      <label for="transaction-type">Transaction Type:</label>
                                      <select class="form-control" id="transaction-type" name="transaction_type">
                                        <option value="">All</option>
                                        <option value="bank">Bank</option>
                                        <option value="wallet">Wallet</option>
                                        <option value="merchant">Merchant</option>
                                        <option value="tsurunc">Insurance</option>
                                      </select>
                                    </div>
                                  </div>
                                </div>

                                <div class="row">
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="bank-wallet-merchant">Bank/Wallet/Merchant/Insurance:</label>
                                      <select class="form-control" id="bank-wallet-merchant" name="bank_wallet_merchant">
                                        <option value="">--Select--</option>
                                        <option value="bank">Bank</option>
                                        <option value="wallet">Wallet</option>
                                        <option value="merchant">Merchant</option>
                                        <option value="tsurunc">Insurance</option>
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
                                      <label for="complaint-reported">Complaint Reported:</label>
                                      <select class="form-control" id="complaint-reported" name="complaint-reported">
                                        <option value="">All</option>
                                        <option value="#">Through Helpline(1930)</option>
                                        <option value="#">Cyber Crime Portal</option>
                                      </select>
                                    </div>
                                  </div>
                                  <div class="col-md-3">
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
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="acknowledgement-no">Acknowledgement No: </label>
                                      <input type="text" class="form-control" id="acknowledgement-no" name="acknowledgement-no">
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="sub-category">Sub category:</label>
                                      <select class="form-control" id="sub-category" name="sub-category">
                                        <option value="">--Select--</option>
                                        <option value="#">Yes</option>
                                        <option value="#">No</option>
                                      </select>
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="sub-category">Sub category:</label>
                                      <select class="form-control" id="sub-category" name="sub-category">
                                        <option value="">--Select--</option>
                                        <option value="#">Yes</option>
                                        <option value="#">No</option>
                                      </select>
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="search-by">Search by:</label>
                                      <select class="form-control" id="search-by" name="search-by">
                                        <option value="">--Select--</option>
                                        <option value="#">Yes</option>
                                        <option value="#">No</option>
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

        // Reload DataTable with new data based on selected date range
        table.ajax.url("{{ route('get.datalist') }}?from_date=" + from_date + "&to_date=" + to_date).load();
    });
});

</script>
@endsection








//         $(document).ready(function() {
//             var table = $('#example').DataTable({
//                 processing: true,
//                 serverSide: true,
//                 buttons: [
//                     'copyHtml5',
//                     'excelHtml5',
//                     'csvHtml5',
//                     'pdfHtml5'
//                 ],
//                 "ajax": {
//                     "url": "{{ route('get.datalist') }}",
//                     "data": function(d) {
//                         return $.extend({}, d, {});
//                     }
//                 },
//                 columns: [{
//                         data: 'id'
//                     },
//                     {
//                         data: 'source_type'
//                     },
//                     {
//                         data: 'acknowledgement_no'
//                     },
//                     {
//                         data: 'district'
//                     },
//                     {
//                         data: 'police_station'
//                     },
//                     {
//                         data: 'complainant_name'
//                     },
//                     {
//                         data: 'complainant_mobile'
//                     },
//                     {
//                         data: 'transaction_id'
//                     },
//                     {
//                         data: 'bank_name'
//                     },
//                     {
//                         data: 'account_id'
//                     },
//                     {
//                         data: 'amount'
//                     },
//                     {
//                         data: 'entry_date'
//                     },
//                     {
//                         data: 'current_status'
//                     },
//                     {
//                         data: 'date_of_action'
//                     },
//                     {
//                         data: 'action_taken_by_name'
//                     },
//                     {
//                         data: 'action_taken_by_designation'
//                     },
//                     {
//                         data: 'action_taken_by_mobile'
//                     },
//                     {
//                         data: 'action_taken_by_email'
//                     },
//                     {
//                         data: 'action_taken_by_bank'
//                     },
//                     {
//                         data: 'edit'
//                     }
//                 ],
//                 "order": [0, 'desc'],
//                 'ordering': true
//             });
//         });



//     // Form submission event handler
//     $('#your-form-id').submit(function(event) {
//         event.preventDefault();
//     var keytable = $('#example').DataTable();
//     keytable.destroy();
//     var table = $('#example').DataTable({
//         processing: true,
//         serverSide: true,
//         buttons: [
//             'copyHtml5',
//             'excelHtml5',
//             'csvHtml5',
//             'pdfHtml5'
//         ],
//         ajax: {
//             url: "{{ route('get.datalist') }}",
//             data: function (d) {
//                 return $.extend({}, d, {
//                     "from_date": $("#from-date").val(),
//                     "to_date": $("#to-date").val(),
//                 });
//             }
//         },


//         columns: [
//             { data: 'id' },
//             { data: 'source_type' },
//             { data: 'acknowledgement_no' },
//             { data: 'district' },
//             { data: 'police_station' },
//             { data: 'complainant_name' },
//             { data: 'complainant_mobile' },
//             { data: 'transaction_id' },
//             { data: 'bank_name' },
//             { data: 'account_id' },
//             { data: 'amount' },
//             { data: 'entry_date' },
//             { data: 'current_status' },
//             { data: 'date_of_action' },
//             { data: 'action_taken_by_name' },
//             { data: 'action_taken_by_designation' },
//             { data: 'action_taken_by_mobile' },
//             { data: 'action_taken_by_email' },
//             { data: 'action_taken_by_bank' },
//             { data: 'edit' }
//         ],
//         order: [0, 'desc'],
//         ordering: true
//     });
// }







        // $(document).on('click', '.bank-case-btn', function() {

        //     var Id = $(this).data('id');
        //     var acknowledgement_no = $(this).data('acknowledgement_no');
        //     var account_id = $(this).data('account_id');

        //     // alert(Id + '-' + acknowledgement_no + '-' + account_id);

        //     $.ajax({
        //         url: 'case-data/bank-case-data',
        //         type: 'get',
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         },
        //         data: {
        //             acknowledgement_no: acknowledgement_no, // Pass the name.
        //             account_id: account_id // Pass the place.
        //         },
        //         success: function(response) {
        //             // Handle success response
        //             console.log(response);
        //         },
        //         error: function(xhr, status, error) {
        //             // Handle error response
        //             console.error(xhr.responseText);
        //         }
        //     });
        // });


        // $(document).on('click', '.bank-case-btn', function() {

        //     var Id = $(this).data('id');
        //     var acknowledgement_no = $(this).data('acknowledgement_no');
        //     var account_id = $(this).data('account_id');

        //     $.ajax({
        //         url: '/case-data-list/',
        //         type: 'POST', // Use POST method.
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         },
        //         data: {
        //             acknowledgement_no: acknowledgement_no, // Pass the name.
        //             account_id: account_id // Pass the place.
        //         },
        //         success: function(response) {
        //             // Handle success response.
        //             // Reload the page.
        //             $('.alert-success-one').html(response.success +
        //                 '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
        //                 '<span aria-hidden="true">&times;</span>' + '</button>').show();

        //             //table.draw();

        //             location.reload();
        //         },
        //         error: function(xhr, status, error) {
        //             // Handle error response.
        //             console.error(xhr.responseText)
        //         }
        //     });
        // });



        // $(document).on('click', '.show-case-btn', function() {
        //     var Id = $(this).data('id');
        //     var acknowledgement_no = $(this).data('acknowledgement_no');
        //     var account_id = $(this).data('account_id');
        //     $.ajax({
        //         url: '/case-data-list/',
        //         type: 'POST', // Use POST method.
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         },
        //         data: {
        //             acknowledgement_no: acknowledgement_no, // Pass the name.
        //             account_id: account_id // Pass the place.
        //         },
        //         success: function(response) {
        //             // Handle success response.
        //             // Reload the page.
        //             $('.alert-success-one').html(response.success +
        //                 '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
        //                 '<span aria-hidden="true">&times;</span>' + '</button>').show();
        //             //table.draw();
        //             location.reload();
        //         },
        //         error: function(xhr, status, error) {
        //             // Handle error response.
        //             console.error(xhr.responseText)
        //         }
        //     });
        // });



