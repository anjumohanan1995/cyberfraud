@extends('layouts.app')
@php
use App\Models\RolePermission;
use Illuminate\Support\Facades\Auth;
$user = Auth::user();
            $role = $user->role;
            $permission = RolePermission::where('role', $role)->first();
            $permissions = $permission && is_string($permission->permission) ? json_decode($permission->permission, true) : ($permission->permission ?? []);
            $sub_permissions = $permission && is_string($permission->sub_permissions) ? json_decode($permission->sub_permissions, true) : ($permission->sub_permissions ?? []);
            if ($sub_permissions || $user->role == 'Super Admin') {
                $hasAddEvidenceTypePermission = in_array('Add Evidence Type', $sub_permissions);
                $hasEditEvidenceTypePermission = in_array('Edit Evidence Type', $sub_permissions);
                $hasDeleteEvidenceTypePermission = in_array('Delete Evidence Type', $sub_permissions);
            } else{
                    $hasShowTTypePermission = $hasShowBankPermission = $hasShowFilledByPermission = $hasShowComplaintRepoPermission = $hasShowFIRLodgePermission = $hasShowStatusPermission = $hasShowSearchByPermission = $hasShowSubCategoryPermission = false;
                }

@endphp
@section('content')
    <!-- container -->
    <div class="container-fluid">
        <!-- breadcrumb -->
        <div class="breadcrumb-header justify-content-between">
            <div>
                <h4 class="content-title mb-2">
                    Evidence Type !
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="#">Evidence Type Management</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Evidence Type
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
                                <div class="alert alert-success-one alert-dismissible fade show w-100" role="alert" style="display:none">

                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                            </div>
                            <div class=" m-4 d-flex justify-content-between">
                                <h4 class="card-title mg-b-10">
                                    Evidence Type
                                </h4>
                                @if($hasAddEvidenceTypePermission)
                                <div class="col-md-1 col-6 text-center">
                                    <div class="task-box primary  mb-0">
                                        <a class="text-white" href="{{ route('evidencetype.create') }}">
                                            <p class="mb-0 tx-12">Add </p>
                                            <h3 class="mb-0"><i class="fa fa-plus"></i></h3>
                                        </a>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <div class="table-responsive mb-0">
                                <table id="example"
                                    class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap table-striped">
                                    <thead>
                                        <tr>
                                            <th>SL No</th>
                                            <th>NAME</th>

                                           @if($hasEditEvidenceTypePermission || $hasDeleteEvidenceTypePermission) <th>ACTION</th> @endif
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

			       	"url": "{{ route('get.evidencetype') }}",
			       	"data": function ( d ) {
			        	return $.extend( {}, d, {

			          	});
       				}
       			},

            columns: [
                { data: 'id' },
                { data: 'name' },
                @if($hasEditEvidenceTypePermission || $hasDeleteEvidenceTypePermission) { data: 'edit' } @endif
			],
            "order": [0, 'desc'],
            'ordering': true
        });
      	table.draw();
    });
    $(document).on('click', '.delete-btn', function() {
        var Id = $(this).data('id');
        if (confirm('Are you sure you want to delete this item?')) {
            $.ajax({
                url: '/evidencetype/' + Id,
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
                    $('.alert-success-one').html(response.success +'<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +'<span aria-hidden="true">&times;</span>' +'</button>').show();

      	            //table.draw();

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
