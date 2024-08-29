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

$hasAddSTPermission = $sub_permissions && in_array('Add Source Type', $sub_permissions) || $user->role == 'Super Admin';
$hasEditSTPermission = $sub_permissions && in_array('Edit Source Type', $sub_permissions) || $user->role == 'Super Admin';
$hasDeleteSTPermission = $sub_permissions && in_array('Delete Source Type', $sub_permissions) || $user->role == 'Super Admin';
@endphp

<!-- container -->
<div class="container-fluid">
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2">Source Type !</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Source Type Management</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Source Type
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
                        <div class="m-4 d-flex justify-content-between">
                            <h4 class="card-title mg-b-10">Add Source Type!</h4>

                            @if (session('success'))
                                <div id="success-message" class="alert alert-success alert-dismissible fade show w-100" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="col-md-9"></div>
                        </div>

                        <div class="table-responsive mb-0">
                            <form id="sourceTypeForm">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Name:</label>
                                            <input type="text" id="name" name="name" class="form-control" placeholder="Enter Source Type" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status">Status:</label>
                                            <select class="form-control" name="status" required>
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                            @error('status')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>

                            <div class="table-responsive mb-0">
                                <table id="example" class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap table-striped">
                                    <thead>
                                        <tr>
                                            <th>SL No</th>
                                            <th>NAME</th>
                                            @if($hasEditSTPermission || $hasDeleteSTPermission)
                                                <th>ACTION</th>
                                            @endif
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
        </div>
        <!-- /row -->
    </div>
    <!-- /row -->
</div>

<script>
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
        ajax: {
            url: "{{ route('get.sourcetype') }}",
            data: function(d) {
                return $.extend({}, d, {});
            }
        },
        columns: [
            { data: 'id' },
            { data: 'name' },
            @if($hasEditSTPermission || $hasDeleteSTPermission) { data: 'edit' } @endif
        ],
        order: [[0, 'desc']],
        ordering: true
    });

    $('#sourceTypeForm').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission

        $.ajax({
            url: "{{ route('sourcetype.store') }}",
            type: 'POST',
            data: $(this).serialize(), // Serialize form data
            success: function(response) {
                // Reload DataTable to show the new record
                table.ajax.reload(null, false); // false to keep the current page
                $('#success-message').html(response.success + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>').show();
                $('#sourceTypeForm')[0].reset(); // Reset the form
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });

    $(document).on('click', '.delete-btn', function() {
        var Id = $(this).data('id');
        if (confirm('Are you sure you want to delete this item?')) {
            $.ajax({
                url: '/sourcetype/' + Id,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    _method: 'DELETE'
                },
                success: function(response) {
                    $('#success-message').html(response.success + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>').show();
                    table.ajax.reload(); // Reload DataTable
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
    });
});
</script>
@endsection
