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
                                    Edit Category
                                </h4>

                                @if (session('success'))
                                <div id="success-message"  class="alert alert-success alert-dismissible fade show w-100" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                @endif

                            </div>

                            <form action ="{{ route('category.update',$category->id ) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">Category Name:</label>
                                                <input type="text" value="{{ $category->name }}" id="name" name="name" class="form-control" placeholder="Category Name" value="{{ old('name') }}" required>
                                                @error('name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="status">Status:</label>
                                                <select class="form-control" name="status" id="status">
                                                    <option value="1" @if($category->status == "1") selected  @endif>Active</option>
                                                    <option value="0" @if($category->status == "0") selected @endif >Inactive</option>
                                                </select>
                                                @error('status')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                    </div>
                                    <button type="submit"  class="btn btn-primary">Submit</button>
                                </form>

                        </div>


                    </div>



                </div>
            </div>
            <!-- /row -->


        </div>
        <!-- /row -->
    </div>



@endsection
