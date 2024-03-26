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
                                <h4 class="card-title mg-b-10">
                                    Add User!
                                </h4>

                            </div>

                            <div class="table-responsive mb-0">
                                <form action="{{ route('users.store') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">Name:</label>
                                                <input type="text" id="name" name="name" class="form-control" placeholder="Enter your name" value="{{ old('name') }}" required>
                                                @error('name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">Last Name:</label>
                                                <input type="text"  name="last_name" class="form-control" placeholder="Enter your last name" value="{{ old('last_name') }}" required>
                                                @error('last_name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">Email:</label>
                                             	<input type="email" class="form-control" placeholder="Email" name="email" value="{{old('email')}}" />
													@error('email')
														<span class="text-danger">{{$message}}</span>
													@enderror
												
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">Password:</label>
                                                <input type="password" class="form-control" placeholder="Password" name="password" value="{{old('password')}}"/>
                                                @error('password')
                                                    <span class="text-danger">{{$message}}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">Role:</label>
                                             	<select id="role" name="role" class="form-control"  >
													<option value="" selected>Select Role</option>
														@foreach($role as $roles)
															<option value="{{$roles->name}}" @if (old('role') == $roles['name']) selected @endif>{{$roles->name}}</option>
														@endforeach
												</select>

												@error('role')
													<span class="text-danger">{{$message}}</span>
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
