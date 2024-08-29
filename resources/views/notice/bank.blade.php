@extends('layouts.app')

@php
use App\Models\RolePermission;
use Illuminate\Support\Facades\Auth;

$user = Auth::user();
$role = $user->role;
$permission = RolePermission::where('role', $role)->first();
$permissions = $permission && is_string($permission->permission) ? json_decode($permission->permission, true) : ($permission->permission ?? []);
$sub_permissions = $permission && is_string($permission->sub_permissions) ? json_decode($permission->sub_permissions, true) : ($permission->sub_permissions ?? []);
$hasShowESTFPermission = $hasShowETFPermission = $hasShowStatusFPermission = $hasShowNoticeTypePermission = $hasGenerateTokenPermission = false;

if ($sub_permissions || $user->role == 'Super Admin') {
    $hasShowESTFPermission = in_array('Show Evidence Source Type Filter', $sub_permissions);
    $hasShowETFPermission = in_array('Show Evidence Type Filter', $sub_permissions);
    $hasShowStatusFPermission = in_array('Show Notice Status Filter', $sub_permissions);
    $hasShowNoticeTypePermission = in_array('Show Notice Type Filter', $sub_permissions);
    $hasGenerateTokenPermission = in_array('Generate Token', $sub_permissions);
}
@endphp

