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
                $hasUploadOtherPermission = in_array('Upload Other Case Data Management', $sub_permissions) || $user->role == 'Super Admin';
                } else{
                    $hasUploadOtherPermission = false;
                }

@endphp

@section('content')
    <!-- container -->
    <div class="container-fluid">
        <!-- breadcrumb -->
        <div class="breadcrumb-header justify-content-between">
            <div>
                <h4 class="content-title mb-2">
                    Upload Other Case Data !
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

                                @if(session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if(session('import_errors'))
                                <div class="alert alert-danger">
                                    <h4>Import Errors</h4>
                                    <ul>
                                        @foreach(session('import_errors') as $rowIndex => $rowErrors)
                                            <li>
                                                Row {{ $rowIndex }}:
                                                <ul>
                                                    @foreach($rowErrors as $field => $errors)
                                                        @foreach($errors as $error)
                                                            <li>{{ $field }}: {{ $error }}</li>
                                                        @endforeach
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            </div>
                            @if ($hasUploadOtherPermission)
<div class=" m-4 d-flex justify-content-between">
                                <h4 class="card-title mg-b-10">
                                    Add Complaints!
                                </h4>

                            </div>

                                <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="source_type">Source Type:</label>
                                                        <select class="form-control" id="sourcetype" name="source_type" required>
                                                        @foreach($sourceTypes as $sourceType)
                                                        <option value="{{ $sourceType->id }}" @if(old('source_type') == $sourceType->id ) selected  @endif>{{ $sourceType->name }}</option>
                                                        @endforeach
                                                        </select>
                                                        {{-- @error('source_type')
                                                        <div class="text-danger">{{ $message }}</div>
                                                        @enderror --}}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="source_type">Case Number</label>
                                                        <input type="text" id="case_number" readonly  class="form-control" name="case_number">
                                                        {{-- @error('case_number')
                                                        <div class="text-danger">{{ $message }}</div>
                                                        @enderror --}}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="place">Letter Upload: <span style="color: red;">(pdf only)</span></label>
                                                        <input type="file" value="{{ old('letter') }}" id="letter" name="letter" class="form-control" required>
                                                        {{-- @error('letter')
                                                        <div class="text-danger">{{ $message }}</div>
                                                        @enderror --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="place">File:</label>
                                                        <input type="file" id="place" name="complaint_file" class="form-control" required>

                                                    </div>
                                                </div>
                                                <div class="col-md-2 justify-content-center align-self-center">
                                                    <a href="{{ route('create-website-download-template')  }}"><button type="button" class="btn btn-primary btn-sm">Website Template <br>
                                                    <i class="fa fa-download"></i></button>
                                                    </a>
                                                </div>
                                                <div class="col-md-2 justify-content-center align-self-center">
                                                    <a href="{{ route('create-socialmedia-download-template')  }}"><button type="button" class="btn btn-primary btn-sm">Social Media <br>
                                                    <i class="fa fa-download"></i></button>
                                                    </a>
                                                </div>
                                                <div class="col-md-2 justify-content-center align-self-center">
                                                    <a href="{{ route('create-mobile-download-template')  }}"><button type="button" class="btn btn-primary btn-sm">Mobile/Whatsapp <br>
                                                    <i class="fa fa-download"></i></button>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                    <div class="form-group">
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </div>
                                    </div>
                                </form>
                            @endif


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

    function getCaseNumber(){
    var source_type = $('#sourcetype').val();
    if(source_type !== ''){

        var sourcetype = $("#sourcetype option:selected").text();
        if(sourcetype !==''){
           $.ajax({
                        url: '{{ route('get.casenumber') }}',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        data: {
                            sourcetype:sourcetype,sourcetype_id:source_type
                        },
                        success: function(response) {
                            //console.log(response);
                            $("#case_number").val(response);

                        },
                        error: function(xhr, status, error) {
                            $editable.text(oldData);
                            alert(response.message);
                        }
                    });
        }
    }
    }
    getCaseNumber();

    $("#sourcetype").on('change',function(){
        getCaseNumber();
    })

});
</script>

@endsection
