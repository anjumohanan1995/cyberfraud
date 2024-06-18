@extends('layouts.app')

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

                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show w-100" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show w-100" role="alert">
                                       @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
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
                                                        <select class="form-control" id="sourcetype" name="source_type">
                                                        @foreach($sourceTypes as $sourceType)
                                                        <option value="{{ $sourceType->id }}" @if(old('source_type') == $sourceType->id ) selected  @endif>{{ $sourceType->name }}</option>
                                                        @endforeach
                                                        </select>
                                                        @error('source_type')
                                                        <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="source_type">Case Number</label>
                                                        <input type="text" id="case_number" readonly  class="form-control" name="case_number">
                                                        @error('case_number')
                                                        <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="place">Letter Upload: <span style="color: red;">(pdf only)</span></label>
                                                        <input type="file" value="{{ old('letter') }}" id="letter" name="letter" class="form-control">
                                                        @error('letter')
                                                        <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="place">File:</label>
                                                        <input type="file" id="place" name="complaint_file" class="form-control">

                                                    </div>
                                                </div>
                                                <div class="col-md-2 justify-content-center align-self-center">
                                                    <a href="{{ route('create-download-template')  }}"><button type="button" class="btn btn-primary btn-sm">Template <br>
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
                            console.log(response);
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
