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

// Evidence Type Permissions
$hasAddEvidenceTypePermission = $sub_permissions && in_array('Add Evidence Type', $sub_permissions) || $user->role == 'Super Admin';
$hasEditEvidenceTypePermission = $sub_permissions && in_array('Edit Evidence Type', $sub_permissions) || $user->role == 'Super Admin';
$hasDeleteEvidenceTypePermission = $sub_permissions && in_array('Delete Evidence Type', $sub_permissions) || $user->role == 'Super Admin';
@endphp

<div class="container-fluid">
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2">Evidence Type Management</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Management</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
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
                        <!-- Evidence Type Management -->
                        <div class="m-4 d-flex justify-content-between">
                            <h4 class="card-title mg-b-10">Add Evidence Type!</h4>
                        </div>
                        <form id="addEvidenceTypeForm" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Name:</label>
                                        <input type="text" id="name" name="name" class="form-control" placeholder="Enter Evidence Type" value="{{ old('name') }}" required>
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
                    </div>
                    @if (session('success'))
                        <div id="success-message" class="alert alert-success alert-dismissible fade show w-100" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    <div class="table-responsive mb-0">
                        <table id="example" class="table table-hover table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>SL No</th>
                                    <th>Name</th>
                                    @if($hasEditEvidenceTypePermission || $hasDeleteEvidenceTypePermission)
                                        <th>Action</th>
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

<script>
    $(document).ready(function(){
        // Initialize DataTable
        var table = $('#example').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('get.evidencetype') }}",
                data: function (d) {
                    return $.extend({}, d, {});
                }
            },
            columns: [
                { data: 'id' },
                { data: 'name' },
                @if($hasEditEvidenceTypePermission || $hasDeleteEvidenceTypePermission) { data: 'edit' } @endif
            ],
            order: [0, 'desc'],
            ordering: true
        });

        // AJAX form submission for adding evidence type
        $('#addEvidenceTypeForm').submit(function(e){
            e.preventDefault();
            $.ajax({
                url: "{{ route('evidencetype.store') }}",
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    // Clear form fields and errors
                    // $('#addEvidenceTypeForm')[0].reset();
                    // $('#nameError').text('');
                    // $('#statusError').text('');

                    // // Show success message
                    // $('#success-message').html(response.success + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>').show();

                    // // Reload DataTable
                    // table.ajax.reload();

                            // Reload DataTable to show the new record
                table.ajax.reload(null, false); // false to keep the current page
                $('#success-message').html(response.success + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>').show();
                $('#sourceTypeForm')[0].reset(); // Reset the form
                },
                error: function(xhr, status, error) {
                var errors = xhr.responseJSON.errors;
                $.each(errors, function(key, value) {
                    $('#' + key).after('<div class="text-danger">' + value + '</div>');
                });
            }
            });
        });

        // Delete evidence type
        $(document).on('click', '.delete-btn', function() {
            var Id = $(this).data('id');
            if (confirm('Are you sure you want to delete this item?')) {
                $.ajax({
                    url: '/evidencetype/' + Id,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        $('#success-message').html(response.success + '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' + '<span aria-hidden="true">&times;</span>' + '</button>').show();
                        table.ajax.reload();
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
