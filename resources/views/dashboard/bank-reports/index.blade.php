@extends('layouts.app')
@section('content')
@php
use App\Models\RolePermission;
use Illuminate\Support\Facades\Auth;
$user = Auth::user();
            $role = $user->role;
            $permission = RolePermission::where('role', $role)->first();
            $permissions = $permission && is_string($permission->permission) ? json_decode($permission->permission, true) : ($permission->permission ?? []);
            $sub_permissions = $permission && is_string($permission->sub_permissions) ? json_decode($permission->sub_permissions, true) : ($permission->sub_permissions ?? []);
            if ($sub_permissions || $user->role == 'Super Admin') {
                $hasDailyCSVPermission = in_array('Daily Bank CSV Download', $sub_permissions);
                $hasDailyExcelPermission = in_array('Daily Bank Excel Download', $sub_permissions);
            } else{
                    $hasShowTTypePermission = $hasShowBankPermission = $hasShowFilledByPermission = $hasShowComplaintRepoPermission = $hasShowFIRLodgePermission = $hasShowStatusPermission = $hasShowSearchByPermission = $hasShowSubCategoryPermission = false;
                }

@endphp
<div class="container-fluid">
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2">Reports !</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Reports</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Bank Daily Reports</li>
                </ol>
            </nav>
        </div>
    </div>
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
                        <h4 class="card-title mg-b-10">Daily Bank Reports</h4>
                    </div>
                    <div class="main-content-body">
                        <div class="row row-sm">
                            <div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
                                <div class="card">
                                    <div class="card-body table-new">
                                        <div id="success_message" class="ajax_response" style="display: none;"></div>
                                        <div class="panel panel-primary">
                                            <div class="panel-body tabs-menu-body">
                                                <div class="tab-content">
                                                    <div class="tab-pane active" id="tabNew">
                                                        <form id="complaint-form-ncrp">
                                                            <div class="row">
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <label for="from-date-new">Date:</label>
                                                                        <input type="date" class="form-control" id="from-date-new"  value="{{ $today }}" name="from_date">
                                                                    </div>
                                                                </div>
                                                                {{-- <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <label for="to-date-new">To Date:</label>
                                                                        <input type="date" class="form-control" id="to-date-new"  value="{{ $today }}" name="to_date" onchange="setFromDatencrp()">
                                                                    </div>
                                                                </div> --}}
                                                                <div class="col-md-2 fil-btn">
                                                                    <div class="form-group">
                                                                        <button type="submit" class="btn btn-primary">Submit</button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </form>
                                                        <div class="table-responsive">
                                                            <table id="example" class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap table-striped">
                                                                <thead>
                                                                    <tr>
                                                                        <th>SL No</th>
                                                                        <th>District</th>
                                                                        <th>1930</th>
                                                                        <th>NCRP</th>
                                                                        <th>Total</th>
                                                                        <th>Actual Amount<br>Lost</th>
                                                                        <th>Actual Amount<br>Lost On <br><span id="actual_amount_lost_on" class="text-info"></span></th>
                                                                        <th>Actual Amount<br>Hold On <br><span id="actual_amount_hold_on" class="text-info"></span></th>
                                                                        <th>Hold Other<br>Than <br><span id="hold_other_than" class="text-info"></span></th>
                                                                        <th>Total Hold<br>On <br><span id="total_hold_on" class="text-info"></span></th>
                                                                        <th>Amount Lost<br>From ECO ON <br><span id="amount_lost_from_eco" class="text-info"></span></th>
                                                                        {{-- <th>Total Amount<br>LOST FROM ECO</th> --}}
                                                                        <th>Amount For<br>Pending Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <!-- Data will be dynamically added here by DataTables -->
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- main-content closed -->
                            </div>
                        </div>
                    </div>
                    <!-- container-closed -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<style>
    .fil-btn{
    padding-top: 30px;
}
    .csv-btn {
    color: #fff!important;
    background-color: #28a745!important;
    border-color: #28a745!important;
}
.excel-btn {
    color: #fff!important;
    background-color: #17a2b8!important;
    border-color: #17a2b8!important;
}
</style>
@section('scripts')
<script>
    $(document).ready(function () {
        var dateValue = $('#from-date-new').val();
            var dateObj = new Date(dateValue);
            var formattedDate = dateObj.getDate() + '-' + (dateObj.getMonth() + 1) + '-' + dateObj.getFullYear();
            document.getElementById('actual_amount_lost_on').textContent = formattedDate;
            document.getElementById('actual_amount_hold_on').textContent = formattedDate;
            document.getElementById('hold_other_than').textContent = formattedDate;
            document.getElementById('total_hold_on').textContent = formattedDate;
            document.getElementById('amount_lost_from_eco').textContent = formattedDate;

        var table = $('#example').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('bank-daily-reports.index') }}",
                data: function (d) {
                    d.from_date = $('#from-date-new').val();
                    //d.to_date = $('#to-date-new').val();
                }
            },
            dom: 'Bfrtip',
            buttons: [
                @if($hasDailyCSVPermission)
                { extend: 'csv', className: 'csv-btn', text: 'Download CSV' },
                @endif

                @if($hasDailyExcelPermission)
                { extend: 'excel', className: 'excel-btn', text: 'Download Excel' },
                @endif

            ],

            columns: [
                {
                    "data": null, "render": function (data, type, full, meta) {
                        return meta.row + 1;
                    }
         },
                { data: 'district', name: 'district' },
                { data: '1930_count', name: '1930_count' },
                { data: 'NCRP_count', name: 'NCRP_count' },
                { data: 'total', name: 'total' },
                { data: 'actual_amount', name: 'actual_amount' },
                { data: 'actual_amount_lost_on', name: 'actual_amount_lost_on' },
                { data: 'actual_amount_hold_on', name: 'actual_amount_hold_on' },
                { data: 'hold_amount_otherthan', name: 'hold_amount_otherthan' },
                { data: 'total_holds', name: 'total_holds' },
                { data: 'total_amount_lost_from_eco', name: 'total_amount_lost_from_eco' },
                { data: 'amount_for_pending_action', name: 'amount_for_pending_action' }
            ],
            order: [[0, 'desc']],
            // Add any additional options you need
        });
        $('#csvDownload, #excelDownload').on('click', function(e) {
        e.preventDefault();
        var format = $(this).attr('id') === 'csvDownload' ? 'csv' : 'excel'; // Determine format based on button clicked
        var url = "{{ route('bank-daily-reports.index') }}" + '?format=' + format + '&' + $.param({
            from_date: $('#from-date-new').val(),
            //to_date: $('#to-date-new').val(),
            current_date: $('#current_date').val(),
            bank_action_status: $('#bank_action_status').val(),
            evidence_type_ncrp: $('#evidence_type_ncrp').val(),
            search_value_ncrp: window['evidence_type_ncrp_searchValue'] || ''
        });
        window.location.href = url;
    });
        $('#complaint-form-ncrp').on('submit', function (e) {
            e.preventDefault();
            table.draw();
            var dateValue = $('#from-date-new').val();
            var dateObj = new Date(dateValue);
            var formattedDate = dateObj.getDate() + '-' + (dateObj.getMonth() + 1) + '-' + dateObj.getFullYear();
            document.getElementById('actual_amount_lost_on').textContent = formattedDate;
            document.getElementById('actual_amount_hold_on').textContent = formattedDate;
            document.getElementById('hold_other_than').textContent = formattedDate;
            document.getElementById('total_hold_on').textContent = formattedDate;
            document.getElementById('amount_lost_from_eco').textContent = formattedDate;

        });
    });
</script>
@endsection
