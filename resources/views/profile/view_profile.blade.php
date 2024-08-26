@extends('layouts.app')

@section('content')
<STYLE>
    .edit{
        float:right;
        color:white;
    }
</STYLE>
<div class="main-content app-content">
    <div class="main-container container-fluid">
        <div class="breadcrumb-header justify-content-between row me-0 ms-0">
            <div class="col-xl-9">
                <h4 class="content-title mb-2">View Profile</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">
                            <i class="side-menu__icon fe fe-box"></i> - Profile
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="main-content-body">
            <div class="row row-sm mt-4 mouse">
                <div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
                            <div class="card">
                        <div class="card-body">
                            @if($hasEditUserPermission || $role == 'Super Admin')
                            <div class="edit btn btn-primary"><a href="/users/{{$user->id}}/edit" style="color:white !important;">EDIT</a></div>
                            {{-- <div class="edit btn btn-light"><a href="/users/{{$user->id}}/edit" style="colur:white !important;">EDIT<i class="fa-solid fa-pen"></i></a></div> --}}
                            @endif
                            <h2>Profile Details</h2>
                            <div class="card">
                                <div class="card-body" width="500px">
                                    <center>
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td>Name</td>
                                                    <td>:</td>
                                                    <td>{{$user->name}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Role</td>
                                                    <td>:</td>
                                                    <td>{{$user->role}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Email</td>
                                                    <td>:</td>
                                                    <td>{{$user->email}}</td>
                                                </tr>
                                                @if($user->role == "Data Entry")
                                                <tr>
                                                    <td>Hospital Name</td>
                                                    <td>:</td>
                                                    <td>{{$user->hospital_name}}</td>
                                                </tr>
                                                @endif
                                                @if (!empty($user->sign))
                                                    <tr>
                                                        <td>Signature</td>
                                                        <td>:</td>
                                                        <td>
                                                            {{-- @if (File::exists(storage_path('public/' . $user->sign))) --}}
                                                            <img src="{{ asset($user->sign) }}" alt="Signature" style="max-width: 200px; height: auto;">
                                                            {{-- @endif --}}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Signature Name</td>
                                                        <td>:</td>
                                                        <td>{{$user->sign_name}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Signature Designation</td>
                                                        <td>:</td>
                                                        <td>{{$user->sign_designation}}</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </center>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
            </div>
            @endif
            @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
            </div>
            @endif
        </div>
    </div>
</div>
<script>
    jQuery('.validatedForm').validate({
        rules: {
            password: {
                minlength: 5
            },
            c_password: {
                minlength: 5,
                equalTo: "#password"
            }
        }
    });

    $('button').click(function() {
        var id = $('#password').val();
        var id1 = $('#c_password').val();
        var id2 = $('#current_password').val();
        if ((id == id1) && (id != '') && (id1 != '')) {
            // window.location.href = "{{ url('change_password')}}"+"/"+id+"/"+id2;
        }
    });
</script>

<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }

    th,
    td {
        text-align: left;
        padding: 8px;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }
</style>
@endsection
