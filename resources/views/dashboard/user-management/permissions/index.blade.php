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
                            Permission
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
                            </div>
                            <div class=" m-4 d-flex justify-content-between">
                                <h4 class="card-title mg-b-10">
                                    All Permissions
                                </h4>
                                <div class="col-md-1 col-6 text-center">
                                    <div class="task-box primary  mb-0">
                                        <a class="text-white" href="{{ route('permissions.create') }}">
                                            <p class="mb-0 tx-12">Add </p>
                                            <h3 class="mb-0"><i class="fa fa-plus"></i></h3>
                                        </a>
                                    </div>
                                </div>
                            </div>



                            <div class="table-responsive mb-0">
                                <table id="example"
                                    class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap table-striped">
                                    <thead>
                                        <tr>
                                            <th>SL No</th>
                                            <th>Permission</th>
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

      $(document).ready(function(){
     	var table = $('#example').DataTable({
            processing: true,
            serverSide: true,
	        buttons: [
	            'copyHtml5',
	            'excelHtml5',
	            'csvHtml5',
	            'pdfHtml5'
	        ],
             "ajax": {

			       	"url": "{{ route('get.permissions') }}",
			       	"data": function ( d ) {
			        	return $.extend( {}, d, {
				           
			          	});
       				}
       			},

            columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'edit' }
			],
            "order": [0, 'desc'],
            'ordering': true
        });
      	table.draw();
    });
       // function fetchPermissions(page) {
            //$.ajax({
               // url: '{{ route('get.permissions') }}',
               // type: 'GET',
               // dataType: 'json',
               // data: {
               //     page: page
              //  },
               // success: function(response) {
                    // Populate table body with received data
                  //  var usersTableBody = $('#users-table tbody');
                   // usersTableBody.empty(); // Clear existing table rows
                   // $.each(response.data, function(index, permission) {
                        //var row = $('<tr>').append(
//$('<td>').text(index + 1),
                          //  $('<td>').text(permission.name),
                          //  $('<td>').append(
                            //    $('<a>').attr('href', '/permissions/' + permission.id + '/edit')
                             //   .addClass('btn btn-primary edit-btn').text('Edit'),
                               // $('<button>').addClass('btn btn-danger delete-btn').attr('data-id',
                        //            permission.id).text('Delete')
                       //     )
                     //   );
                     ///   usersTableBody.append(row);
                   // });

                    // Populate pagination links
                  //  $('#pagination-links').html(response.links);
               // },
                //error: function(xhr, status, error) {
                //    console.error('Error:', error);
               // }
            //});
        //}


        // Initial fetch of permissions data for the first page
        //fetchPermissions(1);


        // Event listener for pagination links
        //$(document).on('click', '#pagination-links a', function(event) {
            //event.preventDefault();
            //var page = $(this).attr('href').split('page=')[1]; // Extract page number from pagination link
           // fetchPermissions(page); // Fetch permissions data for the clicked page
       // });
       



        $(document).on('click', '.delete-btn', function() {
            var permissionId = $(this).data('id');
            if (confirm('Are you sure you want to delete this item?')) {
                $.ajax({
                    url: '/permissions/' + permissionId,
                    type: 'POST', // Use POST method
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        _method: 'DELETE' // Override method to DELETE
                    },
                    success: function(response) {
                        // Handle success response
                        // Reload the page
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        console.error(xhr.responseText)
                    }
                });
            }
        });
    </script>
@endsection