@section('content')
<head>
<meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<div class="container-fluid">
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2">Notice Management !</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Notice Management</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Generate Notice</li>
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

                    <div class="container mt-5">
                        <!-- Trigger the modal with a button -->
                        {{-- <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#evidenceModal">Against Bank</button> --}}

                        <!-- Modal -->
                        <div id="evidenceModal" class="modal fade" role="dialog">
                            <div class="modal-dialog">

                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Select Bank</h4>
                                        <div class="invalid-feedback"></div>
                                        <button type="button" class="close" data-dismiss="modal" onclick="closeModal()">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="evidenceForm">
                                            @csrf
                                            <input type="hidden" class="form-control" id="source_type" name="source_type" value="ncrp">
                                            <input type="hidden" class="form-control" id="status" name="status" value="active">

                                            <div class="form-group">
                                                <label for="from_date">From Date:</label>
                                                <input type="date" class="form-control" id="from_date" name="from_date" >
                                            </div>

                                            <div class="form-group">
                                                <label for="to_date">To Date:</label>
                                                <input type="date" class="form-control" id="to_date" name="to_date" >
                                            </div>

                                            {{-- <div class="form-group">
                                                <label for="ack_no">Acknowledgement No.:</label>
                                                <input type="text" class="form-control" id="ack_no" name="ack_no">
                                            </div> --}}

                                            <div class="form-group">
                                                <label for="transaction_type">Transaction Type:</label><br>
                                                <select class="form-control" id="transaction_type" onchange="showDropdown()">
                                                    <option value="">--Select--</option>
                                                    <option value="bank">Bank</option>
                                                    <option value="wallet">Wallet/PG/PA</option>
                                                    <option value="merchant">Merchant</option>
                                                    <option value="insurance">Insurance</option>
                                                </select>
                                                <br>
                                            </div>

                                            <div class="form-group" id="bankDropdown" style="display:none;">
                                                <label for="bank">Bank:</label>
                                                <select class="form-control" id="bank" name="bank_id">
                                                    <option value="">--Select--</option>
                                                    @foreach ($bank as $b)
                                                        <option value="{{ $b->_id }}"> {{ $b->bank }} </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group" id="walletDropdown" style="display:none;">
                                                <label for="wallet">Wallet:</label>
                                                <select class="form-control" id="wallet" name="wallet_id">
                                                    <option value="">--Select--</option>
                                                    @foreach ($wallet as $w)
                                                        <option value="{{ $w->_id }}"> {{ $w->wallet }} </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group" id="insuranceDropdown" style="display:none;">
                                                <label for="insurance">Insurance:</label>
                                                <select class="form-control" id="insurance" name="insurance_id">
                                                    <option value="">--Select--</option>
                                                    @foreach ($insurance as $i)
                                                        <option value="{{ $i->_id }}"> {{ $i->insurance }} </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group" id="merchantDropdown" style="display:none;">
                                                <label for="merchant">Merchant:</label>
                                                <select class="form-control" id="merchant" name="merchant_id">
                                                    <option value="">--Select--</option>
                                                    @foreach ($merchant as $m)
                                                        <option value="{{ $m->_id }}"> {{ $m->merchant }} </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="notice_type">Notice Type:</label><br>
                                                <select class="form-control" id="notice_type">
                                                    <option value="">--Select--</option>
                                                    <option value="Notice U/s 94 of Bharatiya Nagarik Suraksha Sanhita, 2023 (BNSS)">Notice U/s 94 of Bharatiya Nagarik Suraksha Sanhita, 2023 (BNSS)</option>
                                                    {{-- <option value="immediate_intervention">Notice for immediate intervention to prevent cyber fraud</option> --}}
                                                </select>
                                                <br>
                                            </div>
                                            <button type="button" class="btn btn-primary" id="submitBank">Submit</button>
                                        </form>

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

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- jQuery (necessary for Toastr) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


    <script>
       $(document).ready(function(){
    // Add the CSRF token to every AJAX request
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#submitBank').click(function(){
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        var ack_no = $('#ack_no').val();
        var notice_type = $('#notice_type').val();

        var entityType = $('#transaction_type').val();

        var entityId = null;

        if (entityType === 'bank') {
            entityId = $('#bank').val();
        } else if (entityType === 'wallet') {
            entityId = $('#wallet').val();
        } else if (entityType === 'insurance') {
            entityId = $('#insurance').val();
        } else if (entityType === 'merchant') {
            entityId = $('#merchant').val();
        }

        // Validation: Either entityId or ack_no must be provided
        if ((!entityId || entityId === '') && (!ack_no || ack_no.trim() === '')) {
            toastr.error('Please provide either an Entity (Bank, Wallet, Insurance, Merchant) or an Acknowledgement Number.');
            return;
        }

        // Proceed if validation passes
        var route = notice_type === 'Notice U/s 94 of Bharatiya Nagarik Suraksha Sanhita, 2023 (BNSS)'
            ? "{{ route('generate.bank.acc.notice') }}"
            : "{{ route('generate.bank.ack.notice') }}";

        $.ajax({
            url: route,
            type: 'POST',
            data: {
                from_date: from_date,
                to_date: to_date,
                ack_no: ack_no,
                entity_id: entityId,
                notice_type: notice_type,
                entity_type: entityType
            },
            success: function(response) {
                if (response.success) {
                        toastr.success("Notice created successfully!");
                        $('#evidenceModal').modal('hide');
                        window.location.href = "{{ route('notices.index') }}";

                    } else {
                        toastr.error(response.message || "Failed to generate notice.");
                    }
                },
            error: function(xhr, status, error) {
                toastr.error('An error occurred while submitting the form.');
            }
        });
    });
});


        function showDropdown() {
            var transactionType = $('#transaction_type').val();
            $('#bankDropdown').hide();
            $('#walletDropdown').hide();
            $('#insuranceDropdown').hide();
            $('#merchantDropdown').hide();

            if (transactionType === 'bank') {
                $('#bankDropdown').show();
            } else if (transactionType === 'wallet') {
                $('#walletDropdown').show();
            } else if (transactionType === 'insurance') {
                $('#insuranceDropdown').show();
            } else if (transactionType === 'merchant') {
                $('#merchantDropdown').show();
            }
        }

        function closeModal() {
            $('#evidenceModal').modal('hide');
            $('#evidenceForm')[0].reset();
            $('#bankDropdown').hide();
            $('#walletDropdown').hide();
            $('#insuranceDropdown').hide();
            $('#merchantDropdown').hide();
        }
    </script>

    <!-- jQuery to trigger the modal on page load -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function(){
        $('#evidenceModal').modal('show');
    });
</script>

<script>
    function closeModal() {
        window.location.href = "{{ route('notices.index') }}";
    }
</script>

@endsection
