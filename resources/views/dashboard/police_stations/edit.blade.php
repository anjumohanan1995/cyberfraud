@extends('layouts.app')

@section('content')
    <!-- container -->
    <div class="container-fluid">
        <!-- breadcrumb -->
        <div class="breadcrumb-header justify-content-between">
            <div>
                <h4 class="content-title mb-2">

                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="#">Police Station Management</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Edit Police Station
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
                                    Edit Police station Here!
                                </h4>

                            </div>

                            <div class="table-responsive mb-0">
                                <form action="{{ route('police_stations.update', ['police_station' => $data->id]) }}" method="POST">
                                    @csrf
                                    @method('put')

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="station_name">Station Name:</label>
                                                <input type="text" id="station_name" name="station_name" class="form-control" placeholder="Enter Station Name" value="{{ old('station_name') ?: $data->name }}" required>
                                                @error('station_name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="district_id">District:</label>
                                                <select id="district_id" name="district_id" class="form-control" required>
                                                    @foreach($districts as $district)
                                                        <option value="{{ $district->id }}">{{ $district->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('district_id')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="place">Place:</label>
                                                <input type="text" id="place" name="place" class="form-control" placeholder="Enter Place" value="{{ $data->place }}" required>
                                                @error('place')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="address">Address:</label>
                                                <textarea id="address" name="address" class="form-control" placeholder="Enter Address" required>{{ $data->address }}</textarea>
                                                @error('address')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="phone">Phone:</label>
                                                <input type="number" id="phone" name="phone" class="form-control" placeholder="Enter Phone" value="{{ $data->phone }}" required>
                                                @error('phone')
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
