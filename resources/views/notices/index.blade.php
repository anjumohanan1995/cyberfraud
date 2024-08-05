@extends('layouts.app')
@section('content')

<div class="container-fluid">
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2">Hi, welcome back!</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Notice Management</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Notice List</li>
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

                    <div class="container">
                        <h1>Notices List</h1>
                        <table id="noticesTable" class="display">
                            <thead>
                                <tr>
                                    <th>#</th> <!-- Serial number column -->
                                    <th>Notices</th>
                                    <th>Actions</th> <!-- Actions column for the View button -->
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($notices as $index => $notice)
                                    <tr>
                                        <td>{{ $index + 1 }}</td> <!-- Display serial number -->
                                        <td>{{ $notice->sub }}</td>
                                        <td>
                                            <a href="{{ route('notices.show', $notice->id) }}" class="btn btn-primary">View</a>
                                        </td>
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

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Include DataTables JS -->
<script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js"></script>
<!-- Initialize DataTable -->
<script>
    $(document).ready(function() {
        $('#noticesTable').DataTable();
    });
</script>

@endsection
