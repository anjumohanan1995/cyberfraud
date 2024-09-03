@php
    use App\Models\RolePermission;
    use Illuminate\Support\Facades\Auth;
    $user = Auth::user();
    $role = $user->role;
    $permission = RolePermission::where('role', $role)->first();
    $permissions = $permission && is_string($permission->permission) ? json_decode($permission->permission, true) : ($permission->permission ?? []);
    $sub_permissions = $permission && is_string($permission->sub_permissions) ? json_decode($permission->sub_permissions, true) : ($permission->sub_permissions ?? []);
    if ($sub_permissions || $user->role == 'Super Admin') {
    $hasAddUserPermission = in_array('Add User', $sub_permissions) || $user->role == 'Super Admin';
    $hasEditUserPermission = in_array('Edit User', $sub_permissions) || $user->role == 'Super Admin';
    $hasDeleteUserPermission = in_array('Delete User', $sub_permissions) || $user->role == 'Super Admin';
    } else{
        $hasAddUserPermission = false;
        $hasEditUserPermission = false;
        $hasDeleteUserPermission = false;
    }

@endphp

@extends('layouts.app')

@section('content')
    <!-- container -->

    <style>
        /* CSS for toggle switch */
.switch {
  position: relative;
  display: inline-block;
  width: 34px;
  height: 20px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: .4s;
  border-radius: 20px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 12px;
  width: 12px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}

input:checked + .slider {
  background-color: #4CAF50;
}

input:focus + .slider {
  box-shadow: 0 0 1px #4CAF50;
}

input:checked + .slider:before {
  transform: translateX(14px);
}

    </style>
    <div class="container-fluid">
        <!-- breadcrumb -->
        <div class="breadcrumb-header justify-content-between">
            <div>
                <h4 class="content-title mb-2">
                    User Management !
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="#">User Management</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Users
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

                                <div class="alert alert-success-one alert-dismissible fade show w-100" role="alert" style="display:none">

                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                            </div>
                            <div class=" m-4 d-flex justify-content-between">
                                <h4 class="card-title mg-b-10">
                                    All Users
                                </h4>
                            </div>
                            <div >
                                <label for="from-date-new">Status:</label>
                                <select name="status_filter" id="status_filter" class="form-group col-2">
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <div class="col-md-1 col-6 text-center" style="float:right">
                                    @if ($hasAddUserPermission)
                                        <div class="task-box primary  mb-0">
                                            <a class="text-white" href="{{ route('users.create') }}">
                                                <p class="mb-0 tx-12">Add </p>
                                                <h3 class="mb-0"><i class="fa fa-plus"></i></h3>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            </div>
                            <div class="table-responsive mb-0">
                                <table id="example"
                                    class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap table-striped">
                                    <thead>
                                        <tr>
                                            <th>SL No</th>
                                            <th>NAME</th>
                                            <th>EMAIL</th>
                                            <th>ROLE</th>
                                            @if ($hasEditUserPermission || $hasDeleteUserPermission)
                                                <th>ACTION</th>
                                            @endif
                                            <th>STATUS</th>
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
        // Initialize DataTable
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
                url: "{{ route('get.users-list') }}",
                data: function(d) {
                    // Send the status filter to the server
                    d.status_filter = $('#status_filter').val();
                }
            },
            columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'email' },
                { data: 'role' },
                @if ($hasEditUserPermission || $hasDeleteUserPermission)
                    { data: 'edit' },
                @endif
                { data: 'status' },

            ],
            "order": [0, 'desc'],
            'ordering': true
        });

        // Redraw table on status filter change
        $('#status_filter').change(function() {
            table.draw();
        });

        // Handle toggle button change
        $(document).on('change', '.status-toggle', function() {
            var status = $(this).is(':checked') ? 'active' : 'inactive'; // Check toggle state
            var userId = $(this).data('id');

            $.ajax({
                url: '/users/' + userId + '/update-status', // Define a route for updating user status
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    status: status,
                    _method: 'PATCH' // Override method to PATCH
                },
                success: function(response) {
                    // Handle success response
                    $('.alert-success-one').html('<div class="alert alert-success alert-dismissible fade show w-100" role="alert">' +
                        response.success +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span>' +
                        '</button>' +
                        '</div>').show();

                    // Optionally reload the table to reflect changes
                    table.draw();
                },
                error: function(xhr, status, error) {
                    // Handle error response
                    console.error(xhr.responseText)
                }
            });
        });

        // Handle delete button click
        $(document).on('click', '.delete-btn', function() {
            var Id = $(this).data('id');
            if (confirm('Are you sure you want to delete this item?')) {
                $.ajax({
                    url: '/users/' + Id,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        // Handle success response
                        $('.alert-success-one').html('<div class="alert alert-success alert-dismissible fade show w-100" role="alert">' +
                            response.success +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                            '<span aria-hidden="true">&times;</span>' +
                            '</button>' +
                            '</div>').show();

                        // Reload the table
                        table.draw();
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        console.error(xhr.responseText);
                    }
                });
            }
        });
    });
    </script>

@endsection
