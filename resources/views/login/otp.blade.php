<html lang="en">

<head>
    <style data-styles="">
        ion-icon {
            visibility: hidden
        }

        .hydrated {
            visibility: inherit
        }

    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="Description" content="Bootstrap Responsive Admin Web Dashboard HTML5 Template">
    <meta name="Author" content="Spruko Technologies Private Limited">
    <meta name="Keywords"
        content="admin,admin dashboard,admin dashboard template,admin panel template,admin template,admin theme,bootstrap 4 admin template,bootstrap 4 dashboard,bootstrap admin,bootstrap admin dashboard,bootstrap admin panel,bootstrap admin template,bootstrap admin theme,bootstrap dashboard,bootstrap form template,bootstrap panel,bootstrap ui kit,dashboard bootstrap 4,dashboard design,dashboard html,dashboard template,dashboard ui kit,envato templates,flat ui,html,html and css templates,html dashboard template,html5,jquery html,premium,premium quality,sidebar bootstrap 4,template admin bootstrap 4">
    <!-- Title -->
    <title>Cyber froud and social media</title> <!--- Favicon --->
    <link rel="icon" href="{{ asset('img/favicon.png') }}" type="image/x-icon" />
    <!--- Icons css --->
    <link href="{{ asset('css/icons.css') }}" rel="stylesheet" />
    <!-- Owl-carousel css-->
    <link href="{{ asset('css/owl.carousel.css') }}" rel="stylesheet" />
    <!--- Right-sidemenu css --->
    <link href="{{ asset('css/sidebar.css') }}" rel="stylesheet" />
    <!--- Style css --->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/skin-modes.css') }}" rel="stylesheet" />
    <!--- Sidemenu css --->
    <link href="{{ asset('css/sidemenu.css') }}" rel="stylesheet" />
    <!--- Animations css --->
    <link href="{{ asset('css/animate.css') }}" rel="stylesheet" />
    <!-- Switcher css -->
    <link href="{{ asset('css/switcher.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/demo.css') }}" rel="stylesheet" />
      <link href="{{ asset('css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />

    <meta http-equiv="imagetoolbar" content="no">

    {{-- <script src="https://www.spruko.com/demo/azira/Azira/assets/plugins/ionicons/ionicons/ionicons.suuqn5vt.js"
        type="module" crossorigin="true"
        data-resources-url="https://www.spruko.com/demo/azira/Azira/assets/plugins/ionicons/ionicons/"
        data-namespace="ionicons"></script> --}}

</head>

<body class="main-body  dark-theme" style="overflow:hidden">
    <!-- Loader -->
    <div id="global-loader" style="display: none;"><img src="{{ asset('img/loader.gif') }}" class="loader-img" alt="Loader" />
    </div>
    <!-- /Loader -->
    <!-- Start Switcher -->

    <div class="page"> <!-- main-signin-wrapper -->
        <div class="my-auto page page-h bg_login">
            <div class="">
                <div class="main-card-signin d-md-flex">
                    <div class="p-5 w-100">
                        <div class="main-signin-header">
                            <img src="img/logo.png" class="" alt="" />
                            <br> <br> <br>
                            <div>

                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('success') }}
                                        {{-- <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button> --}}
                                    </div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        {{ session('error') }}
                                        {{-- <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button> --}}
                                    </div>
                                @endif
                            </div>

                            <h4><br>We had sent an OTP to your email id</h4>

                            <form action="{{ route('validate.otp') }}" method="POST" >
                                @csrf
                                <div class="form-group">
                                    <label for="email">OTP</label>
                                    <input id="otp" name="otp" class="form-control" placeholder="Enter OTP" type="text">
                                    <input type="hidden" name="userid" value="{{ $userid }}">
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <button  class="btn btn-main-primary btn-block">Submit</button>
                            </form>


                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


</body>




</html>
