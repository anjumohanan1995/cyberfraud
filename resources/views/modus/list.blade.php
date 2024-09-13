@extends('layouts.app')

@section('content')
    <!-- container -->
    <div class="container-fluid">
        <!-- breadcrumb -->
        <div class="breadcrumb-header justify-content-between">
            <div>
                <h4 class="content-title mb-2">
                    Modus !
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="#">Modus Management</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Modus
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
                                    Add Modus
                                </h4>

                                {{-- @if (session('success'))
                                <div id="success-message"  class="alert alert-success alert-dismissible fade show w-100" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                @endif --}}

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
                                                <label for="name">Modus Name:</label>
                                                <input type="text" id="name" name="name" class="form-control" placeholder="Modus Name" value="{{ old('name') }}" required>
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
                                    <table id="modus"  class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap table-striped">
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
    var categoryTable = $('#modus').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('get.modus') }}",
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
            url: "{{ route('add.modus') }}",
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                name: name,
                status: status,
            },
            success: function(response) {
                // alert(response)
            // console.log(response.success);
                $('#hidesuccess').hide();
                $('#modus').DataTable().ajax.reload();
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

$(document).on('click', '.delete-btn', function() {

    var categoryId = $(this).data('id');
    if (confirm('Are you sure you want to delete this modus?')) {
    $.ajax({
        url: '{{ url('modus') }}/' + categoryId,
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
                    console.log(response);
                    $('#modus').DataTable().ajax.reload();
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
