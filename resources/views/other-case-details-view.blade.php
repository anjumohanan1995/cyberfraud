@extends('layouts.app')

@section('content')
    <!-- container -->
    <div class="container-fluid">
        <!-- breadcrumb -->
        <div class="breadcrumb-header justify-content-between">
            <div>
                <h4 class="content-title mb-2">
                    Other Case Data !
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="#">Other Case Data Details</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Details
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
                                    Case Number - {{ $complaint_others_by_id->case_number }}
                                </h4>
                             </div>
                            <form action="{{ route('case-data-others.update',$complaint_others_by_id->_id) }}" method="post">
                            @csrf
                            @method('PUT')
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-4">
                                            <div class="form-group">
                                            <label for="from-date">URL</label>
                                            <input type="text" value="{{ $complaint_others_by_id->url }}" class="form-control" id="url" name="url">
                                            </div>
                                            </div>

                                            <div class="col-md-4">
                                            <div class="form-group">
                                            <label for="from-date">Domain</label>
                                            <input type="text" value="{{ $complaint_others_by_id->domain }}" class="form-control" name="domain" id="domain">
                                            </div>
                                            </div>

                                            <div class="col-md-4">
                                            <div class="form-group">
                                            <label for="from-date">IP</label>
                                            <input type="text" value="{{ $complaint_others_by_id->ip }}" class="form-control" name="ip" id="ip" >
                                            </div>
                                            </div>

                                            <div class="col-md-6">
                                            <div class="form-group">
                                            <label for="from-date">Registry Details</label>
                                            <input type="text" value="{{ $complaint_others_by_id->registry_details }}" class="form-control" name="registry_details" id="registry_details" >
                                            </div>
                                            </div>

                                            <div class="col-md-6">
                                            <div class="form-group">
                                            <label for="from-date">Registrar</label>
                                            <input type="text" value="{{ $complaint_others_by_id->registrar }}" class="form-control" name="registrar" id="registrar" >
                                            </div>
                                            </div>

                                            <div class="col-md-12">
                                            <div class="form-group">
                                            <label for="from-date">Remarks</label>
                                            <textarea class="form-control" name="remarks" id="remarks">{{ $complaint_others_by_id->remarks }}</textarea>
                                            </div>
                                            </div>

                                            <div class="col-md-3">
                                            <button type="submit" class="btn btn-primary"> Update </button>
                                            </div>

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


@endsection
