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
                $hasAddSTPermission = in_array('Add Source Type', $sub_permissions);
                $hasAddCategoryPermission = in_array('Add Category', $sub_permissions);
                $hasAddSubCategoryPermission = in_array('Add Subcategory', $sub_permissions);
                $hasAddProfessionPermission = in_array('Add Profession', $sub_permissions);
                $hasAddModusPermission = in_array('Add Modus', $sub_permissions);
                $hasUploadRegistrarPermission = in_array('Upload registrar', $sub_permissions);
                $hasEditSTPermission = in_array('Edit source Type', $sub_permissions);
                $hasDeleteSTPermission = in_array('Delete Source Type', $sub_permissions);
            } else{
                $hasAddSTPermission = $hasAddCategoryPermission = $hasAddSubCategoryPermission = $hasAddProfessionPermission = $hasAddModusPermission = $hasUploadRegistrarPermission = $hasEditSTPermission = $hasDeleteSTPermission = false;
                }

@endphp
@section('content')
<style>
.task-box.danger i {
    color: #fff;
    padding: 20px;
}
    </style>
    <!-- container -->
    <div class="container-fluid">
        <!-- breadcrumb -->
        <div class="breadcrumb-header justify-content-between">
            <div>
                <h4 class="content-title mb-2">
                    Masters !
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="#"> Management</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Masters
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
                            <div class="m-4 justify-content-between flex-wrap">
                                <h4 class="card-title mg-b-10">All  Types</h4><br><br>

                                @if ($hasAddSTPermission)
                                <div class="col-md-2 text-center mb-3 float-left">
                                    <div class="task-box danger mb-0">
                                        <a class="text-white" href="{{ route('sourcetype.create') }}">
                                            <h3 class="mb-0"><i class="fa fa-plus"></i></h3><p class="mb-0 tx-12"> Source Type </p>
                                        </a>
                                    </div>
                                </div>
                                @endif

                                @if ($hasAddCategoryPermission)
                                <div class="col-md-2 text-center mb-3 float-left">
                                    <div class="task-box danger mb-0">
                                        <a class="text-white" href="{{ route('category.index') }}">
                                            <h3 class="mb-0"><i class="fa fa-plus"></i></h3> <p class="mb-0 tx-12">Category</p>
                                        </a>
                                    </div>
                                </div>
                                @endif

                                @if ($hasAddSubCategoryPermission)
                                <div class="col-md-2 text-center mb-3 float-left">
                                    <div class="task-box danger mb-0">
                                        <a class="text-white" href="{{ route('subcategory.create') }}">

                                            <h3 class="mb-0"><i class="fa fa-plus"></i></h3> <p class="mb-0 tx-12">SubCategory</p>
                                        </a>
                                    </div>
                                </div>
                                @endif

                                @if ($hasAddProfessionPermission)
                                <div class="col-md-2 text-center mb-3 float-left">
                                    <div class="task-box danger mb-0">
                                        <a class="text-white" href="{{ url('profession') }}">
                                            <h3 class="mb-0"><i class="fa fa-plus"></i></h3> <p class="mb-0 tx-12"> Profession </p>
                                        </a>
                                    </div>
                                </div>
                                @endif

                                @if ($hasAddModusPermission)
                                <div class="col-md-2 text-center mb-3 float-left">
                                    <div class="task-box danger mb-0">
                                        <a class="text-white" href="{{ url('modus') }}">
                                            <h3 class="mb-0"><i class="fa fa-plus"></i></h3>  <p class="mb-0 tx-12"> Modus </p>
                                        </a>
                                    </div>
                                </div>
                                @endif

                                {{-- @if ($hasAddModusPermission) --}}
                                <div class="col-md-2 text-center mb-3 float-left">
                                    <div class="task-box danger mb-0">
                                        <a class="text-white" href="{{ url('evidencetype') }}">
                                            <h3 class="mb-0"><i class="fa fa-plus"></i></h3>  <p class="mb-0 tx-12"> Evidence Type </p>

                                        </a>
                                    </div>
                                </div>
                                {{-- @endif --}}



                                <div class="col-md-2 text-center mb-3 float-left">
                                    <div class="task-box danger mb-0">
                                        <a class="text-white" href="{{ url('bank-create') }}">
                                            <p class="mb-0 tx-12"> Bank </p>
                                            <h3 class="mb-0"><i class="fa fa-plus"></i></h3>
                                        </a>
                                    </div>
                                </div>

                                <div class="col-md-2 text-center mb-3 float-left">
                                    <div class="task-box danger mb-0">
                                        <a class="text-white" href="{{ url('wallet-create') }}">
                                            <p class="mb-0 tx-12"> Wallet </p>
                                            <h3 class="mb-0"><i class="fa fa-plus"></i></h3>
                                        </a>
                                    </div>
                                </div>

                                <div class="col-md-2 text-center mb-3 float-left">
                                    <div class="task-box danger mb-0">
                                        <a class="text-white" href="{{ url('insurance-create') }}">
                                            <p class="mb-0 tx-12"> Insurance </p>
                                            <h3 class="mb-0"><i class="fa fa-plus"></i></h3>
                                        </a>
                                    </div>
                                </div>

                                <div class="col-md-2 text-center mb-3 float-left">
                                    <div class="task-box danger mb-0">
                                        <a class="text-white" href="{{ url('merchant-create') }}">
                                            <p class="mb-0 tx-12"> Merchants </p>
                                            <h3 class="mb-0"><i class="fa fa-plus"></i></h3>
                                        </a>
                                    </div>
                                </div>

                                @if($hasUploadRegistrarPermission)
                                <div class="col-md-2 text-center mb-3 float-left">
                                    <div class="task-box danger mb-0">
                                        <a class="text-white" href="{{ url('upload-registrar') }}">
                                            <p class="mb-0 tx-12"> Registrar upload </p>
                                            <h3 class="mb-0"><i class="fa fa-upload"></i></h3>
                                        </a>
                                    </div>
                                </div>
                                @endif
                            </div>


                            {{-- <div class="table-responsive mb-0">
                                <table id="example"
                                    class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap table-striped">
                                    <thead>
                                        <tr>
                                            <th>SL No</th>
                                            <th>NAME</th>

                                            @if($hasEditSTPermission || $hasDeleteSTPermission)<th>ACTION</th>@endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
            <!-- /row -->


        </div>
        <!-- /row -->
    </div>
<script>
    // $(document).ready(function(){
    //  	var table = $('#example').DataTable({
    //         processing: true,
    //         serverSide: true,
	//         buttons: [
	//             'copyHtml5',
	//             'excelHtml5',
	//             'csvHtml5',
	//             'pdfHtml5'
	//         ],
    //          "ajax": {

	// 		       	"url": "{{ route('get.sourcetype') }}",
	// 		       	"data": function ( d ) {
	// 		        	return $.extend( {}, d, {

	// 		          	});
    //    				}
    //    			},

    //         columns: [
    //             { data: 'id' },
    //             { data: 'name' },
    //            @if($hasEditSTPermission || $hasDeleteSTPermission) { data: 'edit' } @endif
	// 		],
    //         "order": [0, 'desc'],
    //         'ordering': true
    //     });
    //   	table.draw();
    // });
    // $(document).on('click', '.delete-btn', function() {
    //     var Id = $(this).data('id');
    //     if (confirm('Are you sure you want to delete this item?')) {
    //         $.ajax({
    //             url: '/sourcetype/' + Id,
    //             type: 'POST', // Use POST method
    //             headers: {
    //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //             },
    //             data: {
    //                 _method: 'DELETE' // Override method to DELETE
    //             },
    //             success: function(response) {
    //                 // Handle success response
    //                 // Reload the page
    //                 $('.alert-success-one').html(response.success +'<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +'<span aria-hidden="true">&times;</span>' +'</button>').show();

    //   	            //table.draw();

    //                 location.reload();
    //             },
    //             error: function(xhr, status, error) {
    //                 // Handle error response
    //                 console.error(xhr.responseText)
    //             }
    //         });
    //     }
    // });
</script>
@endsection
