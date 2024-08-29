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
                $hasNCRPCSVPermission = in_array('NCRP CSV Download', $sub_permissions);
                $hasOthersCSVPermission = in_array('Other CSV Download', $sub_permissions);
                $hasNCRPExcelPermission = in_array('NCRP Excel Download', $sub_permissions);
                $hasOthersExcelPermission = in_array('Other Excel Download', $sub_permissions);
            } else{
                    $hasShowTTypePermission = $hasShowBankPermission = $hasShowFilledByPermission = $hasShowComplaintRepoPermission = $hasShowFIRLodgePermission = $hasShowStatusPermission = $hasShowSearchByPermission = $hasShowSubCategoryPermission = false;
                }

@endphp
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

/* Style DataTables buttons */
.dt-buttons {
    padding-left: 250px; /* Adjust padding */
    padding-bottom: 10px;
    margin-top:10px;
    border-radius: 5px;
}

/* Style individual buttons */
.dt-button {
    margin-right: 5px;
    border: 1px solid #ccc;
    background-color: #fff;
    color: #333;
    border-radius: 3px;
    padding: 3px 8px; /* Adjust padding */
    font-size: 5px; /* Adjust font size */
}

/* Style when button is hovered */
.dt-button:hover {
    background-color: #e0e0e0;
}

/* Style when button is active */
.dt-button.active {
    background-color: #ccc;
}

