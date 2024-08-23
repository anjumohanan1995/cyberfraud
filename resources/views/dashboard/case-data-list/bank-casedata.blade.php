@extends('layouts.app')

@section('content')
    <!-- container -->
    <div class="container-fluid">
        <!-- breadcrumb -->
        <div class="breadcrumb-header justify-content-between">
            <div>
                <h4 class="content-title mb-2">
                    Upload NCRP Case Data !

                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="#">Case Data Management</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Bank Case Data
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
                            <div class="m-4 d-flex justify-content-between">
                                <h4 class="card-title mg-b-10">
                                    Bank Case Data list.
                                </h4>
                                <div class="col-md-1 col-6 text-center d-flex flex-row">
                                    <div class="task-box primary mb-0 mr-2" style="width: 100%; padding: 6px">
                                        <a class="text-white" href="#">
                                            <div>
                                                <h3 class="mb-0"><i class="fa fa-upload"></i></h3>
                                                <a class="text-white"
                                                    href="{{ route('evidence.create', ['acknowledgement_no' => $acknowledgement_no]) }}">
                                            </div>
                                        </a>
                                    </div>
                                    <div class="task-box primary mb-0" style="width: 100%; padding: 16px">
                                        <a class="text-white" href="{{ route('modus.create') }}">
                                            <div>
                                                <h3 class="mb-0"><i class="fa fa-plus"></i></h3>
                                                <p class="mb-0 tx-12">Add</p>
                                            </div>
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
                                            <th>Transaction ID or UTR No</th>
                                            <th>Layer</th>
                                            <th>Account No 1</th>
                                            <th>Action Taken By Bank</th>
                                            <th>Bank</th>
                                            <th>Account No 2</th>
                                            <th>IFSC Code</th>
                                            <th>Cheque No</th>
                                            <th>MID</th>
                                            <th>TID</th>
                                            <th>Approval Code</th>
                                            <th>Merchant Name</th>
                                            <th>Transaction Date</th>
                                            <th>Transaction Amount</th>
                                            <th>Reference No</th>
                                            <th>Remarks</th>
                                            <th>Date of Action</th>
                                            <th>Action Taken By Bank</th>
                                            <th>Action Taken Name</th>
                                            <th>Action Taken Email</th>
                                            <th>Branch Location</th>
                                            <th>Branch Manager Details</th>
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
        var acknowledgement_no = '{{ $acknowledgement_no }}';
        var account_id = '{{ $account_id }}';


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
                "ajax": {
                    "url": "{{ route('get.bank.datalist') }}",
                    "data": function(d) {
                        d.acknowledgement_no = acknowledgement_no;
                        d.account_id = account_id;
                        return d;
                    }
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'acknowledgement_no'
                    },
                    {
                        data: 'transaction_id_or_utr_no'
                    },
                    {
                        data: 'Layer'
                    },
                    {
                        data: 'account_no_1'
                    },
                    {
                        data: 'action_taken_by_bank'
                    },
                    {
                        data: 'bank'
                    },
                    {
                        data: 'account_no_2'
                    },
                    {
                        data: 'ifsc_code'
                    },
                    {
                        data: 'cheque_no'
                    },
                    {
                        data: 'mid'
                    },
                    {
                        data: 'tid'
                    },
                    {
                        data: 'approval_code'
                    },
                    {
                        data: 'merchant_name'
                    },
                    {
                        data: 'transaction_date'
                    },
                    {
                        data: 'transaction_amount'
                    },
                    {
                        data: 'reference_no'
                    },
                    {
                        data: 'remarks'
                    },
                    {
                        data: 'date_of_action'
                    },
                    {
                        data: 'action_taken_by_bank'
                    },
                    {
                        data: 'action_taken_name'
                    },
                    {
                        data: 'action_taken_email'
                    },
                    {
                        data: 'branch_location'
                    },
                    {
                        data: 'branch_manager_details'
                    },
                    {
                        data: 'edit'
                    }
                ],
                "order": [0, 'desc'],
                'ordering': true
            });
        });
    </script>
@endsection
