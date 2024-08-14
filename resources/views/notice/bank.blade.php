@extends('layouts.app')

@php
use App\Models\RolePermission;
use Illuminate\Support\Facades\Auth;

$user = Auth::user();
$role = $user->role;
$permission = RolePermission::where('role', $role)->first();
$permissions = $permission && is_string($permission->permission) ? json_decode($permission->permission, true) : ($permission->permission ?? []);
$sub_permissions = $permission && is_string($permission->sub_permissions) ? json_decode($permission->sub_permissions, true) : ($permission->sub_permissions ?? []);
if ($sub_permissions || $user->role == 'Super Admin') {
    $hasShowESTFPermission = in_array('Show Evidence Source Type Filter', $sub_permissions);
    $hasShowETFPermission = in_array('Show Evidence Type Filter', $sub_permissions);
    $hasShowStatusFPermission = in_array('Show Notice Status Filter', $sub_permissions);
    $hasShowNoticeTypePermission = in_array('Show Notice Type Filter', $sub_permissions);
    $hasGenerateTokenPermission = in_array('Generate Token', $sub_permissions);
} else{
    $hasShowTTypePermission = $hasShowBankPermission = $hasShowFilledByPermission = $hasShowComplaintRepoPermission = $hasShowFIRLodgePermission = $hasShowStatusPermission = $hasShowSearchByPermission = $hasShowSubCategoryPermission = false;
}
@endphp
@section('content')
<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<!-- Custom Styles -->
<style>
    .toast-success {
        background-color: #4CAF50 !important;
    }
    .toast-success .toast-title, .toast-success .toast-message {
        color: #FFFFFF !important;
    }
    .toast-error {
        background-color: #df370d !important;
    }
    .toast-error .toast-title, .toast-error .toast-message {
        color: #FFFFFF !important;
    }
</style>
<div class="container-fluid">
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2">Hi, welcome back!</h4>
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
    <div id="error-message" style="display: none; color: red;"></div>

    <!-- Trigger the modal with a button -->
    <button type="button" class="btn btn-info btn-lg" data-toggle="modal" >Bank Account</button>

    <!-- Modal -->
    <div id="evidenceModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Select Bank/Wallet/Merchant/Insurance Name</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="evidenceForm">
                        <input type="hidden" class="form-control" id="source_type" name="source_type" value="ncrp">
                        <input type="hidden" class="form-control" id="status" name="status" value="active">

                        <div class="form-group">
                            <label for="from_date">From Date:</label>
                            <input type="date" class="form-control" id="from_date" name="from_date" required>
                        </div>

                        <div class="form-group">
                            <label for="to_date">To Date:</label>
                            <input type="date" class="form-control" id="to_date" name="to_date" required>
                        </div>

                        <div class="form-group">
                            <label for="type">Transaction Type:</label><br>
                            <select class="form-control" id="type" onchange="showDropdown()" required>
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
                                @foreach ($bank as $bank)
                                    <option value="{{ $bank->_id }}"> {{ $bank->bank }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group" id="walletDropdown" style="display:none;">
                            <label for="wallet">Wallet:</label>
                            <select class="form-control" id="wallet" name="wallet_id">
                                <option value="">--Select--</option>
                                @foreach ($wallet as $wallet)
                                    <option value="{{ $wallet->_id }}"> {{ $wallet->wallet }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group" id="insuranceDropdown" style="display:none;">
                            <label for="insurance">Insurance:</label>
                            <select class="form-control" id="insurance" name="insurance_id">
                                <option value="">--Select--</option>
                                @foreach ($insurance as $insurance)
                                    <option value="{{ $insurance->_id }}"> {{ $insurance->insurance }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group" id="merchantDropdown" style="display:none;">
                            <label for="merchant">Merchant:</label>
                            <select class="form-control" id="merchant" name="merchant_id">
                                <option value="">--Select--</option>
                                @foreach ($merchant as $merchant)
                                    <option value="{{ $merchant->_id }}"> {{ $merchant->merchant }} </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="button" class="btn btn-primary" id="submitBank">Submit</button>
                    </form>
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

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function(){
        $('#submitBank').click(function(){
            var source_type = $('#source_type').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            var status = $('#status').val();

            // Determine the selected entity type and ID
            var entityType = $('#type').val();
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

            if (!from_date || !to_date || !entityType || !entityId) {
                toastr.error('Please fill out all required fields.');
                return; // Stop the submission
            }

            $.ajax({
                url: "{{ route('generate.mule.notice') }}", // Route to generate notice
                type: "POST",
                data: {
                    source_type: source_type,
                    from_date: from_date,
                    to_date: to_date,
                    entity_id: entityId,
                    entity_type: entityType,
                    status: status,
                    _token: "{{ csrf_token() }}"
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
                error: function(xhr) {
                    toastr.error("No data found for the given criteria.");
                }
            });
        });
    });

    function showDropdown() {
        var entityType = $('#type').val();
        $('#bankDropdown, #walletDropdown, #insuranceDropdown, #merchantDropdown').hide();

        if (entityType === 'bank') {
            $('#bankDropdown').show();
        } else if (entityType === 'wallet') {
            $('#walletDropdown').show();
        } else if (entityType === 'insurance') {
            $('#insuranceDropdown').show();
        } else if (entityType === 'merchant') {
            $('#merchantDropdown').show();
        }
    }
</script>

                </div>
            </div>
        </div>
    </div>
    <!-- main-content-body -->
</div>
@endsection
