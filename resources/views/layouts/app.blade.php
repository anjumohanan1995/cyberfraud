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
    <link href="{{ asset('css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />

    <link href="{{ asset('css/style.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/skin-modes.css') }}" rel="stylesheet" />
    <!--- Sidemenu css --->
    <link href="{{ asset('css/sidemenu.css') }}" rel="stylesheet" />
    <!--- Animations css --->
    <link href="{{ asset('css/animate.css') }}" rel="stylesheet" />
    <!-- Switcher css -->
    <link href="{{ asset('css/switcher.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/demo.css') }}" rel="stylesheet" />


    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">


    <link href="{{ asset('css/jquery.dataTables.min.css') }}" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css">

    <script src="{{ asset('js/jquery.min.js') }}"></script>

        <!-- Include SweetAlert CSS and JS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- coustom style to render   --}}
    @yield('styles')



    <meta http-equiv="imagetoolbar" content="no" />

    {{-- <script src="https://www.spruko.com/demo/azira/Azira/assets/plugins/ionicons/ionicons/ionicons.suuqn5vt.js"
        type="module" crossorigin="true"
        data-resources-url="https://www.spruko.com/demo/azira/Azira/assets/plugins/ionicons/ionicons/"
        data-namespace="ionicons"></script> --}}
</head>

<body class="main-body app sidebar-mini">
    <!-- Loader -->
    <div id="global-loader" style="display: none">
        <img src="{{ asset('img/loader.gif') }}" class="loader-img" alt="Loader" />
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

    {{-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css" /> --}}
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.min.css"> --}}

    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- jQuery Library -->
     <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Datatable JS -->
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>


    {{-- <script src="{{ asset('js/jquery.min.js') }}"></script> --}}

    <script src="{{ asset('js/datepicker.js') }}"></script>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

    {{-- <script src="{{ asset('js/ionicons.js') }}"></script> --}}

    <script src="{{ asset('js/Chart.bundle.min.js') }}"></script>

    {{-- <script src="{{ asset('js/jquery.sparkline.min.js') }}"></script> --}}

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

    <script src="{{ asset('js/ckeditor/ckeditor.js')}}"></script>
        {{-- <script type="text/javascript">
            $(document).ready(function() {
                $('.ckeditor').ckeditor();
            });

        </script> --}}

    {{-- <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script> --}}

    {{-- for datatable export --}}

    {{-- <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script> --}}


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  {{-- for datatable export --}}


    {{-- coustom scripts can be put here  --}}
    @yield('scripts')
    {{-- coustom scripts can be put here  --}}
</body>

</html>
