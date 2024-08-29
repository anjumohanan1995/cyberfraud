@extends('layouts.app')

@section('content')
    <!-- container -->
    <div class="container-fluid">
        <!-- breadcrumb -->
        <div class="breadcrumb-header justify-content-between">
            <div>
                <h4 class="content-title mb-2">
                    Source Type !
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="#">Merchant Management</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Edit Merchant
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
                            <div class="m-4 d-flex justify-content-between">
                                <h4 class="card-title mg-b-10">
                                    Edit Merchant Here!
                                </h4>
                            </div>

                            <div class="table-responsive mb-0">
                                <form action="{{ route('merchant.update', $data->id) }}" method="POST">
                                    @csrf
                                    @method('put')

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">Name:</label>
                                                <input type="text" id="name" name="name" class="form-control" placeholder="Enter Merchant" value="{{ old('name') ?: $data->merchant }}" required>
                                                @error('name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="status">Status:</label>
                                                <select class="form-control" name="status" required>
                                                    <option value="active" {{ $data->status == 'active' ? 'selected' : '' }}>Active</option>
                                                    <option value="inactive" {{ $data->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                                @error('status')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- /row -->


        </div>
        <!-- /row -->
    </div>
@endsection
