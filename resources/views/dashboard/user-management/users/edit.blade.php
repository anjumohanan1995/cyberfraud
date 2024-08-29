@extends('layouts.app')

@section('content')
    <!-- container -->
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
                            Edit User
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
                                <h4 class="card-title mg-b-10">
                                    Edit User Here!
                                </h4>
                            </div>
                            <div class="table-responsive mb-0">
                                <form action="{{ route('users.update', ['user' => $data->id]) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('put')

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">Name:</label>
                                                <input type="text" id="name" name="name" class="form-control" placeholder="Enter your name" value="{{ old('name') ?: $data->name }}" required>
                                                @error('name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Email:</label>
                                                <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" value="{{ old('email') ?: $data->email }}" required>
                                                @error('email')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="role">Role:</label>
                                                <select id="role" name="role" class="form-control" required>
                                                    <option value="" selected>Select Role</option>
                                                    @foreach($roles as $role)
                                                        <option value="{{ $role->name }}" @if ($data->role == $role->name) selected @endif>{{ $role->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('role')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="old_password">Current Password:</label>
                                                    <input type="password" id="old_password" name="old_password" class="form-control" placeholder="Enter your current password">
                                                    @error('old_password')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="password">New Password:</label>
                                                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter new password">
                                                    @error('password')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                    <small class="text-muted">Leave this field empty if you don't want to change the password.</small>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="password">New Password:</label>
                                                <input type="password" id="password" name="password" class="form-control" placeholder="Enter new password">
                                                @error('password')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Leave this field empty if you don't want to change the password.</small>
                                            </div>
                                        </div> --}}
                                    </div>
                                    {{-- <div id="signature-fields" style="display: none;"> --}}
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="sign">Signature:</label>
                                                    <input type="file" id="sign" name="sign" class="form-control" placeholder="">
                                                    @error('sign')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="sign_name">Signature Name:</label>
                                                    <input type="text" id="sign_name" name="sign_name" class="form-control" placeholder="" value="{{ old('sign_name') ?: $data->sign_name }}">
                                                    @error('sign_name')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="sign_designation">Signature Designation:</label>
                                                    <input type="text" id="sign_designation" name="sign_designation" class="form-control" placeholder="" value="{{ old('sign_designation') ?: $data->sign_designation }}">
                                                    @error('sign_designation')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    {{-- </div> --}}

                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /row -->
        </div>
        <!-- /row -->
    </div>

    {{-- @section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const roleSelect = document.getElementById('role');
            const signatureFields = document.getElementById('signature-fields');

            // Function to toggle the visibility of the signature fields
            function toggleSignatureFields() {
                if (roleSelect.value === 'Super Admin') {
                    signatureFields.style.display = 'block';
                } else {
                    signatureFields.style.display = 'none';
                }
            }

            // Initial check
            toggleSignatureFields();

            // Add event listener to role select dropdown
            roleSelect.addEventListener('change', toggleSignatureFields);
        });
    </script>
    @endsection --}}
@endsection
