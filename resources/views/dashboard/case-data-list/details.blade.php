@extends('layouts.app')

@section('content')
    <style>
        .table {
            width: 100%;
            overflow-x: scroll;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .table thead th {
            background-color: #f2f2f2;
        }
    </style>
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


                        <div class="card-body" width="500px">
                            <div class=" m-4 d-flex justify-content-between">
                                <div class="row">
                                    <div class="col-12">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td><span>Complainant Name</span></td>
                                                    <td>: </td>
                                                    <td>{{ @$complaint->complainant_name }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Mobile</td>
                                                    <td>:</td>
                                                    <td>{{ @$complaint->complainant_mobile }}</td>
                                                </tr>
                                                <tr>
                                                    <td>District</td>
                                                    <td>:</td>
                                                    <td>{{ @$complaint->district }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Police Station</td>
                                                    <td>:</td>
                                                    <td>{{ @$complaint->police_station }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-2 col-6 text-center">
                                    <div class="row justify-content-end">

                                        <div class="col-4 px-1">
                                            <div class="task-box primary  mb-0">
                                                <a class="text-white" data-toggle="tooltip" data-placement="top"
                                                    title="Back" href="{{ route('case-data.index') }}">
                                                    <h3 class="mb-0"><i class="ti ti-arrow-left"></i></h3>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-4 px-1">
                                            <div class="task-box primary  mb-0">
                                                <a class="text-white" data-toggle="tooltip" data-placement="top"
                                                    title="Add Evidence"
                                                    href="{{ route('evidence.create', ['acknowledgement_no' => @$complaint->acknowledgement_no]) }}">
                                                    <h3 class="mb-0"><i class="ti ti-plus"></i></h3>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-4 px-1">
                                            <div class="task-box primary  mb-0">
                                                <a class="text-white" data-toggle="tooltip" data-placement="top"
                                                    title="View Evidence"
                                                    href="{{ route('evidence.index', ['acknowledgement_no' => @$complaint->acknowledgement_no]) }}">
                                                    <h3 class="mb-0"><i class="ti ti-eye"></i></h3>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>


                            <br>
                            Total Fraudulent Amount reported by Complainant : <span
                                style="color: red;">₹{{ number_format($sum_amount, 2) }}</span>
                            <br><br>
                            Debited Transaction Details

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Transaction ID / UTR Number</th>
                                        <th>Account Number</th>
                                        <th>Transaction Date</th>
                                        <th>Transaction Amount</th>
                                        <th>Bank</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($complaints as $complnt)
                                        <tr>
                                            <td>{{ @$complnt->transaction_id }}</td>
                                            <td>{{ @$complnt->account_id }}</td>
                                            <td>{{ @$complnt->entry_date }}</td>
                                            <td>{{ @$complnt->amount }}</td>
                                            <td>{{ @$complnt->bank_name }}</td>
                                            <td>{{ @$complnt->current_status }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>


                    </div>


                    <div class="card overflow-hidden review-project">


                        <div class="card-body" width="500px">
                            Action Taken By Bank
                            <br>
                            <div style="overflow-x: auto;">
                                @if ($bank_datas->isEmpty())
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="alert alert-info col-6 text-center mt-3 mb-3">
                                            No Data available yet!
                                        </div>

                                    </div>
                                @else
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Account No./(Wallet/PG/PA) Id<br>
                                                    <hr> Transaction ID / UTR Number
                                                </th>
                                                <th>Action Taken by Bank / (Wallet/PG/PA) / Merchant / Insurance</th>
                                                <th>Bank<br>
                                                    <hr>(Wallet/PG/PA)<br>
                                                    <hr> Merchant<br>
                                                    <hr>Insurance
                                                </th>
                                                <th>Account Details</th>
                                                <th>Transaction Details</th>
                                                <th>Branch Location<br>
                                                    <hr>Branch Manager Name & Contact Details
                                                </th>
                                                <th>Reference No / Remarks</th>
                                                <th>ATM ID<br>
                                                    <hr>Place / Location of ATM
                                                </th>
                                                <th>Action Taken By<br>
                                                    <hr>Date of Action
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($bank_datas as $bank_data)
                                                <tr>
                                                    <td>{{ @$bank_data->account_no_1 }}<br><br>
                                                        {{ @$bank_data->transaction_id_or_utr_no }}<br><br>
                                                        Layer : {{ @$bank_data->Layer }}</td>
                                                    <td>{{ @$bank_data->action_taken_by_bank }}<br><br>
                                                        Txn Date: {{ @$bank_data->transaction_date }}</td>
                                                    <td>{{ @$bank_data->bank }}</td>
                                                    <td>A/C No : {{ @$bank_data->account_no_2 }}<br>
                                                        ifsc Code : {{ @$bank_data->ifsc_code }}</td>
                                                    <td>
                                                        Transaction ID /UTR Number :
                                                        {{ @$bank_data->transaction_id_or_utr_no }}<br><br>
                                                        Transaction Amount : {{ @$bank_data->transaction_amount }}</td>
                                                    <td>{{ @$bank_data->branch_location }}<br><br>
                                                        {{ @$bank_data->branch_manager_details }} </td>
                                                    <td>{{ @$bank_data->reference_no }}<br><br>
                                                        {{ @$bank_data->remarks }}</td>
                                                    <td></td>
                                                    <td><i class="side-menu__icon fe fe-user"> </i> :
                                                        {{ @$bank_data->action_taken_name }}<br>
                                                        <i class="side-menu__icon fe fe-mail"> </i>
                                                        {{ @$bank_data->action_taken_email }}
                                                        {{ @$bank_data->date_of_action }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>

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
            $('[data-toggle="tooltip"]').tooltip();

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
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'source_type'
                    },
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
                "order": [0, 'desc'],
                'ordering': true
            });
        });






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
    </script>
@endsection
