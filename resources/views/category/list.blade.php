@extends('layouts.app')

@section('content')
    <!-- container -->
    <div class="container-fluid">
        <!-- breadcrumb -->
        <div class="breadcrumb-header justify-content-between">
            <div>
                <h4 class="content-title mb-2">
                    Category !
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="#">Category Management</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Category
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
                                    Add Category
                                </h4>

                                @if (session('success'))
                                <div id="success-message"  class="alert alert-success alert-dismissible fade show w-100" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                @endif

                                <div class="col-md-9"></div>
                                {{-- <div class="col-md-2 text-center">
                                    <div class="task-box primary  mb-0">
                                        <a class="text-white" href="{{ route('subcategory.create') }}">
                                            <p class="mb-0 tx-12 "> Sub Category </p>
                                            <h3 class="mb-0"><i class="fa fa-plus"></i></h3>
                                        </a>
                                    </div>
                                </div> --}}


                            </div>

                            <form >

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">Category Name:</label>
                                                <input type="text" id="name" name="name" class="form-control" placeholder="Category Name" value="{{ old('name') }}" required>
                                                @error('name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
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
                                    <table id="category"  class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap table-striped">
                                    <thead>
                                    <tr>
                                    <th>Sl No</th>
                                    <th>Name</th>
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


    // Initialize DataTable when the document is ready
    var categoryTable = $('#category').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('get.categories') }}",
            data: function (d) {
                return $.extend({}, d, {});
            }
        },
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'status' },
            { data: 'edit' }
        ],
        order: [0, 'desc'],
        ordering: true
    });

    $("#submit").click(function (){
        var name = $("#name").val();
        var status = $("#status").val();

        $.ajax({
            url: "{{ route('add.category') }}",
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                name: name,
                status: status,
            },
            success: function(response) {
                //console.log(response);
                $('#name').val('');
                categoryTable.ajax.reload(null, false);
                var successMessage = "{{ session('success') }}";
                if (successMessage) {

                    $('#success-message').html(successMessage); // Update message
                    $('#success-message').fadeIn(); // Show the message
                }
            },
            error: function(xhr, status, error){
                console.error(xhr.responseText);
            }
        });
    });

$(document).on('click', '.delete-btn', function() {

    var categoryId = $(this).data('id');
    if (confirm('Are you sure you want to delete this category?')) {
    $.ajax({
        url: '{{ url('category') }}/' + categoryId,
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            //console.log(response);
            categoryTable.ajax.reload(null, false);
            alert('Category deleted successfully');
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
            alert('Error deleting category');
        }
    });
}
})

});




</script>

@endsection
