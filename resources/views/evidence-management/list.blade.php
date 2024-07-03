@extends('layouts.app')

@section('content')

@if ($errors->any())
    <div class="alert alert-success">
        {{ $errors->first('message') }}
    </div>
@endif

<style>
    .tabs-menu1 ul li a {
        padding: 10px 20px 11px 20px;
        display: block;
        color: #282f53;
        text-decoration: none;
    }
    .tabs-menu1 ul li a.active {
        border-bottom: 3px solid #3858f9;
        color: #3858f9; /* Optional: Change color when active */
    }
</style>

<link rel="stylesheet" href="path_to_bootstrap_css">
<link rel="stylesheet" href="path_to_font_awesome">


    <!-- container -->
    <div class="container-fluid">
        <!-- breadcrumb -->
        <div class="breadcrumb-header justify-content-between">
            <div>
                <h4 class="content-title mb-2">Hi, welcome back!</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Evidence Management</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Evidence</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- /breadcrumb -->

        <!-- main-content-body -->
        <div class="row row-sm">
            <div class="col-md-12 col-xl-12">
                <div class="card overflow-hidden review-project">
                    <div class="card-body">
                        <div class="m-4 d-flex justify-content-between">
                            <div id="alert_ajaxx" style="display:none"></div>
                            @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show w-100" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @endif
                            <div class="alert alert-success-one alert-dismissible fade show w-100" role="alert" style="display:none">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                        <div class="m-4 d-flex justify-content-between">
                            <h4 class="card-title mg-b-10">All Evidence</h4>
                        </div>
                        <div class="main-content-body">
                            <div class="row row-sm">
                                <div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
                                    <div class="card">
                                        <div class="card-body table-new">
                                            <div id="success_message" class="ajax_response" style="display: none;"></div>
                                            <div class="panel panel-primary">
                                                <div class="tab-menu-heading">
                                                    <div class="tabs-menu1">
                                                        <ul class="nav panel-tabs">
                                                            <li><a href="#tabNew" class="active" data-bs-toggle="tab" data-bs-target="#tabNew">NCRP Case Data</a></li>
                                                            <li><a href="#tabReturned" data-bs-toggle="tab" data-bs-target="#tabReturned">Others Case Data</a></li>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <div class="panel-body tabs-menu-body">
                                                    <div class="tab-content">
                                                        <div class="tab-pane active" id="tabNew">
                                                            <form id="complaint-form-ncrp">
                                                                <div class="row">
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label for="from-date-new">From Date:</label>
                                                                            <input type="date" class="form-control" id="from-date-ncrp" name="from_date">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label for="to-date-new">To Date:</label>
                                                                            <input type="date" class="form-control" id="to-date-ncrp" name="to_date">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label for="current_date">Today:</label>
                                                                            <select class="form-control" id="current_date" name="current_date">
                                                                                <option value="">All</option>
                                                                                <option value="today">Today</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>


                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label for="evidence_type_ncrp">Evidence Type:</label>
                                                                            <select class="form-control" id="evidence_type_ncrp" name="evidence_type_ncrp" onchange="showTextBox('evidence_type_ncrp')">
                                                                                <option value="">--select--</option>
                                                                                @foreach($evidenceTypes as $evidenceType)
                                                                                    <option value="{{ $evidenceType->id }}">{{ $evidenceType->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            <div id="searchBoxContainer_evidence_type_ncrp"></div>
                                                                            @error('evidence_type_ncrp')
                                                                                <div class="text-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                                <div class="row">
                                                                     <div class="col-md-3">
                                                                    <label for="evidence_type_ncrp">URL:</label>
                                                                    <input type="text" name="url" id="url" class="form-control">
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                    <label for="evidence_type_ncrp">Domain:</label>
                                                                        <input type="text" name="domain" id="domain" class="form-control">
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                     <label for="evidence_type_ncrp">Acknowledgement No:</label>
                                                                    <input type="text" name="acknowledgement_no" id="acknowledgement_no" class="form-control">
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12 text-right">
                                                                        <button type="submit" class="btn btn-primary">Submit</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                            <div class="table-responsive">
                                                                <table id="ncrp" class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap table-striped">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>SL No</th>
                                                                            <th>Acknowledgement No</th>
                                                                            <th>Evidence Type</th>
                                                                            <th>URL</th>
                                                                            <th>Mobile</th>
                                                                            <th>Domain</th>
                                                                            <th>IP</th>
                                                                            <th>Registrar</th>
                                                                            <th>Registry Details</th>
                                                                            <th>Mail</th>
                                                                            <th>Status</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <!-- Data will be populated here -->
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane" id="tabReturned">
                                                            <form id="complaint-form-others">
                                                                <div class="row">
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label for="from-date-new">From Date:</label>
                                                                            <input type="date" class="form-control" id="from-date-others" name="from_date">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label for="to-date-new">To Date:</label>
                                                                            <input type="date" class="form-control" id="to-date-others" name="to_date">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label for="current_date">Today:</label>
                                                                            <select class="form-control" id="current_date_others" name="current_date">
                                                                                <option value="">All</option>
                                                                                <option value="today">Today</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>


                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label for="evidence_type_ncrp">Evidence Type:</label>
                                                                            <select class="form-control" id="evidence_type_others" name="evidence_type_ncrp" onchange="showTextBox('evidence_type_ncrp')">
                                                                                <option value="">--select--</option>
                                                                                @foreach($evidenceTypes as $evidenceType)
                                                                                    <option value="{{ $evidenceType->id }}">{{ $evidenceType->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            <div id="searchBoxContainer_evidence_type_ncrp"></div>
                                                                            @error('evidence_type_ncrp')
                                                                                <div class="text-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                                <div class="row">
                                                                     <div class="col-md-3">
                                                                    <label for="evidence_type_ncrp">URL:</label>
                                                                    <input type="text" name="url" id="url-others" class="form-control">
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                    <label for="evidence_type_ncrp">Domain:</label>
                                                                        <input type="text" name="domain-others" id="domain" class="form-control">
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                     <label for="evidence_type_ncrp">Case No:</label>
                                                                    <input type="text" name="case_number" id="case_number" class="form-control">
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12 text-right">
                                                                        <button type="submit" class="btn btn-primary">Submit</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                            <div class="table-responsive">
                                                                <table id="others" style="width:100%" class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap table-striped">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>SL No</th>
                                                                            <th>Case No</th>
                                                                            <th>Evidence Type</th>
                                                                            <th>URL</th>
                                                                            <th>Domain</th>
                                                                            <th>IP</th>
                                                                            <th>Registrar</th>
                                                                            <th>Registry Details</th>
                                                                            <th>Mail</th>
                                                                            <th>Status</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <!-- Data will be populated here -->
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /row -->
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="path_to_bootstrap_js"></script>

    <link rel="stylesheet" href="{{ asset('css/toastr.min.css') }}">

    <script src="{{ asset('js/toastr.js') }}"></script>


<!-- Include Bootstrap JavaScript -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


    @if (session('status'))
    <script>
        toastr.success('{{ session('status') }}', 'Success!')
    </script>
@endif

    <script>
        $(document).ready(function() {
            $('.tabs-menu1 ul li a').on('click', function(e) {
                e.preventDefault();
                $('.tabs-menu1 ul li a').removeClass('active');
                $(this).addClass('active');

                // Show the corresponding tab content
                $('.tab-pane').removeClass('active');
                $($(this).attr('data-bs-target')).addClass('active');
            });

            $('#complaint-form-ncrp').on('submit', function(e) {
                e.preventDefault();
                // Add your AJAX code here to filter the NCRP Case Data table
            });

            $('#complaint-form-others').on('submit', function(e) {
                e.preventDefault();
                // Add your AJAX code here to filter the Others Case Data table
            });
        });
    </script>

<script>
$(document).ready(function() {
    // Initialize DataTable for #example
    var tableNew = $('#ncrp').DataTable({
        processing: true,
        serverSide: true,


        ajax: {
            url: "{{ route('get.evidence.ncrp') }}",
            data: function(d) {
                return $.extend({}, d, {
                    from_date: $('#from-date-ncrp').val(),
                    to_date: $('#to-date-ncrp').val(),
                    current_date: $('#current_date').val(),
                    acknowledgement_no: $('#acknowledgement_no').val(),
                    url: $('#url').val(),
                    domain: $('#domain').val(),
                    evidence_type: $("#evidence_type_ncrp").val(),
                    evidence_type_text: $("#evidence_type_ncrp option:selected").text(),

                });
            }
        },
        columns: [
            { data: 'id' },
            { data: 'acknowledgement_no' },
            { data: 'evidence_type' },
            { data: 'url' },
            { data: 'mobile' },
            { data: 'domain' },
            { data: 'ip' },
            { data: 'registrar' },
            { data: 'registry_details' },
            { data: 'edit'},
            { data: 'status'}
        ],
        order: [0, 'desc'],
        ordering: true
    });

    // Initialize DataTable for #example1
    var tableReturned = $('#others').DataTable({
        processing: true,
        serverSide: true,

        ajax: {
            url: "{{ route('get.evidence.others') }}",
            data: function(d) {
                return $.extend({}, d, {
                    from_date: $('#from-date-others').val(),
                    to_date: $('#to-date-others').val(),
                    current_date: $('#current_date_others').val(),
                    case_number: $('#case_number').val(),
                    url: $('#url-others').val(),
                    domain: $('#domain-others').val(),
                    evidence_type: $("#evidence_type_others").val(),
                    evidence_type_text: $("#evidence_type_others option:selected").text(),
                });
            }
        },
        columns: [
            { data: 'id' },
            { data: 'case_number' },
            { data: 'evidence_type' },
            { data: 'url' },
            { data: 'domain' },
            { data: 'ip' },
            { data: 'registrar' },
            { data: 'registry_details' },
            { data: 'edit' },
            { data: 'status'}
        ],
        order: [0, 'desc'],
        ordering: true
    });

    // Form submission handler for #complaint-form-ncrp
    $('#complaint-form-ncrp').on('submit', function(e) {
        e.preventDefault();
        tableNew.ajax.reload();
    });

    // Form submission handler for #complaint-form-others
    $('#complaint-form-others').on('submit', function(e) {
        e.preventDefault();
        tableReturned.ajax.reload();
    });

    // Tab navigation handler
    $('.tabs-menu1 ul li a').on('click', function(e) {
        e.preventDefault();
        $('.tabs-menu1 ul li a').removeClass('active');
        $(this).addClass('active');

        // Show the corresponding tab content
        $('.tab-pane').removeClass('active');
        $($(this).attr('data-bs-target')).addClass('active');
    });

            // Dropdown toggle handler
            $(document).on('click', '.dropdown-toggle', function() {
            $(this).next('.dropdown-menu').toggle();
        });
});

</script>

<script>
    function toggleReportStatus(element) {
        var recordId = element.getAttribute('data-record-id');
        var isChecked = element.checked;

        // Perform AJAX call to update reported status
        fetch('/update-reported-status/' + recordId)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Update UI or notify user based on response
            console.log('Status updated successfully');
        })
        .catch(error => {
            console.error('Error updating status:', error);
            // Handle error or show user an error message
        });
    }
</script>

<script>
    function toggleReportStatusOther(element) {
        var recordId = element.getAttribute('data-record-id');
        var isChecked = element.checked;

        // Perform AJAX call to update reported status
        fetch('/update-reported-statusother/' + recordId)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Update UI or notify user based on response
            console.log('Status updated successfully');
        })
        .catch(error => {
            console.error('Error updating status:', error);
            // Handle error or show user an error message
        });
    }
</script>


@endsection
