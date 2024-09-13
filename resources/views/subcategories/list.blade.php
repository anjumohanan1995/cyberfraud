@extends('layouts.app')

@section('content')
    <!-- container -->
    <div class="container-fluid">
        <!-- breadcrumb -->
        <div class="breadcrumb-header justify-content-between">
            <div>
                <h4 class="content-title mb-2">
                    Sub Category !
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="#">Sub Category Management</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Sub Category
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
                            <div id="hidesuccess">
                                @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show w-100" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                @endif

                                </div>

                                <div id="success-message" class="alert alert-success alert-dismissible fade show w-100" role="alert" style="display: none;">
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <div class=" m-4 d-flex justify-content-between">
                                <h4 class="card-title mg-b-10">
                                    Add SubCategory
                                </h4>


                                <div id="success-message">

                                </div>


                            </div>

                            <form >

                                    <div class="row">
                                    <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="name">Category</label>
                                                <select id="category" name="category" class="form-control" value="{{ old('category') }}">
                                                <option value="">--select--</option>
                                                @foreach($categories as $category)
                                                 <option value="{{ $category->id }}"> {{ $category->name }} </option>
                                                @endforeach
                                                </select>
                                                @error('name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="name">Sub Category:</label>
                                                <input type="text" id="subcategory" name="subcategory" class="form-control" placeholder="Category Name" value="{{ old('name') }}" required>
                                                @error('subcategory')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="status">Status:</label>
                                                <select class="form-control" name="status" id="status">
                                                    <option value="1" selected>Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                                @error('status')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                    </div>
                                    <button type="button" id="submit" class="btn btn-primary">Submit</button>
                                </form>
                                <div class="table-responsive mb-0">
                                    <table id="subcategorylist" style="width:100%" class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap table-striped">
                                    <thead>
                                    <tr>
                                    <th>Sl No</th>
                                    <th>Category</th>
                                    <th>Sub Category</th>
                                    <th>Status</th>
                                    <th>Action</th>
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

    var subcategoryTable = $('#subcategorylist').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('get.subcategories') }}",
            data: function (d) {
                return $.extend({}, d, {});
            }
        },
        columns: [
            { data: 'id' },
            { data: 'category' },
            { data: 'subcategory' },
            { data: 'status' },
            { data: 'edit' }
        ],
        order: [0, 'desc'],
        ordering: true
    });

    $("#submit").click(function(){
        var category = $("#category").val();
        var subcategory = $("#subcategory").val();
        var status = $("#status").val();

        $.ajax({
            url: "{{ route('add.subcategory') }}",
            method: 'POST',
            headers:{
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:{
                category: category,
                subcategory: subcategory,
                status:status,
            },
            success: function(response) {
                // alert(response)
            // console.log(response.success);
                $('#hidesuccess').hide();
                $('#subcategorylist').DataTable().ajax.reload();
                $('#success-message').html(response.success + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>').show();
                // table.ajax.reload(); // Reload DataTable
                // windows.location.reload();
                },
                error: function(xhr, status, error) {
                var errors = xhr.responseJSON.errors;
                $.each(errors, function(key, value) {
                    $('#' + key).after('<div class="text-danger">' + value + '</div>');
                });
            }
        });
    });


$(document).on('click', '.delete-btn', function(){

    var subcategoryId = $(this).data('id');
   if (confirm('Are you sure you want to delete this category?')) {
    $.ajax({
        url: '{{ url('subcategory') }}/' + subcategoryId,
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
                    console.log(response);
                    $('#subcategorylist').DataTable().ajax.reload();
                    $('#success-message').html(response.success + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>').show();
                    // table.ajax.reload(); // Reload DataTable
                    // windows.location.reload();
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
    });
   }

})

});




</script>

@endsection
