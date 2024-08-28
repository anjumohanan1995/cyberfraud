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
                $hasAddSubPermissionPermission = in_array('Add Sub Permission', $sub_permissions) || $user->role == 'Super Admin';
                $hasDeleteSubPermissionPermission = in_array('Delete Sub Permission', $sub_permissions) || $user->role == 'Super Admin';
                } else{
                    $hasAddSubPermissionPermission = $hasDeleteSubPermissionPermission = false;
                }

@endphp

@section('content')
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
                            <a href="#">User Management</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Permission
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
                            <div class=" ">

                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show w-100" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                                {{-- @if ($hasAddSubPermissionPermission)
<h4 class="card-title mg-b-10">
                                    Add Sub Permission Here!
                                </h4>
                                @endif


                            </div>
                            @if ($hasAddSubPermissionPermission)
                            <div class="table-responsive mb-0">
                                <form action="{{ route('subpermissions.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="permission_id" value={{ $subpermission->id }}>
                                    <div class="form-group col-md-6">
                                        <label for="name">Name:</label>
                                        <input type="text" id="name" name="sub_permission" class="form-control" placeholder="Enter Sub Permission" value="{{ old('sub_permission') }}" required>
                                        @error('sub_permission')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </form>

                            </div>
@endif --}}

                            <h3>Existing Subpermissions</h3>
                            <table class="table"  class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap table-striped">
                                <thead>
                                    <tr>
                                        <th>Subpermission Name</th>
                                         {{-- @if ($hasDeleteSubPermissionPermission)
<th>Action</th>
                                         @endif --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($subpermissions))
@foreach($subpermissions as $detail )
                                    <tr>
                                        <td>{{ $detail }}</td>
                                        {{-- @if ($hasDeleteSubPermissionPermission)
                                         <td>
                                            <!-- Add a delete button with a form -->
                                            <form action="{{ route('subpermissions.destroy',$detail) }}" method="POST">
                                                @csrf
                                              <input type="hidden" name="permission_id" value={{ $subpermission->id }}>
                                                <button type="submit" class="btn btn-danger">Delete</button>
                                            </form>
                                        </td>
                                        @endif --}}
                                    </tr>
                                    @endforeach
                                    @endif

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /row -->


        </div>
        <!-- /row -->
    </div>
@endsection
