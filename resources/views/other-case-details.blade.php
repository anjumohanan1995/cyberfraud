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
                                    Case Number -  {{ $case_details[0]->case_number }}
                                </h4>

                            </div>

                            <div class="table-responsive mb-0">
                                <table class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap table-striped">
                                    <thead>
                                        <tr>
                                            <th>SL No</th>
                                            <th>URL</th>
                                            <th>Domain</th>
                                            <th>Registry Details</th>
                                            <th>IP</th>
                                            <th>Registrar</th>
                                            <th>Remarks</th>
                                            <th colspan="2">ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($case_details as $case)
                                        <tr>
                                        <td> {{ $loop->iteration }} </td>
                                        <td> {{ $case->url }} </td>
                                        <td> {{ $case->domain }}  </td>
                                        <td> {{ $case->registry_details }}  </td>
                                        <td> {{ $case->ip }}  </td>
                                        <td> {{ $case->registrar }}  </td>
                                        <td> {{ $case->remarks }}  </td>
                                        <td> <a class="btn btn-success" href="{{ route('edit-others-caseData',$case->_id) }}"><i class="fa fa-edit"></i></a> </td>
                                        <td> <div class="form-check form-switch form-switch-sm d-flex justify-content-center align-items-center" dir="ltr"> <input data-id="31505240010711" onchange="confirmActivation(this)" class="form-check-input" type="checkbox" checked="" title="Deactivate">  </div> </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
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
