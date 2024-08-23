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
                $hasUploadPrimaryPermission = in_array('Upload Primary Data', $sub_permissions) || $user->role == 'Super Admin';
                } else{
                    $hasUploadPrimaryPermission = false;
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

#loader {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: 10px;
}

.dot {
    width: 8px;
    height: 8px;
    margin: 0 4px;
    background-color: #fff;
    border-radius: 50%;
    animation: blink 1.4s infinite both;
}

.dot:nth-child(2) {
    animation-delay: 0.2s;
}

.dot:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes blink {
    0%, 100% {
        opacity: 0;
    }
    50% {
        opacity: 1;
    }
}

</style>

@section('content')
    <!-- container -->
    <div class="container-fluid">
        <!-- breadcrumb -->
        <div class="breadcrumb-header justify-content-between">
            <div>
                <h4 class="content-title mb-2">
                    Upload NCRP Case Data !
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="#">Import Complaints</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Import
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

                                @if ($errors->any())

                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="alert alert-danger alert-dismissible fade show w-100" role="alert"  style="display:none" id="erroralert">
                                    <ul id="errors">

                                    </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="alert alert-success alert-dismissible fade show w-100" id="sucessalert" style="display:none" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>

                                @if (session()->has('upload_id'))
                                <input type="hidden" id="upload_id" value="{{ session('upload_id') }}">

                                @endif

                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show w-100" id="success-alert" role="alert">
                                        {{ session('success') }}<div id="loader" class="d-none">
                                        <div class="dot"></div>
                                        <div class="dot"></div>
                                        <div class="dot"></div>
                                        </div>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show w-100" role="alert">
                                        {{ session('error') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            @if ($hasUploadPrimaryPermission)


                            <div class=" m-4 d-flex justify-content-between">
                                <h4 class="card-title mg-b-10">
                                    Add Complaints!
                                </h4>

                            </div>


                            <div class="table-responsive mb-0">
                                <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data" onsubmit="showSpinner()">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">

                                            <input type="hidden" id="sourcetype" value="NCRP" name="source_type">

                                            {{-- <input type="hidden" name="sourcetypetext" id="sourcetypetext"> --}}

                                            <div class="form-group">
                                                <label for="place">File:</label>
                                                <input type="file" id="place" name="complaint_file">
                                                @error('complaint_file')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                        </div>
                                    </div>
                                    <button type="submit" id="submit" class="btn btn-primary">Submit</button>
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

{{-- <script>
$(document).ready(function(){
    $('#sourcetype').on('change', function() {
    var sourcetype = $(this).find('option:selected').text();
    if(sourcetype == 'Cyber Domain'){
        $('#sourcetypetext').val('Cyber Domain');
       $('#cyberdomaindisplay').show();
    }
    else{
        $('#cyberdomaindisplay').hide();
    }
});
});
</script> --}}

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

<script>
$(document).ready(function() {

    @if (session('redirected'))

    <?php session()->forget('redirected'); ?>
    function executeAjaxCall() {
        var uploadId = $("#upload_id").val();

        $.ajax({
            url: '/show-upload-errors/' + uploadId,
            method: 'GET',
            success: function(data){
                $('#errors').html('');

                if (data.errors.length > 0) {
                    $('#erroralert').show();
                    $("#success-alert").hide();
                    $('#loader').addClass('d-none');
                    data.errors.forEach(error => {
                        $('#errors').append('<li>' + error.error + '</li>');
                    });
                } else {
                    $('#erroralert').hide();
                    $("#success-alert").show();
                    $('#loader').removeClass('d-none');
                }
            }
        });
    }

    // Call executeAjaxCall function every 5 seconds
    setInterval(executeAjaxCall, 2000);
    @endif
})

</script>

