<!-- resources/views/no-permission.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6">
                        <h4>No Permission</h4>
                    </div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ url('/') }}">
                                    <svg class="stroke-icon">
                                        <use href="svg/icon-sprite.svg#stroke-home"></use>
                                    </svg>
                                </a>
                            </li>
                            <li class="breadcrumb-item">No Permission</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-danger">
                        <strong>Access Denied!</strong> You do not have permission to access this page.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
