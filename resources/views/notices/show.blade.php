@extends('layouts.app')

@section('content')

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        line-height: 1.6;
    }
    .container-fluid {
        padding: 20px;
    }
    .notice-header {
        text-align: center;
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 20px;
    }
    .section {
        margin-bottom: 20px;
    }
    .section-title {
        font-weight: bold;
        margin-bottom: 10px;
        font-size: 1.25rem;
    }
    .details-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    .details-table td {
        padding: 8px;
        border: 1px solid #ddd;
    }
    .details-table th {
        padding: 8px;
        border: 1px solid #ddd;
        text-align: left;
        background-color: #f8f9fa;
    }
    .footer {
        margin-top: 20px;
        text-align: center;
    }
    .footer p {
        margin: 5px 0;
    }
    .btn {
        display: inline-block;
        padding: 10px 20px;
        font-size: 16px;
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #fff;
        background-color: #007bff;
        border-radius: 5px;
        border: none;
        cursor: pointer;
    }
    .btn-secondary {
        background-color: #6c757d;
    }
    .btn-secondary:hover {
        background-color: #5a6268;
    }
    .btn-success {
        background-color: #28a745;
    }
    .btn-success:hover {
        background-color: #218838;
    }
    .signature {
        margin-top: 20px;
        text-align: center;
    }
    .signature img {
        max-width: 100%;
        height: auto;
    }
</style>

<div class="container-fluid">
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2">Hi, welcome back!</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Notice Management</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Notice view</li>
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

                    <div class="notice-content">
                        {!! htmlspecialchars_decode($notice->content) !!}
                    </div>

                    <a href="{{ route('notices.index') }}" class="btn btn-secondary">Back to List</a>
                    <a href="{{ route('notices.edit', $notice->id) }}" class="btn btn-success">Update</a>
                    <a href="" class="btn btn-success">Follow</a>

                </div>
            </div>
        </div>
    </div>
    <!-- /row -->
</div>

@endsection
