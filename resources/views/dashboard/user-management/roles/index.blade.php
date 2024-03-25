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
                            <a href="#">User Management</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Roles
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
                                <h4 class="card-title mg-b-10">
                                    All Projects
                                </h4>
                                <div class="col-md-1 col-6 text-center">
                                    <div class="task-box primary  mb-0">
                                        <a class="text-white" href="">
                                            <p class="mb-0 tx-12">Add </p>
                                            <h3 class="mb-0"><i class="fa fa-plus"></i></h3>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive mb-0">
                                <table id="users-table"
                                    class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap table-striped">
                                    <thead>
                                        <tr>
                                            <th>SL No</th>
                                            <th>ROLE</th>
                                            <th>ACTION</th>
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

    <script>
        $(document).ready(function() {
            // Make AJAX request to fetch users data
            $.ajax({
                url: '{{ route('get.users') }}',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    // Populate table with received data
                    var usersTableBody = $('#users-table tbody');
                    $.each(response, function(index, user) {
                        usersTableBody.append('<tr>' +
                            '<td>' + (index + 1) + '</td>' +
                            '<td>' + user.role + '</td>' +
                            '<td>' + '<button class="btn btn-primary edit-btn" data-id="' + user.id + '">Edit</button>' +
                            '<button class="btn btn-danger delete-btn" onclick="return confirm(\"Are you sure you want to delete this item?\");" data-id="' + user.id + '">Delete</button>' +
                            '</td>' +
                            '</tr>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

    </script>
@endsection
