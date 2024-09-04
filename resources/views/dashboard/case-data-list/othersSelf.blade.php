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
                $hasOthersSelfAssign = in_array('Show Others Self Assign Button', $sub_permissions);
                } else{
                    $hasShowTTypePermission = $hasShowBankPermission = $hasShowFilledByPermission = $hasShowComplaintRepoPermission = $hasShowFIRLodgePermission = $hasShowStatusPermission = $hasShowSearchByPermission = $hasShowSubCategoryPermission = false;
                }

@endphp
@section('content')
    <!-- container -->
    <div class="container-fluid">
        <!-- breadcrumb -->
        <div class="breadcrumb-header justify-content-between">
            <div>
                <h4 class="content-title mb-2">
                    Other Case Data !
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
                                <div id="alert_ajaxx" style="display:none">

                                </div>
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

                            </div>

                            <form id="complaint-form">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="caseNumber">Case Number</label>
                                            <input type="text" class="form-control" id="caseNumber" name="casenumber">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="url">Url\Mobile</label>
                                            <input type="text" class="form-control" id="url" name="url">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="domain">Domain\Post\Profile</label>
                                            <input type="text" class="form-control" id="domain" name="domain">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="registrar">Registrar</label>
                                            <input type="text" class="form-control" id="registrar" name="registrar">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="ip">IP\Modus Keyword</label>
                                            <input type="text" class="form-control" id="ip" name="ip">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="source_type">Source Type</label>
                                            <select id="source_type" name="source_type" class="form-control">
                                                <option value="">Select Source Type</option>
                                                @foreach($source as $sources)
                                                    <option value="{{ $sources->_id }}">{{ $sources->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button type="submit" id="filter" class="btn btn-primary">Submit</button> <!-- Make sure this is submit -->
                                    </div>
                                </div>
                            </form>



                            <div class="table-responsive mb-0">
                                <table id="complaints"
                                    class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap table-striped">
                                    <thead>
                                        <tr>
                                            <th>SL No</th>
                                            <th>Source type</th>
                                            <th>Case Number</th>
                                            <th>URL\ Mobile</th>
                                            <th>Domain\ Post\ Profile</th>
                                            <th>IP\ Modus Keyword</th>
                                            <th>Registrar</th>
                                            <th>Remarks</th>
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
            <div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="statusModalLabel">Update Case Status</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" data-id="" id="complaint-id">
                            <div class="form-group">
                                <label for="complaint-status">Status:</label>
                                <select id="complaint-status" class="form-control">
                                    <option value="Started">Started</option>
                                    <option value="Ongoing">Ongoing</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="submitStatus()">Submit</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- /row -->
    </div>
    <link rel="stylesheet" href="{{ asset('css/toastr.min.css') }}">

    <script src="{{ asset('js/toastr.js') }}"></script>

     <script>
        $(document).ready(function(){
            var table = $('#complaints').DataTable({
                stateSave: true,
                processing: true,
                serverSide: true,

                buttons: [
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5',
                    'pdfHtml5'
                ],
                "ajax": {
                    "url": "{{ route('get.datalist.others')}}",
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
                        data: 'case_number'
                    },

                    {
                        data: 'url'
                    },

                    {
                        data: 'domain'
                    },
                    {
                        data: 'ip'
                    },
                    {
                        data:'registrar'
                    },
                    {
                        data: 'remarks'
                    },


                ],
                "order": [0, 'desc'],
                'ordering': true
            });

        });

// self Assign code start


        function upStatus(caseNo) {
           // alert($(ackno).data('id'));
            $('#complaint-id').val($(caseNo).data('id'));
            $('#statusModal').modal('show');
        }

        function submitStatus() {
            var caseNo = $('#complaint-id').val();
            var status = $('#complaint-status').val();

            $.ajax({
                url: '/update-complaint-status-others',
                type: 'POST',
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: JSON.stringify({
                    caseNo: caseNo,
                    status: status
                }),
                success: function(response) {
                    alert(response.message);
                    $('#complaints').DataTable().ajax.reload();
                    $('#statusModal').modal('hide');

                },
                error: function(xhr) {
                    alert("Error: " + xhr.responseJSON.message);
                }
            });
        }
        function selfAssign(caseNo) {
            //  alert("dsf");
            var user_id = '{{ Auth::user()->id }}';
            var case_id = $(caseNo).data('id');
           //alert(case_id);
            $.ajax({
                type: "GET",
                dataType: "json",
                url: 'assignedToOthers',
                data: {
                    'userid': user_id,
                    'caseNo': case_id
                },
                success: function(data) {
                    //console.log(data.status)
                    //toastr.success(data.status, 'Success!');
                $('#example').DataTable().ajax.reload();
                }
            });
        }

// self Assign code end

    $(document).ready(function() {
    // Initialize the DataTable first if not already done
    var table = $('#complaints').DataTable();

    // Bind the submit event handler to the form
    $('#complaint-form').on('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        // Get form values
        var casenumber = $("#caseNumber").val();
        var urlValue = $("#url").val(); // Rename variable to avoid conflict
        var domain = $("#domain").val();
        var registrar = $("#registrar").val();
        var ip = $("#ip").val();
        var source_type = $("#source_type").val();

        // Construct the URL with query parameters
        var requestUrl = "{{ route('get.datalist.others') }}?casenumber=" + casenumber +
                         "&url=" + urlValue + "&domain=" + domain +
                         "&registrar=" + registrar + "&ip=" + ip + "&source_type=" + source_type;

        // Reload DataTable with new data based on selected filters
        table.ajax.url(requestUrl).load(function(json) {
            console.log("Data loaded!"); // Confirm DataTable load
        });
    });
});



// $("#filter").click(function() {

// var casenumber = $('#caseNumber').val();
// var url = $('#url').val();
// var domain = $('#domain').val();
// var registrar = $('#registrar').val();
// var ip = $('#ip').val();

// // if ($.fn.DataTable.isDataTable('#complaints')) {
// //         $('#complaints').DataTable().destroy(); // Destroy old instance
// //     }

// // Check if DataTable is already initialized
// // if ($.fn.DataTable.isDataTable('#complaints')) {
// //     $('#complaints').DataTable().destroy(); // Destroy old instance
// //     $('#complaints').empty(); // Clear table content to avoid data duplication
// // }

// var table = $('#complaints').DataTable({
//     processing: true,
//     serverSide: true,
//     buttons: [
//         'copyHtml5',
//         'excelHtml5',
//         'csvHtml5',
//         'pdfHtml5'
//     ],
//     ajax: {
//         url: "{{ route('get.datalist.others') }}",
//         data: function(d) {
//             return $.extend({}, d, {
//                 "casenumber": casenumber,
//                 "url": url,
//                 "domain": domain,
//                 "registrar": registrar,
//                 "ip": ip
//             });
//         }
//     },
//     columns: [
//         { data: 'id' },
//         { data: 'source_type' },
//         { data: 'case_number' },
//         { data: 'url' },
//         { data: 'domain' },
//         { data: 'ip' },
//         { data: 'registrar' },
//         { data: 'remarks' }
//     ],
//     order: [[0, 'desc']], // Ensure the correct syntax for ordering
//     ordering: true
// });
// });

</script>


@endsection
