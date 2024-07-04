@extends('layouts.app')

@section('content')

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show w-100" role="alert">
        <ul>
            @foreach ($errors->all() as $error)
                <li>
                    {{ $error }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </li>
            @endforeach
        </ul>
    </div>
@endif


{{-- @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show w-100" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif --}}

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

<div class="container-fluid">
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2">Hi, welcome back!</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Mail Merge List</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Mail Merge List</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row row-sm">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div id="alert_ajaxx" style="display:none;"></div>

                    @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    <div class="m-4">
                        <h4 class="card-title">All Evidence Corresponding to website</h4>
                    </div>

                    <input type="hidden" id="evidence_type" value="{{ $evidence_type }}">
                    <input type="hidden" id="case_no" value="{{ $case_no }}">

                    <div class="table-responsive">
                        <table id="ncrp" class="table table-hover table-bordered table-striped">
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="path_to_bootstrap_js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<link rel="stylesheet" href="{{ asset('css/toastr.min.css') }}">
<script src="{{ asset('js/toastr.js') }}"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTable for #ncrp
        var tableNew = $('#ncrp').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('get.mailmergelist.other') }}",
                data: function(d) {
                    d.evidence_type = $('#evidence_type').val();
                    d.case_no = $('#case_no').val();
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

            ],
            order: [0, 'desc'],
            ordering: true
        });
    });
</script>


@endsection
