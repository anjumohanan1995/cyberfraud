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

                            <div class="table-responsive mb-0">
                                <table id="example"
                                    class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap table-striped">
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
        $(document).ready(function(){
            var table = $('#example').DataTable({
                processing: true,
                serverSide: true,

                buttons: [
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5',
                    'pdfHtml5'
                ],
                "ajax": {
                    "url": "{{ route('get.datalist') }}",
                    "data": function(d) {
                        return $.extend({}, d, {});
                    }
                },
                columns:[{
                        data: 'id'
                    },

                    {
                        data: 'acknowledgement_no'
                    },
                    {
                        data: 'district'
                    },

                    {
                        data: 'complainant_name'
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
                        data: 'edit'
                    }
                ],
                "order": [0, 'desc'],
                'ordering': true
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
