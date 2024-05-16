<html lang="en">

<head>
    <style data-styles="">
        ion-icon {
            visibility: hidden;
        }

        .hydrated {
            visibility: inherit;
        }
    </style>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="Description" content="Bootstrap Responsive Admin Web Dashboard HTML5 Template" />
    <meta name="Author" content="Spruko Technologies Private Limited" />
    <meta name="Keywords"
        content="admin,admin dashboard,admin dashboard template,admin panel template,admin template,admin theme,bootstrap 4 admin template,bootstrap 4 dashboard,bootstrap admin,bootstrap admin dashboard,bootstrap admin panel,bootstrap admin template,bootstrap admin theme,bootstrap dashboard,bootstrap form template,bootstrap panel,bootstrap ui kit,dashboard bootstrap 4,dashboard design,dashboard html,dashboard template,dashboard ui kit,envato templates,flat ui,html,html and css templates,html dashboard template,html5,jquery html,premium,premium quality,sidebar bootstrap 4,template admin bootstrap 4" />
    <!-- Title -->
    <title>
        Cyber fraud and social media
    </title>
    <!--- Favicon --->
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

    <link href="{{ asset('css/jquery.dataTables.min.css') }}" rel="stylesheet" />

    <link href="{{ asset('https://editor.datatables.net/extensions/Editor/css/editor.dataTables.css') }}" rel="stylesheet" />
    
    <script src="{{ asset('js/jquery.min.js') }}"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- coustom style to render   --}}
    @yield('styles');



    <meta http-equiv="imagetoolbar" content="no" />

    <script src="https://www.spruko.com/demo/azira/Azira/assets/plugins/ionicons/ionicons/ionicons.suuqn5vt.js"
        type="module" crossorigin="true"
        data-resources-url="https://www.spruko.com/demo/azira/Azira/assets/plugins/ionicons/ionicons/"
        data-namespace="ionicons"></script>
</head>

<body class="main-body app sidebar-mini">
    <!-- Loader -->
    <div id="global-loader" style="display: none">
        <img src="img/loaders/loader-4.svg" class="loader-img" alt="Loader" />
    </div>
    <!-- /Loader -->

    <!-- page -->
    <div class="page">
        <!-- main-sidebar opened -->
        <div class="app-sidebar__overlay active" data-toggle="sidebar"></div>



        {{-- included side bar. start --}}
        @include('includes.sidebar-left')
        {{-- included side bar. end --}}


        <!-- main-content -->
        <div class="main-content">

            <!-- main-header -->
            @include('includes.header')
            <!-- /main-header -->


            {{-- main content yield  --}}
            @yield('content')


        </div>
        <!-- /main-content -->

        {{-- included side bar. start --}}
        @include('includes.sidebar-right')
        {{-- included side bar. end --}}





        @include('includes.footer')

    </div>




    <!-- page closed -->
    <!--- Back-to-top --->
    <a href="#top" id="back-to-top" style="display: none">
        <i class="las la-angle-double-up"> </i>
    </a>

    <!-- Datatable CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css"/>

    <!-- jQuery Library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Datatable JS -->
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>

    {{-- <script src="{{ asset('js/jquery.min.js') }}"></script> --}}

    <script src="{{ asset('js/datepicker.js') }}"></script>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

    <script src="{{ asset('js/ionicons.js') }}"></script>

    <script src="{{ asset('js/Chart.bundle.min.js') }}"></script>

    <script src="{{ asset('js/jquery.sparkline.min.js') }}"></script>

    <script src="{{ asset('js/chart.flot.sampledata.js') }}"></script>

    <script src="{{ asset('js/jquery.rating-stars.js') }}"></script>

    <script src="{{ asset('js/jquery.barrating.js') }}"></script>

    <script src="{{ asset('js/eva-icons.min.js') }}"></script>

    <script src="{{ asset('js/moment.js') }}"></script>

    <script src="{{ asset('js/perfect-scrollbar.min.js') }}"></script>

    <script src="{{ asset('js/p-scroll.js') }}"></script>

    <script src="{{ asset('js/switcher.js') }}"></script>

    <script src="{{ asset('js/sidemenu.js') }}"></script>

    <script src="{{ asset('js/sidebar.js') }}"></script>

    <script src="{{ asset('js/sidebar-custom.js') }}"></script>

    <script src="{{ asset('js/raphael.min.js') }}"></script>

    <script src="{{ asset('js/morris.min.js') }}"></script>

    <script src="{{ asset('js/script.js') }}"></script>

    <script src="{{ asset('js/index.js') }}"></script>

    <script src="{{ asset('js/switcher.js') }}"></script>

    <script src="{{ asset('js/custom.js') }}"></script>

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>




    {{-- coustom scripts can be put here  --}}
    @yield('scripts')
    {{-- coustom scripts can be put here  --}}
</body>

</html>
