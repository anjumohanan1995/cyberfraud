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
                $hasUploadBankActionPermission = in_array('Upload Bank Action', $sub_permissions) || $user->role == 'Super Admin';
                } else{
                    $hasUploadBankActionPermission = false;
                }

@endphp

 <style>
        /* Center the spinner */
        .spinner-container {
            position: fixed;
            top: 40%;
            left: 50%;
            z-index: 9999;
            display: none;
        }
        .spinner-active {
            display: block !important;
        }
        .blur-background {

            pointer-events: none;
            opacity: 0.5;
        }

</style>

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
                            <a href="#">Upload CaseData</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Bank CaseData Upload!
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
                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        {{ session('error') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show w-100" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                            </div>

                            @if ($hasUploadBankActionPermission)
                            <div class=" m-4 d-flex justify-content-between">
                                <h4 class="card-title mg-b-10">
                                    Add Bank data!
                                </h4>
                            </div>

                            <div class="table-responsive mb-0">
                                <form id="uploadForm" action="{{ route('bank-case-data.store') }}" method="POST" onsubmit="showSpinner()"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            {{-- <div class="form-group">
                                                <label for="source_type">Source Type:</label>
                                                <select class="form-control" id="source_type" name="source_type">
                                                    <option value="NCRP Portal"
                                                        {{ old('source_type') == 'NCRP Portal' ? 'selected' : '' }}>NCRP
                                                        Portal</option>
                                                    <option value="Public"
                                                        {{ old('source_type') == 'Public' ? 'selected' : '' }}>Public
                                                    </option>
                                                    <option value="Cyber Dome"
                                                        {{ old('source_type') == 'Cyber Dome' ? 'selected' : '' }}>Cyber
                                                        Dome</option>
                                                    <option value="Special Branch"
                                                        {{ old('source_type') == 'Special Branch' ? 'selected' : '' }}>
                                                        Special Branch</option>
                                                    <option value="Cyber Operation"
                                                        {{ old('source_type') == 'Cyber Operation' ? 'selected' : '' }}>
                                                        Cyber Operation</option>
                                                </select>
                                                @error('source_type')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div> --}}

                                            <div class="form-group">
                                                <label for="source_type">Upload Excel:</label>
                                                <input type="file" name="file" id="file" name="file">
                                                @error('file')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>



                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </form>


                            </div>
@endif

                        </div>
                    </div>
                </div>
            </div>
            <!-- /row -->

    <div id="spinnerContainer" class="spinner-container">
        <div  role="status">
            <span class="sr-only">Loading...</span>
            <img src="{{ asset('img/Loading_2.gif') }}" alt="" width='80px';height='80px'; display="block">
        </div>
        <div class="mt-2" ><b style="font-weight:1000">Uploading...</b></div>
    </div>

        </div>
        <!-- /row -->
    </div>




@endsection

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script>
        function showSpinner() {
            var spinnerContainer = document.getElementById('spinnerContainer');
            spinnerContainer.classList.add('spinner-active'); // Show spinner

            // Blur the background
            document.body.classList.add('blur-background');
        }

        // Hide spinner when page loaded
        window.addEventListener('load', function() {
            var spinnerContainer = document.getElementById('spinnerContainer');
            spinnerContainer.classList.remove('spinner-active'); // Hide spinner after page loaded

            // Unblur the background
            document.body.classList.remove('blur-background');
        });
    </script>
