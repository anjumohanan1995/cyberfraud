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

                                @if ($errors->any())

                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
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
                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show w-100" role="alert">
                                        {{ session('error') }}
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


                            <div class="table-responsive mb-0">
                                <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="source_type">Source Type:</label>
                                                <select class="form-control" id="sourcetype" name="source_type">
                                                    @foreach($sourceTypes as $sourceType)
                                                        <option value="{{ $sourceType->id }}">{{ $sourceType->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('source_type')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <input type="hidden" name="sourcetypetext" id="sourcetypetext">

                                           <div id="cyberdomaindisplay" style="display:none"> 
                                           <div class="form-group">
                                                <label for="source_type">Case Number</label>
                                                <input type="text" class="form-control" name="case_number">
                                                @error('case_number')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="place">Letter Upload:</label>
                                                <input type="file" id="letter" name="letter" class="form-control">
                                                @error('letter')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div> 
                                           </div>

                                            <div class="form-group">
                                                <label for="place">File:</label>
                                                <input type="file" id="place" name="complaint_file" class="form-control">
                                                @error('complaint_file')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                        </div>
                                    </div>
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

<script>
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
</script>

@endsection