</style>
<link rel="stylesheet" href="path_to_bootstrap_css">
<link rel="stylesheet" href="path_to_font_awesome">


    <!-- container -->
    <div class="container-fluid">
        <!-- breadcrumb -->
        <div class="breadcrumb-header justify-content-between">
            <div>
                <h4 class="content-title mb-2">Reports !</h4>
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
                                                                            <input type="date" class="form-control" id="to-date-new" name="to_date" onchange="setFromDatencrp()">
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
                                                                            <label for="evidence_type_ncrp">Evidence Type:</label>
                                                                            <select class="form-control" id="evidence_type_ncrp" name="evidence_type_ncrp" onchange="showTextBox('evidence_type_ncrp')">
                                                                                <option value="">--select--</option>
                                                                                @foreach($evidenceTypes as $evidenceType)
                                                                                    <option value="{{ $evidenceType->id }}">{{ $evidenceType->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            <div id="searchBoxContainer_evidence_type_ncrp"></div>
                                                                            @error('evidence_type_ncrp')
                                                                                <div class="text-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12 text-right">
                                                                        <button type="submit" class="btn btn-primary">Submit</button>
                                                                        <!-- CSV Download Button -->
                                                                        @if($hasNCRPCSVPermission)<a href="#" class="btn btn-success" id="csvDownload">Download CSV</a>@endif
                                                                        <!-- Excel Download Button -->
                                                                        @if($hasNCRPExcelPermission)<a href="#" class="btn btn-info" id="excelDownload">Download Excel</a>@endif
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
                                                                            {{-- <th>Current Status</th> --}}
                                                                            <th>Date of Action</th>
                                                                            <th>Action Taken By Bank</th>
                                                                            <th>Action Taken By Name</th>
                                                                            <th>Evidence Type</th>
                                                                            <th>Url</th>
                                                                            {{-- <th>Bank Action</th> --}}
                                                                            {{-- <th>Action</th> --}}
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
                                                                            <input type="date" class="form-control" id="to-date-others" name="to_date" onchange="setFromDateothers()">
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
                                                                            <label for="evidence_type_others">Evidence Type:</label>
                                                                            <select class="form-control" id="evidence_type_others" name="evidence_type_others" onchange="showTextBox('evidence_type_others')">
                                                                                <option value="">--select--</option>
                                                                                @foreach($evidenceTypes as $evidenceType)
                                                                                    <option value="{{ $evidenceType->name}}">{{ $evidenceType->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            <div id="searchBoxContainer_evidence_type_others"></div>
                                                                            @error('evidence_type_others')
                                                                                <div class="text-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>


                                                                    {{-- <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label for="evidence_type_ncrp">Evidence Type:</label>
                                                                            <select class="form-control" id="evidence_type_ncrp" name="evidence_type_ncrp" onchange="showTextBox('evidence_type_ncrp')">
                                                                                <option value="">--select--</option>
                                                                                @foreach($evidenceTypes as $evidenceType)
                                                                                    <option value="{{ $evidenceType->id }}">{{ $evidenceType->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            <div id="searchBoxContainer_evidence_type_ncrp"></div>
                                                                            @error('evidence_type_ncrp')
                                                                                <div class="text-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div> --}}

                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12 text-right">
                                                                        <button type="submit" class="btn btn-primary">Submit</button>
                                                                        <!-- CSV Download Button -->
                                                                        @if($hasOthersCSVPermission)<a href="#" class="btn btn-success" id="csvDownloadothers">Download CSV</a>@endif
                                                                        <!-- Excel Download Button -->
                                                                        @if($hasOthersExcelPermission)<a href="#" class="btn btn-info" id="excelDownloadothers">Download Excel</a>@endif
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
                                                                            <th>Evidence Type</th>
                                                                            <th>URL / Mobile No.</th>
                                                                            <th>Domain / Post / Profile</th>
                                                                            <th>IP / Modus Keyword</th>
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
            ajax: {
                url: "{{ route('get.datalist.ncrp') }}",
                data: function(d) {
                    return $.extend({}, d, {
                        format: 'ncrp', // Add format parameter
                        from_date: $('#from-date-new').val(),
                        to_date: $('#to-date-new').val(),
                        current_date: $('#current_date').val(),
                        bank_action_status: $('#bank_action_status').val(),
                        evidence_type_ncrp: $('#evidence_type_ncrp').val(),
                        search_value_ncrp: window['evidence_type_ncrp_searchValue'] || ''
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
                // { data: 'current_status' },
                { data: 'date_of_action' },
                { data: 'action_taken_by_bank' },
                { data: 'action_taken_by_name' },
                { data: 'evidence_type' },
                { data: 'url' },
                // { data: '' }
            ],
            order: [0, 'desc'],
            ordering: true
        });

 // Click event handler for Download buttons
 $('#csvDownload, #excelDownload').on('click', function(e) {
        e.preventDefault();
        var format = $(this).attr('id') === 'csvDownload' ? 'csv' : 'excel'; // Determine format based on button clicked
        var url = "{{ route('get.datalist.ncrp') }}" + '?format=' + format + '&' + $.param({
            from_date: $('#from-date-new').val(),
            to_date: $('#to-date-new').val(),
            current_date: $('#current_date').val(),
            bank_action_status: $('#bank_action_status').val(),
            evidence_type_ncrp: $('#evidence_type_ncrp').val(),
            search_value_ncrp: window['evidence_type_ncrp_searchValue'] || ''
        });
        window.location.href = url;
    });

        // Initialize DataTable for #example1
        var tableReturned = $('#example1').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('get.datalist.othersourcetype') }}",
                data: function(d) {
                    return $.extend({}, d, {
                        format: 'others', // Add format parameter
                        from_date: $('#from-date-others').val(),
                        to_date: $('#to-date-others').val(),
                        current_value: $('#current_value').val(),
                        evidence_type_others: $('#evidence_type_others').val(),
                        search_value_others: window['evidence_type_others_searchValue'] || ''
                    });
                }
            },
            columns: [
                { data: 'id' },
                { data: 'source_type' },
                { data: 'case_number' },
                { data: 'evidence_type' },
                { data: 'url' },
                { data: 'domain' },
                { data: 'ip' },
                { data: 'registrar' },
                { data: 'remarks' }
            ],
            order: [0, 'desc'],
            ordering: true
        });

         // Click event handler for Download buttons
 $('#csvDownloadothers, #excelDownloadothers').on('click', function(e) {
        e.preventDefault();
        var format = $(this).attr('id') === 'csvDownloadothers' ? 'csv' : 'excel'; // Determine format based on button clicked
        var url = "{{ route('get.datalist.othersourcetype') }}" + '?format=' + format + '&' + $.param({
            from_date: $('#from-date-others').val(),
            to_date: $('#to-date-others').val(),
            current_value: $('#current_value').val(),
            evidence_type_others: $('#evidence_type_others').val(),
            search_value_others: window['evidence_type_others_searchValue'] || ''
        });
        window.location.href = url;
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

<script>
    function showTextBox(selectId) {

        var selectedValue = document.getElementById(selectId).value;

        // Hide any previously displayed search boxes
        var allSearchBoxes = document.querySelectorAll('[id^="searchBox_"]');
        allSearchBoxes.forEach(function(box) {
            box.style.display = "none";
        });

        // If the selected value is empty, do not show any search box and clear the search values
        if (selectedValue === "") {
            window[selectId + '_searchValue'] = '';
            window[selectId + '_selectedValue'] = '';
            return;
        }

        // Check if a search box already exists for this dropdown
        var existingSearchBox = document.getElementById("searchBox_" + selectId + "_" + selectedValue);
        if (existingSearchBox) {
            // If a search box exists, show it
            existingSearchBox.style.display = "block";
        } else {
            // If a search box doesn't exist, create and append a new one
            var searchBoxContainer = document.getElementById("searchBoxContainer_" + selectId);
            var newSearchBox = document.createElement("div");
            newSearchBox.id = "searchBox_" + selectId + "_" + selectedValue;
            newSearchBox.innerHTML = '<input type="text" class="form-control" placeholder="Search url..." oninput="setSearchValue(\'' + selectId + '\', \'' + selectedValue + '\', this.value)">';
            searchBoxContainer.appendChild(newSearchBox);
        }
    }

    function setSearchValue(selectId, selectedValue, searchValue) {
        window[selectId + '_searchValue'] = searchValue;
        window[selectId + '_selectedValue'] = selectedValue;
    }
</script>


<script>
    function setFromDateothers() {
        const toDateElement = document.getElementById('to-date-others');
        const fromDateElement = document.getElementById('from-date-others');

        const toDateValue = toDateElement.value;
        if (toDateValue) {
            const toDate = new Date(toDateValue);
            toDate.setMonth(toDate.getMonth() - 1);
            const month = ("0" + (toDate.getMonth() + 1)).slice(-2);
            const day = ("0" + toDate.getDate()).slice(-2);
            const year = toDate.getFullYear();
            const fromDateValue = `${year}-${month}-${day}`;
            fromDateElement.value = fromDateValue;
        }
    }
</script>

<script>
    function setFromDatencrp() {
        const toDateElement = document.getElementById('to-date-new');
        const fromDateElement = document.getElementById('from-date-new');

        const toDateValue = toDateElement.value;
        if (toDateValue) {
            const toDate = new Date(toDateValue);
            toDate.setMonth(toDate.getMonth() - 1);
            const month = ("0" + (toDate.getMonth() + 1)).slice(-2);
            const day = ("0" + toDate.getDate()).slice(-2);
            const year = toDate.getFullYear();
            const fromDateValue = `${year}-${month}-${day}`;
            fromDateElement.value = fromDateValue;
        }
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    const startDateInput = document.getElementById('from-date-new');
    const endDateInput = document.getElementById('to-date-new');

    const today = new Date();
    const endDate = today.toISOString().split('T')[0]; // Current date
    today.setMonth(today.getMonth() - 1);
    const startDate = today.toISOString().split('T')[0]; // Date one month ago

    startDateInput.value = startDate;
    endDateInput.value = endDate;
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    const startDateInput = document.getElementById('from-date-others');
    const endDateInput = document.getElementById('to-date-others');

    const today = new Date();
    const endDate = today.toISOString().split('T')[0]; // Current date
    today.setMonth(today.getMonth() - 1);
    const startDate = today.toISOString().split('T')[0]; // Date one month ago

    startDateInput.value = startDate;
    endDateInput.value = endDate;
});
</script>


@endsection
