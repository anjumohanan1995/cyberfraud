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
<style>
   #success-message {
    display: none; /* Hidden by default */
}

    </style>

<div class="container-fluid">
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2">Wallet !</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Wallet Management</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Wallet
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="main-content-body">
        <div class="row row-sm">
            <div class="col-md-12 col-xl-12">
                <div class="card overflow-hidden review-project">
                    <div class="card-body">
                        <div id="hidesuccess">
                            @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show w-100" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @endif

                            </div>

                            <div id="success-message" class="alert alert-success alert-dismissible fade show w-100" role="alert" style="display: none;">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <div class="m-4 d-flex justify-content-between">
                            <h4 class="card-title mg-b-10">Add Wallet!</h4>


                            {{-- @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show w-100" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif --}}
                                {{-- <div id="success-message" class="alert alert-success alert-dismissible fade" role="alert" style="display: block;">
                                    Test success message
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>


                            @if ($errors->any())
                                <div id="error-message" class="alert alert-danger alert-dismissible fade show w-100" role="alert">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif --}}
                        </div>

                        <div class="table-responsive mb-0">
                            <form id="sourceTypeForm" action="{{ route('wallet.store') }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Name:</label>
                                            <input type="text" id="name" name="name" class="form-control" placeholder="Enter Wallet" value="{{ old('name') }}" required>
                                            <div id="name-error" class="text-danger"></div> <!-- Placeholder for name errors -->
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status">Status:</label>
                                            <select class="form-control" name="status" id="status" required>
                                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                            {{-- @if($hasEditSTPermission || $hasDeleteSTPermission) --}}
                                                <th>ACTION</th>
                                            {{-- @endif --}}
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
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    console.log('JavaScript loaded!');
    $(document).ready(function() {
    console.log('jQuery is loaded!');
});

    </script>

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
            url: "{{ route('get.wallet') }}",
            data: function(d) {
                return $.extend({}, d, {});
            }
        },
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'edit' }
        ],
        order: [[0, 'desc']],
        ordering: true
    });

    $('#sourceTypeForm').on('submit', function(e) {
    e.preventDefault(); // Prevent the default form submission
    // alert('Form submitted!'); // Should show an alert if the form is submitted

    $.ajax({
        url: "{{ route('wallet.store') }}",
        type: 'POST',
        data: $(this).serialize(), // Serialize form data
        success: function(response) {
                // alert(response)
            // console.log(response.success);
                $('#hidesuccess').hide();
                $('#example').DataTable().ajax.reload();
                $('#success-message').html(response.success + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>').show();
                // table.ajax.reload(); // Reload DataTable
                // windows.location.reload();
                },

        error: function(xhr, status, error) {
            console.error('Error response:', xhr.responseText); // Log error response

            $('#name-error').html('').hide(); // Clear previous name error
            $('#status-error').html('').hide(); // Clear previous status error

            if (xhr.responseJSON.errors) {
                $.each(xhr.responseJSON.errors, function(field, messages) {
                    var errorHtml = '<div class="text-danger">';
                    $.each(messages, function(index, message) {
                        errorHtml += '<li>' + message + '</li>';
                    });
                    errorHtml += '</div>';

                    // Display error messages below the respective input field
                    if (field === 'name') {
                        $('#name-error').html(errorHtml).show();
                    } else if (field === 'status') {
                        $('#status-error').html(errorHtml).show();
                    }
                });
            }
        }
    });
});

    $(document).on('click', '.delete-btn', function() {
        var Id = $(this).data('id');
        if (confirm('Are you sure you want to delete this item?')) {
            $.ajax({
                url: '/wallet/' + Id,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    _method: 'DELETE'
                },
                success: function(response) {
                    console.log(response);
                    $('#example').DataTable().ajax.reload();
                    $('#success-message').html(response.success + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>').show();
                    // table.ajax.reload(); // Reload DataTable
                    // windows.location.reload();
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
