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
                            <a href="#">Case Data Management</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Case Data
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
                                <div id="alert_ajaxx" style="display:none">

                                </div>
                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show w-100" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                                <div class="alert alert-success-one alert-dismissible fade show w-100" role="alert"
                                    style="display:none">

                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>
                            <div class=" m-4 d-flex justify-content-between">
                                <h4 class="card-title mg-b-10">
                                    All Case Data
                                </h4>

                            </div>

                            <form id="complaint-form">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="from-date">Case Number</label>
                                            <input type="text" class="form-control" id="caseNumber" name="casenumber">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="to-date">Url</label>
                                            <input type="text" class="form-control" id="url" name="url">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="mobile">Domain</label>
                                            <input type="text" class="form-control" id="domain" name="domain">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="mobile">Registrar</label>
                                            <input type="text" class="form-control" id="registrar" name="registrar">
                                        </div>
                                    </div>

                            </div>
                            <div class="row">
                            <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="mobile">IP</label>
                                            <input type="text" class="form-control" id="ip" name="ip">
                                        </div>
                                    </div>
                            </div>
                            <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button type="button" id="filter" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive mb-0">
                                <table id="complaints"
                                    class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap table-striped">
                                    <thead>
                                        <tr>
                                            <th>SL No</th>
                                            <th>Source type</th>
                                            <th>Case Number</th>
                                            <th>URL</th>
                                            <th>Domain</th>
                                            <th>IP</th>
                                            <th>Registrar</th>
                                            <th>Remarks</th>

                                        </tr>
                                    </thead>
                                    <tbody>
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
    <link rel="stylesheet" href="{{ asset('css/toastr.min.css') }}">

    <script src="{{ asset('js/toastr.js') }}"></script>

     <script>
        $(document).ready(function(){
            var table = $('#complaints').DataTable({
                stateSave: true,
                processing: true,
                serverSide: true,

                buttons: [
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5',
                    'pdfHtml5'
                ],
                "ajax": {
                    "url": "{{ route('get.datalist.others')}}",
                    "data": function(d) {
                        return $.extend({}, d, {});
                    }
                },
                columns: [{
                        data: 'id'
                    },

                    {
                        data: 'source_type'
                    },
                    {
                        data: 'case_number'
                    },

                    {
                        data: 'url'
                    },

                    {
                        data: 'domain'
                    },
                    {
                        data: 'ip'
                    },
                    {
                        data:'registrar'
                    },
                    {
                        data: 'remarks'
                    }

                ],
                "order": [0, 'desc'],
                'ordering': true
            });

        });
</script>

<script>
$(document).ready(function(){

    $("#filter").click(function(){

        var casenumber = $('#caseNumber').val();
        var url = $('#url').val();
        var domain = $('#domain').val();
        var registrar = $('#registrar').val();
        var ip = $('#ip').val();


        if ($.fn.DataTable.isDataTable('#complaints')){
          $('#complaints').DataTable().destroy();
        }
          var table = $('#complaints').DataTable({
                processing: true,
                serverSide: true,

                buttons: [
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5',
                    'pdfHtml5'
                ],
                "ajax": {
                    "url": "{{ route('get.datalist.others')}}",
                    "data": function(d) {
                        return $.extend({}, d, {
                            "casenumber":casenumber,
                            "url":url,
                            "domain":domain,
                            "registrar":registrar,
                            "ip":ip
                        });
                    }
                },
                columns: [{
                        data: 'id'
                    },

                    {
                        data: 'source_type'
                    },
                    {
                        data: 'case_number'
                    },

                    {
                        data: 'url'
                    },

                    {
                        data: 'domain'
                    },
                    {
                        data: 'ip'
                    },
                    {
                        data:'registrar'
                    },
                    {
                        data: 'remarks'
                    }

                ],
                "order": [0, 'desc'],
                'ordering': true
            });




    })
})
</script>


@endsection
