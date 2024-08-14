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
                            <div class=" m-4 d-flex justify-content-between">
                                <h4 class="card-title mg-b-10">
                                    Edit SubCategory
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

                            <form action ="{{ route('subcategory.update',$subcategory->id ) }}" method="POST">
                                @csrf
                                @method('PUT')

                                    <div class="row">
                                    <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="name">Category</label>
                                                <select id="category"  name="category" class="form-control" value="{{ old('category') }}">
                                                <option value="">--select--</option>
                                                @foreach($categories as $category)
                                                 <option value="{{ $category->id }}" @if($subcategory->category_id == $category->id ) selected @endif > {{ $category->name }} </option>
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
                                                <input type="text" id="subcategory" value="{{ $subcategory->subcategory }}" name="subcategory" class="form-control" placeholder="Category Name" value="{{ old('name') }}" required>
                                                @error('name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="status">Status:</label>
                                                <select class="form-control" name="status" id="status">
                                                    <option value="1" @if($subcategory->status == "1") selected @endif>Active</option>
                                                    <option value="0" @if($subcategory->status == "0") selected @endif >Inactive</option>
                                                </select>
                                                @error('status')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                    </div>
                                    <button type="submit" id="submit" class="btn btn-primary">Submit</button>
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
