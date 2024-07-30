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
        .tdblack{
            color:black !important;
        }
        .tdgreen{
            color:green !important;
        }
        .tdred{
             color:red !important;
        }
        .table-wrapper {
        max-height: 200px !important;
        overflow-y: auto !important;
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
                            <div class="m-4 d-flex justify-content-between align-items-start">
                                <div class="col-7">
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td><span>Acknowledgement No</span></td>
                                                <td>:</td>
                                                <td><strong>{{ @$complaint->acknowledgement_no }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td><span>Complainant Name</span></td>
                                                <td>:</td>
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
                                            <tr>
                                                <td>Age</td>
                                                <td>:</td>
                                                <td>{{ @$additional->age }}</td>
                                            </tr>
                                            <tr>
                                                <td>Profession</td>
                                                <td>:</td>
                                                <td>{{ optional(@$additional->professionRelation)->name }}</td>
                                            </tr>
                                            <tr>
                                                <td>Modus</td>
                                                <td>:</td>
                                                <td>{{ optional(@$additional->modusRelation)->name }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>

                                <div class="col-4">
                                    <div class="row justify-content-end">
                                        @php $id = Crypt::encrypt($complaint->acknowledgement_no); @endphp
                                        <div class="col-2 px-1">
                                            <div
                                                class="task-box primary mb-0 d-flex align-items-center justify-content-center">
                                                <a class="text-white d-flex align-items-center justify-content-center w-100 h-100"
                                                    data-toggle="tooltip" data-placement="top" title="Back"
                                                    href="{{ route('case-data.index') }}">
                                                    <i class="ti ti-arrow-left"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-2 px-1">
                                            <div
                                                class="task-box primary mb-0 d-flex align-items-center justify-content-center">
                                                <a class="text-white d-flex align-items-center justify-content-center w-100 h-100"
                                                    data-toggle="modal" data-target="#updateProfile" data-toggle="tooltip"
                                                    data-placement="top" title="Profile Update">
                                                    <i class="ti ti-pencil"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-2 px-1">
                                            <div
                                                class="task-box primary mb-0 d-flex align-items-center justify-content-center">
                                                <a class="text-white d-flex align-items-center justify-content-center w-100 h-100"
                                                    href="{{ route('download.fir', ['ak_no' => $complaint->acknowledgement_no]) }}"
                                                    data-toggle="tooltip" data-placement="top" title="Download FIR">
                                                    <i class="ti ti-download"></i>
                                                </a>
                                            </div>


                                        </div>
                                        <div class="col-2 px-1">
                                            <div
                                                class="task-box primary mb-0 d-flex align-items-center justify-content-center">
                                                <a class="text-white d-flex align-items-center justify-content-center w-100 h-100"
                                                    data-toggle="modal" data-target="#uploadFIRModal" data-toggle="tooltip"
                                                    data-placement="top" title="Upload FIR">
                                                    <i class="ti ti-upload"></i>
                                                </a>
                                            </div>
                                        </div>

                                        <div class="col-2 px-1">
                                            <div
                                                class="task-box primary mb-0 d-flex align-items-center justify-content-center">
                                                <a class="text-white d-flex align-items-center justify-content-center w-100 h-100"
                                                    data-toggle="tooltip" data-placement="top" title="Add Evidence"
                                                    href="{{ route('evidence.create', ['acknowledgement_no' => @$id]) }}">
                                                    <i class="ti ti-plus"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-2 px-1">
                                            <div
                                                class="task-box primary mb-0 d-flex align-items-center justify-content-center">
                                                <a class="text-white d-flex align-items-center justify-content-center w-100 h-100"
                                                    data-toggle="tooltip" data-placement="top" title="View Evidence"
                                                    href="{{ route('evidence.index', ['acknowledgement_no' => @$id]) }}">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                        {{-- for bulk upload --}}
                                        <div class="col-4 px-1 mt-3">
                                            <div
                                                class="task-box primary mb-0 d-flex align-items-center justify-content-center">
                                                <a class="text-white d-flex align-items-center justify-content-center w-100 h-100"
                                                    data-toggle="tooltip" data-placement="top" title="View Evidence"
                                                    href="{{ route('evidence.bulkUpload', ['acknowledgement_no' => @$id]) }}">
                                                    <i class="ti ti-upload"></i> Bulk Upload
                                                </a>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="updateProfile" tabindex="-1" role="dialog"
                                aria-labelledby="updateProfileLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="updateProfileLabel">Update Profile</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('profile.update') }}" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <div class="form-group">
                                                    <label for="age">Age</label>
                                                    <input type="number" min="1" class="form-control" name="age"
                                                        value="{{ @$additional->age }}">
                                                </div>
                                                {{-- <div class="form-group" hidden>
                                                    <label for="profession">Profession</label>
                                                    <input type="text" class="form-control" name="profession"
                                                        value="{{ @$additional->profession }}">
                                                </div> --}}
                                                <div class="form-group">
                                                    <label for="profession">Profession:</label>
                                                    <select class="form-control" id="profession" name="profession">
                                                        <option value="">--select--</option>
                                                        @foreach($professions as $profession)
                                                            <option value="{{ $profession->id }}" {{ (isset($additional->profession) && $additional->profession == $profession->id) ? 'selected' : '' }}>
                                                                {{ $profession->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('profession')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="profession">Modus:</label>
                                                    <select class="form-control" id="modus" name="modus">
                                                        <option value="">--select--</option>
                                                        @foreach($modus as $modus)
                                                            <option value="{{ $modus->id }}" {{ (isset($additional->modus) && $additional->modus== $modus->id) ? 'selected' : '' }}>
                                                                {{ $modus->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('modus')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <input type="hidden" name="acknowledgement_no"
                                                    value="{{ @$complaint->acknowledgement_no }}">
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- Modal Structure -->
                            <div class="modal fade" id="uploadFIRModal" tabindex="-1" role="dialog"
                                aria-labelledby="uploadFIRModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="uploadFIRModalLabel">Upload FIR</h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('fir_file.upload') }}" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <div class="form-group">
                                                    <label for="firFile">Upload FIR File</label>
                                                    <input type="file" class="form-control" id="firFile"
                                                        name="fir_file">
                                                    <input type="hidden" name="acknowledgement_no"
                                                        value="{{ @$complaint->acknowledgement_no }}">
                                                </div>
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <br>

                            <br>
                            <table class="table table-bordered">
                                <thead>
                                <tr >
                                <th colspan="8" ><b>Debited Transaction Details</b></th>
                                </tr>
                                    <tr>
                                    <th>Sl.no</th>
                                        <th>Transaction ID / UTR Number</th>
                                        <th>Account Number</th>
                                        <th>Transaction Date</th>
                                        <th>Transaction Amount</th>
                                        <th>Bank</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($complaints as $complnt)
                                        <tr>
                                         <td> {{ $loop->iteration }} </td>
                                            <td>{{ @$complnt->transaction_id }}</td>
                                            <td>{{ @$complnt->account_id }}</td>
                                            <td>{{ @$complnt->bankCaseData->transaction_date }}</td>
                                            <td>{{ @$complnt->amount }}</td>
                                            <td>{{ @$complnt->bank_name }}</td>
                                            <td>{{ @$complnt->current_status }}</td>
                                            <td>
                                                <div class="form-check form-switch form-switch-sm d-flex justify-content-center align-items-center"
                                                    dir="ltr"> <input data-id="{{ $complnt->_id }}" data-transaction="{{ @$complnt->transaction_id }}" data-ackno="{{ $complnt->acknowledgement_no }}"
                                                        onchange="confirmActivation(this)" class="form-check-input"
                                                        type="checkbox" id="SwitchCheckSizesm{{ $complnt->id }}"
                                                        {{ $complnt->com_status == 1 ? 'checked' : 0 }}>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <br>

                            <table>
                            <tbody>
                            <tr>
                                <td>Total Fraudulent Amount reported by Complainant : </td>
                                <td><span
                                style="color: red;">₹{{ number_format($sum_amount, 2) }}</span></td>

                            {{-- </tr>
                             <tr>
                                <td>Total amount hold</td>
                                <td><span
                                style="color: red;">₹{{ number_format($hold_amount, 2) }}</span></td>

                            </tr> --}}
                             {{-- <tr>
                                <td>Total amount lost</td>
                                <td><span
                                style="color: red;">₹{{ number_format($lost_amount, 2) }}</span></td>

                            </tr>  --}}
                            <tr>
                                <td>Pending amount : </td>
                                <td><span
                                style="color: red;">₹{{ number_format($pending_amount, 2) }}</span></td>

                            </tr>
                            <tr>
                                <td>Total amount hold</td>
                                <td><span
                                style="color: green;">₹{{ number_format($hold_amount, 2) }}</span></td>

                            </tr>
                            <tr>
                                <td>Total amount lost</td>
                                <td><span
                                style="color: blue;">₹{{ number_format($lost_amount, 2) }}</span></td>

                            </tr>
                           </tbody>
                           </table>
                        </div>
                         <table class="table table-bordered table-wrapper" style="width:auto">
                                <thead>
                                <tr>
                                <th colspan="4" class="tdblack"><b>Pending Banks Details</b></th>
                                </tr>
                                    <tr >
                                    <th>Sl.no</th>
                                        <th >Pending Banks</th>
                                        <th >Transaction Count</th>
                                        <th >Transaction Amount</th>
                                        
                                    </tr>
                                @if($finalData_pending_banks)
                                    @foreach ($finalData_pending_banks as $item)
                                <tr>
                                <td class="tdred"> {{ $loop->iteration }} </td>
                                    <td class="tdred">{{ $item['pending_banks'] }}</td>
                                    <td class="tdred">
                                    {{ $item['transaction_id'] }}
                                    </td>
                                    <td class="tdred">
                                        {{ $item['transaction_amount'] }}
                                        {{-- <span class="copy-icon" style="cursor:pointer; color:blue;"> => </span> --}}
                                    </td>
                                    {{-- <td class="tdred">
                                        <input type="number" class="editable-field" value="{{ $item['desputed_amount'] }}" data-amount="{{ $item['transaction_amount'] }}" data-transaction-id="{{ $item['transaction_id'] }}" data-pending_banks="{{ $item['pending_banks'] }}">
                                    {{ $item['desputed_amount'] }} 
                                    </td> --}}
                                </tr>
                                @endforeach

                                @else

                                <tr>
                                    <td colspan="3" class="tdgreen">No pending banks found </td>
                                </tr>

                                @endif

                                <tr>
                                </tr>

                                </thead>
                            </table>

                        </div>



                    <div class="card overflow-hidden review-project">


                        <div class="card-body" width="500px">
                            Action Taken By Bank
                            <br>
                            <div style="overflow-x: auto;">


                                @if (empty($final_array))
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="alert alert-info col-6 text-center mt-3 mb-3">
                                            No Data available yet!
                                        </div>

                                    </div>
                                @else
                                    <table class="table table-bordered ">
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
                                            @foreach ($final_array as $bank_data)
                                                <tr>
                                                    <td>{{ @$bank_data['account_no_1'] }}<br><br>
                                                        {{ @$bank_data['transaction_id_or_utr_no'] }}<br><br>
                                                        Layer : {{ @$bank_data['Layer'] }}</td>
                                                    <td>{{ @$bank_data['action_taken_by_bank'] }}<br><br>
                                                        Txn Date: {{ @$bank_data['transaction_date'] }}</td>
                                                    <td>{{ @$bank_data['bank'] }}</td>
                                                    <td>A/C No : {{ @$bank_data['account_no_2'] }}<br>
                                                        ifsc Code : {{ @$bank_data['ifsc_code'] }}</td>
                                                    <td>
                                                        Transaction ID /UTR Number :
                                                        {{ @$bank_data['transaction_id_sec'] }}<br><br>
                                                        Transaction Amount : {{ @$bank_data['transaction_amount'] }}
                                                        <br><br>
                                                        <span style="color:red">Disputed Amount : {{ @$bank_data['dispute_amount'] }}</span></td>
                                                        
                                                    <td>{{ @$bank_data['branch_location'] }}<br><br>
                                                        {{ @$bank_data['branch_manager_details'] }} </td>
                                                    <td>{{ @$bank_data['reference_no'] }}<br><br>
                                                        {{ @$bank_data['remarks'] }}</td>
                                                    <td></td>
                                                    <td><i class="side-menu__icon fe fe-user"> </i> :
                                                        {{ @$bank_data['action_taken_name'] }}<br>
                                                        <i class="side-menu__icon fe fe-mail"> </i>
                                                        {{ @$bank_data['action_taken_email'] }}
                                                        {{ @$bank_data['date_of_action'] }}
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
    <link rel="stylesheet" href="{{ asset('css/toastr.min.css') }}">

    <script src="{{ asset('js/toastr.js') }}"></script>

    @if (session('status'))
        <script>
            toastr.success('{{ session('status') }}', 'Success!')
        </script>
    @endif
    <script>
        function confirmActivation(identifier) {
            var isChecked = $(identifier).prop('checked');
            var confirmationMessage = isChecked ? "Do you want to activate this?" :
                "Do you want to deactivate this?";

            if (confirm(confirmationMessage)) {
                activateLink(identifier);
            } else {
                // Revert the checkbox state if the user cancels the action
                $(identifier).prop('checked', !isChecked);
            }
        }

        function activateLink(identifier){
            // alert("dsf");
            var status = $(identifier).prop('checked') == true ? 1 : 0;
            var com_id = $(identifier).data('id');
            var transaction_id_sec = $(identifier).data('transaction');
            var ackno = $(identifier).data('ackno');

            $.ajax({
                type: "GET",
                dataType: "json",
                url: '/activateLinkIndividual',
                data: {
                    'status': status,
                    'com_id': com_id,
                    'transaction_id_sec': transaction_id_sec,
                    'ackno':ackno
                },
                success: function(data) {
                    console.log(data.status)
                    if(data.success){
                    window.location.reload();
                    toastr.success(' status successfully updated!');
                    }
                    else{

                        toastr.error(' updation error!');
                    }


                },

            });

        }
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const copyIcons = document.querySelectorAll('.copy-icon');
        copyIcons.forEach(function(icon) {
            icon.addEventListener('click', function() {
                const amount = this.previousSibling.textContent.trim();
                const inputField = this.parentElement.nextElementSibling.querySelector('.editable-field');
                inputField.value = amount;
                inputField.dispatchEvent(new Event('input')); // Trigger input event

            });
        });
    });

    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editableFields = document.querySelectorAll('.editable-field');

            function sendAjaxRequest(field) {
                const transactionId = field.getAttribute('data-transaction-id');
                const pendingBanks = field.getAttribute('data-pending_banks');
                const transactionAmount = field.value;

                /*$.ajax({
                    url: '{{ route('update.transaction.amount') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        transaction_id: transactionId,
                        pending_banks: pendingBanks,
                        transaction_amount: transactionAmount
                    },
                    success: function(response) {
                        if (response.success) {
                            console.log('Transaction amount updated successfully.');
                        } else {
                            console.log('Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('AJAX Error: ' + error);
                    }
                });
                */
            }

            editableFields.forEach(function(field) {
                // Trigger AJAX request when the value is changed
                field.addEventListener('change', function() {
                    sendAjaxRequest(this);
                });

                // Trigger AJAX request when the value is initially set
                field.addEventListener('input', function() {
                    sendAjaxRequest(this);
                });
            });
        });

    </script>

@endsection
