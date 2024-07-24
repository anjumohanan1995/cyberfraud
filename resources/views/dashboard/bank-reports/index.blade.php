@extends('layouts.app')
@section('content')

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
                        <li class="breadcrumb-item"><a href="#">Reports</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Bank Daily Reports</li>
                    </ol>
                </nav>
            </div>
        </div>
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
                            <h4 class="card-title mg-b-10">All Case Data</h4>
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
                                                                            <input type="date" class="form-control" id="from-date-new" name="from_date">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label for="to-date-new">To Date:</label>
                                                                            <input type="date" class="form-control" id="to-date-new" name="to_date" onchange="setFromDatencrp()">
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
                                                                            <label for="bank_action_status">Bank Action </label>
                                                                            <select class="form-control" id="bank_action_status">
                                                                                <option value="">--select--</option>
                                                                                <option value="moneytransferto">Money Transfer to</option>
                                                                                <option value="cashwithdrawalthroughcheque">Cash Withdrawal through Cheque</option>
                                                                                <option value="transactionputonhold">Transaction put on hold</option>
                                                                                <option value="other">Other</option>
                                                                                <option value="withdrawalthroughatm">Withdrawal through ATM</option>
                                                                                <option value="withdrawalthroughpos">Withdrawal through POS</option>
                                                                                <option value="wrongtransaction">Wrong Transaction</option>
                                                                                <option value="closedcomplaint">Closed Complaint</option>
                                                                                <option value="oldtransaction">Old Transaction</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label for="evidence_type_ncrp">Evidence Type:</label>
                                                                            <select class="form-control" id="evidence_type_ncrp" name="evidence_type_ncrp" onchange="showTextBox('evidence_type_ncrp')">
                                                                                <option value="">--select--</option>
                                                                                {{-- @foreach($evidenceTypes as $evidenceType) --}}
                                                                                    <option value="{{-- $evidenceType->id --}}">{{-- $evidenceType->name --}}</option>
                                                                                {{-- @endforeach --}}
                                                                            </select>
                                                                            <div id="searchBoxContainer_evidence_type_ncrp"></div>
                                                                            @error('evidence_type_ncrp')
                                                                                <div class="text-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12 text-right">
                                                                        <button type="submit" class="btn btn-primary">Submit</button>
                                                                        <!-- CSV Download Button -->
                                                                        @if(true)<a href="#" class="btn btn-success" id="csvDownload">Download CSV</a>@endif
                                                                        <!-- Excel Download Button -->
                                                                        @if(true)<a href="#" class="btn btn-info" id="excelDownload">Download Excel</a>@endif
                                                                    </div>
                                                                </div>
                                                            </form>
                                                            <div class="table-responsive">
                                                                <table id="example" class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap table-striped">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>SL No</th>
                                                                            <th>District</th>
                                                                            <th>1930</th>
                                                                            <th>NCRP</th>
                                                                            <th>Total</th>
                                                                            <th>Actual Amount<br>Lost</th>
                                                                            <th>Actual Amount<br>Lost On</th>
                                                                            <th>Actual Amount<br>Hold On</th>
                                                                            <th>Hold Other<br>Than</th>
                                                                            <th>Total Hold<br>On</th>
                                                                            <th>Amount Lost<br>From ECO ON</th>
                                                                            <th>Total Amount<br>LOST FROM ECO</th>
                                                                            <th>Amount For<br>Pending Action</th>
                                                                            {{-- <th>Bank Action</th> --}}
                                                                            {{-- <th>Action</th> --}}
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

@endsection
