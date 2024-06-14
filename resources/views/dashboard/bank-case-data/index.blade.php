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
                            </div>


                            <div class=" m-4 d-flex justify-content-between">
                                <h4 class="card-title mg-b-10">
                                    Add Bank data!
                                </h4>
                            </div>

                            <div class="table-responsive mb-0">
                                <form action="{{ route('bank-case-data.store') }}" method="POST"
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
                        </div>
                    </div>
                </div>
            </div>
            <!-- /row -->


        </div>
        <!-- /row -->
    </div>
@endsection
