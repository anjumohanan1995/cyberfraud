@extends('layouts.app')

@section('content')
<style>
    .card[disabled] {
    opacity: 1; /* Reduce opacity to indicate disabled state */
    pointer-events: none; /* Disable mouse events */
    filter: grayscale(0%); /* Optionally desaturate/grayscale */
    /* Add more styles as needed */
}
</style>
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
                            <a href="#">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Project
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- /breadcrumb -->
        <!-- main-content-body -->
        <div class="main-content-body">
            <div class="row row-sm">
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <a href="{{ url('case-data') }}">
                        <div class="card overflow-hidden project-card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="my-auto">
                                        <svg enable-background="new 0 0 438.891 438.891"
                                            class="mr-4 ht-60 wd-60 my-auto danger" version="1.1"
                                            viewBox="0 0 438.89 438.89" xml:space="preserve"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="m347.97 57.503h-39.706v-17.763c0-5.747-6.269-8.359-12.016-8.359h-30.824c-7.314-20.898-25.6-31.347-46.498-31.347-20.668-0.777-39.467 11.896-46.498 31.347h-30.302c-5.747 0-11.494 2.612-11.494 8.359v17.763h-39.707c-23.53 0.251-42.78 18.813-43.886 42.318v299.36c0 22.988 20.898 39.706 43.886 39.706h257.04c22.988 0 43.886-16.718 43.886-39.706v-299.36c-1.106-23.506-20.356-42.068-43.886-42.319zm-196.44-5.224h28.735c5.016-0.612 9.045-4.428 9.927-9.404 3.094-13.474 14.915-23.146 28.735-23.51 13.692 0.415 25.335 10.117 28.212 23.51 0.937 5.148 5.232 9.013 10.449 9.404h29.78v41.796h-135.84v-41.796zm219.43 346.91c0 11.494-11.494 18.808-22.988 18.808h-257.04c-11.494 0-22.988-7.314-22.988-18.808v-299.36c1.066-11.964 10.978-21.201 22.988-21.42h39.706v26.645c0.552 5.854 5.622 10.233 11.494 9.927h154.12c5.98 0.327 11.209-3.992 12.016-9.927v-26.646h39.706c12.009 0.22 21.922 9.456 22.988 21.42v299.36z">
                                            </path>
                                            <path
                                                d="m179.22 233.57c-3.919-4.131-10.425-4.364-14.629-0.522l-33.437 31.869-14.106-14.629c-3.919-4.131-10.425-4.363-14.629-0.522-4.047 4.24-4.047 10.911 0 15.151l21.42 21.943c1.854 2.076 4.532 3.224 7.314 3.135 2.756-0.039 5.385-1.166 7.314-3.135l40.751-38.661c4.04-3.706 4.31-9.986 0.603-14.025-0.19-0.211-0.391-0.412-0.601-0.604z">
                                            </path>
                                            <path
                                                d="m329.16 256.03h-120.16c-5.771 0-10.449 4.678-10.449 10.449s4.678 10.449 10.449 10.449h120.16c5.771 0 10.449-4.678 10.449-10.449s-4.678-10.449-10.449-10.449z">
                                            </path>
                                            <path
                                                d="m179.22 149.98c-3.919-4.131-10.425-4.364-14.629-0.522l-33.437 31.869-14.106-14.629c-3.919-4.131-10.425-4.364-14.629-0.522-4.047 4.24-4.047 10.911 0 15.151l21.42 21.943c1.854 2.076 4.532 3.224 7.314 3.135 2.756-0.039 5.385-1.166 7.314-3.135l40.751-38.661c4.04-3.706 4.31-9.986 0.603-14.025-0.19-0.211-0.391-0.412-0.601-0.604z">
                                            </path>
                                            <path
                                                d="m329.16 172.44h-120.16c-5.771 0-10.449 4.678-10.449 10.449s4.678 10.449 10.449 10.449h120.16c5.771 0 10.449-4.678 10.449-10.449s-4.678-10.449-10.449-10.449z">
                                            </path>
                                            <path
                                                d="m179.22 317.16c-3.919-4.131-10.425-4.363-14.629-0.522l-33.437 31.869-14.106-14.629c-3.919-4.131-10.425-4.363-14.629-0.522-4.047 4.24-4.047 10.911 0 15.151l21.42 21.943c1.854 2.076 4.532 3.224 7.314 3.135 2.756-0.039 5.385-1.166 7.314-3.135l40.751-38.661c4.04-3.706 4.31-9.986 0.603-14.025-0.19-0.21-0.391-0.411-0.601-0.604z">
                                            </path>
                                            <path
                                                d="m329.16 339.63h-120.16c-5.771 0-10.449 4.678-10.449 10.449s4.678 10.449 10.449 10.449h120.16c5.771 0 10.449-4.678 10.449-10.449s-4.678-10.449-10.449-10.449z">
                                            </path>
                                        </svg>

                                    </div>
                                    <div class="project-content">
                                        <h6>Total NCRP Cases</h6>
                                        <ul>
                                            <li>
                                                <strong>Total Number Of Cases:</strong>
                                                <span>{{ $totalComplaints }}</span>
                                            </li>
                                            <li hidden>
                                                <strong>Accepted</strong>
                                                <span>16</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <a href="{{url('case-data-others')}}">
                    <div class="card overflow-hidden project-card">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="my-auto">
                                    <svg enable-background="new 0 0 438.891 438.891" class="mr-4 ht-60 wd-60 my-auto danger"
                                        version="1.1" viewBox="0 0 438.89 438.89" xml:space="preserve"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M347.97,57.503h-39.706V39.74c0-5.747-6.269-8.359-12.016-8.359H265.424c-7.314-20.898-25.6-31.347-46.498-31.347
                                            c-20.668-0.777-39.467,11.896-46.498,31.347h-30.302c-5.747,0-11.494,2.612-11.494,8.359v17.763H91.424
                                            c-23.53,0.251-42.78,18.813-43.886,42.318v299.36c0,22.988,20.898,39.706,43.886,39.706h257.04c22.988,0,43.886-16.718,43.886-39.706
                                            v-299.36C390.75,76.316,371.5,57.754,347.97,57.503z M151.53,52.279h28.735c5.016-0.612,9.045-4.428,9.927-9.404
                                            c3.094-13.474,14.915-23.146,28.735-23.51c13.692,0.415,25.335,10.117,28.212,23.51c0.937,5.148,5.232,9.013,10.449,9.404h29.78
                                            v41.796H151.53V52.279z M370.96,399.189c0,11.494-11.494,18.808-22.988,18.808h-257.04c-11.494,0-22.988-7.314-22.988-18.808v-299.36
                                            c1.066-11.964,10.978-21.201,22.988-21.42h39.706v26.645c0.552,5.854,5.622,10.233,11.494,9.927h154.12
                                            c5.98,0.327,11.209-3.992,12.016-9.927v-26.646h39.706c12.009,0.22,21.922,9.456,22.988,21.42V399.189z" />
                                        <path
                                            d="M219.445,182.007h-15.659v-15.659c0-6.279-5.103-11.382-11.382-11.382s-11.382,5.103-11.382,11.382v15.659h-15.659
                                            c-6.279,0-11.382,5.103-11.382,11.382s5.103,11.382,11.382,11.382h15.659v15.659c0,6.279,5.103,11.382,11.382,11.382
                                            s11.382-5.103,11.382-11.382v-15.659h15.659c6.279,0,11.382-5.103,11.382-11.382S225.724,182.007,219.445,182.007z" />
                                    </svg>

                                </div>
                                <div class="project-content">
                                    <h6>Total Other Cases</h6>
                                    <ul>
                                        <li>
                                            <strong>Number Of Other Cases:</strong>
                                            <span>{{ $totalOtherComplaints }}</span>
                                        </li>
                                        <li hidden>
                                            <strong>Completed</strong>
                                            <span>23</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    </a>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <a href="{{url('muleaccount')}}">
                    <div class="card overflow-hidden project-card">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="my-auto">
                                    <svg enable-background="new 0 0 469.682 469.682" version="1.1"
                                        class="mr-4 ht-60 wd-60 my-auto primary" viewBox="0 0 469.68 469.68"
                                        xml:space="preserve" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M234.84,0C105.151,0,0,105.151,0,234.84c0,129.69,105.151,234.84,234.84,234.84c129.69,0,234.84-105.151,234.84-234.84
                                            C469.68,105.151,364.529,0,234.84,0z M347.748,331.639c-5.644,5.644-14.804,5.644-20.448,0l-77.528-77.528l-77.528,77.528
                                            c-5.644,5.644-14.804,5.644-20.448,0c-5.644-5.644-5.644-14.804,0-20.448l77.528-77.528l-77.528-77.528
                                            c-5.644-5.644-5.644-14.804,0-20.448s14.804-5.644,20.448,0l77.528,77.528l77.528-77.528c5.644-5.644,14.804-5.644,20.448,0
                                            s5.644,14.804,0,20.448l-77.528,77.528l77.528,77.528C353.392,316.835,353.392,325.995,347.748,331.639z" />
                                    </svg>

                                </div>
                                <div class="project-content">
                                    <h6>Mule account</h6>
                                    <ul>
                                        <li>
                                            <strong>Mule Account Count:</strong>
                                            <span>{{ $muleAccountCount }}</span>
                                        </li>
                                        <li hidden>
                                            <strong>Paid</strong>
                                            <span>56</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    </a>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <div class="card overflow-hidden project-card">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="my-auto">
                                    <svg enable-background="new 0 0 512 512" class="mr-4 ht-60 wd-60 my-auto warning"
                                        version="1.1" viewBox="0 0 512 512" xml:space="preserve"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M464,64H48C21.49,64,0,85.49,0,112v288c0,26.51,21.49,48,48,48h416c26.51,0,48-21.49,48-48V112
                                        C512,85.49,490.51,64,464,64z M480,400c0,8.822-7.178,16-16,16H48c-8.822,0-16-7.178-16-16V112c0-8.822,7.178-16,16-16h416
                                        c8.822,0,16,7.178,16,16V400z" />
                                        <path d="M256,176c-44.112,0-80,35.888-80,80s35.888,80,80,80s80-35.888,80-80S300.112,176,256,176z M256,304
                                        c-26.468,0-48-21.532-48-48s21.532-48,48-48s48,21.532,48,48S282.468,304,256,304z" />
                                        <path d="M112,128h32v32h-32V128z" />
                                        <path d="M368,128h32v32h-32V128z" />
                                        <path d="M112,352h32v32h-32V352z" />
                                        <path d="M368,352h32v32h-32V352z" />
                                        <rect x="144" y="192" width="224" height="32" />
                                        <rect x="144" y="288" width="224" height="32" />
                                        <path d="M464,84H48C28.8,84,16,96.8,16,116v280c0,19.2,12.8,32,32,32h416c19.2,0,32-12.8,32-32V116
                                        C496,96.8,483.2,84,464,84z M480,396c0,8.8-7.2,16-16,16H48c-8.8,0-16-7.2-16-16V116c0-8.8,7.2-16,16-16h416
                                        c8.8,0,16,7.2,16,16V396z" />
                                        <path d="M400,216h-8v-8c0-4.4-3.6-8-8-8h-64c-4.4,0-8,3.6-8,8v8h-8c-4.4,0-8,3.6-8,8v64c0,4.4,3.6,8,8,8h8v8
                                        c0,4.4,3.6,8,8,8h64c4.4,0,8-3.6,8-8v-8h8c4.4,0,8-3.6,8-8v-64C408,219.6,404.4,216,400,216z M344,272c-13.3,0-24-10.7-24-24
                                        s10.7-24,24-24s24,10.7,24,24S357.3,272,344,272z" />
                                        <path d="M192,216h-8v-8c0-4.4-3.6-8-8-8h-64c-4.4,0-8,3.6-8,8v8h-8c-4.4,0-8,3.6-8,8v64c0,4.4,3.6,8,8,8h8v8
                                        c0,4.4,3.6,8,8,8h64c4.4,0,8-3.6,8-8v-8h8c4.4,0,8-3.6,8-8v-64C200,219.6,196.4,216,192,216z M136,272c-13.3,0-24-10.7-24-24
                                        s10.7-24,24-24s24,10.7,24,24S149.3,272,136,272z" />
                                    </svg>

                                </div>
                                <div class="project-content">
                                    {{-- <h6>Amount Retrived</h6> --}}
                                    <h6>Amount Pending</h6>
                                    <ul>
                                        <li>
                                            {{-- <strong>Total Amount Retrived:</strong> --}}
                                            <strong>Total Amount Pending:</strong>
                                            <span>{{ $pending_amount }}</span>
                                        </li>
                                        <li hidden>
                                            <strong>Expensive</strong>
                                            <span>$8,147</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- row -->
            <div class="row row-sm">
                <div class="col-xl-8 col-lg-12 col-md-12 col-sm-12" hidden>
                    <div class="card overflow-hidden">
                        <div class="card-header bg-transparent pd-b-0 pd-t-20 bd-b-0">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mg-b-10">
                                    Project Budget
                                </h4>
                                <i class="mdi mdi-dots-horizontal text-gray">
                                </i>
                            </div>
                            <p class="tx-12 text-muted mb-2">
                                The Project Budget is a tool used by
                                project managers to estimate the
                                total cost of a project. A project
                                budget template includes a detailed
                                estimate of all costs.
                                <a href="">Learn more</a>
                            </p>
                        </div>
                        <div class="card-body pd-y-7">
                            <div class="chartjs-size-monitor"
                                style="
                                                position: absolute;
                                                inset: 0px;
                                                overflow: hidden;
                                                pointer-events: none;
                                                visibility: hidden;
                                                z-index: -1;
                                            ">
                                <div class="chartjs-size-monitor-expand"
                                    style="
                                                    position: absolute;
                                                    left: 0;
                                                    top: 0;
                                                    right: 0;
                                                    bottom: 0;
                                                    overflow: hidden;
                                                    pointer-events: none;
                                                    visibility: hidden;
                                                    z-index: -1;
                                                ">
                                    <div
                                        style="
                                                        position: absolute;
                                                        width: 1000000px;
                                                        height: 1000000px;
                                                        left: 0;
                                                        top: 0;
                                                    ">
                                    </div>
                                </div>
                                <div class="chartjs-size-monitor-shrink"
                                    style="
                                                    position: absolute;
                                                    left: 0;
                                                    top: 0;
                                                    right: 0;
                                                    bottom: 0;
                                                    overflow: hidden;
                                                    pointer-events: none;
                                                    visibility: hidden;
                                                    z-index: -1;
                                                ">
                                    <div
                                        style="
                                                        position: absolute;
                                                        width: 200%;
                                                        height: 200%;
                                                        left: 0;
                                                        top: 0;
                                                    ">
                                    </div>
                                </div>
                            </div>
                            <div class="area chart-legend mb-0">
                                <div>
                                    <i class="mdi mdi-album text-primary mr-2">
                                    </i>
                                    Total Budget
                                </div>
                                <div>
                                    <i class="mdi mdi-album text-pink mr-2">
                                    </i>Amount Used
                                </div>
                            </div>
                            <canvas id="project-budget" class="ht-300 chartjs-render-monitor" width="831" height="415"
                                style="
                                                display: block;
                                                width: 831px;
                                                height: 415px;
                                            ">
                            </canvas>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-4" hidden>
                    <div class="card overflow-hidden">
                        <div class="card-body pb-3">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mg-b-10">
                                    project &amp; task
                                </h4>
                                <i class="mdi mdi-dots-horizontal text-gray">
                                </i>
                            </div>
                            <p class="tx-12 text-muted mb-3">
                                In project, a task is an activity
                                that needs to be accomplished within
                                a defined period of time or by a
                                deadline. <a href="">Learn more</a>
                            </p>
                            <div class="table-responsive mb-0 projects-stat tx-14">
                                <table
                                    class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap">
                                    <thead>
                                        <tr>
                                            <th>
                                                Project &amp; Task
                                            </th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="project-names">
                                                    <h6
                                                        class="bg-primary-transparent text-primary d-inline-block mr-2 text-center">
                                                        U
                                                    </h6>
                                                    <p class="d-inline-block font-weight-semibold mb-0">
                                                        UI Design
                                                    </p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="badge badge-success">
                                                    Completed
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="project-names">
                                                    <h6
                                                        class="bg-pink-transparent text-pink d-inline-block text-center mr-2">
                                                        R
                                                    </h6>
                                                    <p class="d-inline-block font-weight-semibold mb-0">
                                                        Landing Page
                                                    </p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="badge badge-warning">
                                                    Pending
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="project-names">
                                                    <h6
                                                        class="bg-success-transparent text-success d-inline-block mr-2 text-center">
                                                        W
                                                    </h6>
                                                    <p class="d-inline-block font-weight-semibold mb-0">
                                                        Website
                                                        &amp; Blog
                                                    </p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="badge badge-danger">
                                                    Canceled
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="project-names">
                                                    <h6
                                                        class="bg-purple-transparent text-purple d-inline-block mr-2 text-center">
                                                        P
                                                    </h6>
                                                    <p class="d-inline-block font-weight-semibold mb-0">
                                                        Product
                                                        Development
                                                    </p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="badge badge-teal">
                                                    on-going
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="project-names">
                                                    <h6
                                                        class="bg-danger-transparent text-danger d-inline-block mr-2 text-center">
                                                        L
                                                    </h6>
                                                    <p class="d-inline-block font-weight-semibold mb-0">
                                                        Logo Design
                                                    </p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="badge badge-success">
                                                    Completed
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container mt-5">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="yearSelect" class="form-label">Select Year:</label>
                        <select id="yearSelect" class="form-select">
                            <option value="">Select Year</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="monthSelect" class="form-label">Select Month:</label>
                        <select id="monthSelect" class="form-select">
                            <option value="">Select Month</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="daySelect" class="form-label">Select Day:</label>
                        <select id="daySelect" class="form-select">
                            <option value="">Select Day</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="sourceSelect" class="form-label">Select Source Type:</label>
                        <select id="sourceSelect" class="form-select">
                            <option value="NCRP" selected>NCRP</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="chartTypeSelect" class="form-label">Select Chart Type:</label>
                        <select id="chartTypeSelect" class="form-select">
                            <option value="bar" selected>Bar Chart</option>
                            <option value="line">Line Chart</option>
                            <option value="pie">Pie Chart</option>
                        </select>
                    </div>
                </div>

                <div class="row" id="chart">
                    <div class="col-md-6 mb-4">
                        <div class="card" disabled>
                            <div class="card-header">
                                <h2 class="card-title">Cases per Day</h2>
                            </div>
                            <div class="card-body">
                                <canvas id="casesPerDayChart" width="800" height="400"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card" disabled>
                            <div class="card-header">
                                <h2 class="card-title">Cases per Month</h2>
                            </div>
                            <div class="card-body">
                                <canvas id="casesPerMonthChart" width="800" height="400"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <div class="card" disabled>
                            <div class="card-header">
                                <h2 class="card-title">Cases per Year</h2>
                            </div>
                            <div class="card-body">
                                <canvas id="casesPerYearChart" width="800" height="400"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /row -->
    </div>
    <!-- /container -->

            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

            <script>
                $(document).ready(function() {
                    var currentYear = new Date().getFullYear();

                    // Populate years dropdown
                    for (var year = currentYear; year >= currentYear - 5; year--) {
                        $('#yearSelect').append($('<option>', {
                            value: year,
                            text: year,
                            selected: year == currentYear // Set current year as selected
                        }));
                    }

                    // Populate months dropdown using Moment.js for month names
                    for (var month = 1; month <= 12; month++) {
                        $('#monthSelect').append($('<option>', {
                            value: month,
                            text: moment(month, 'MM').format('MMMM'),
                            selected: month == new Date().getMonth() + 1 // Set current month as selected
                        }));
                    }

                    // Function to populate days dropdown based on selected year and month
                    function populateDaysDropdown() {
                        var selectedYear = $('#yearSelect').val() || currentYear;
                        var selectedMonth = $('#monthSelect').val() || new Date().getMonth() + 1;
                        var daysInMonth = new Date(selectedYear, selectedMonth, 0).getDate();
                        $('#daySelect').empty();
                        for (var day = 1; day <= daysInMonth; day++) {
                            $('#daySelect').append($('<option>', {
                                value: day,
                                text: day,
                                selected: day == new Date().getDate() // Set current day as selected
                            }));
                        }
                    }

                    // Initial population of days dropdown
                    populateDaysDropdown();

                    // Event listeners for month and year changes
                    $('#monthSelect, #yearSelect').change(function() {
                        populateDaysDropdown();
                    });

                    // Function to fetch data via AJAX and render charts
                    function fetchData(year, month, day, source) {
                        $.ajax({
                            url: '{{ route("complaints.chart") }}', // Replace with your actual route
                            method: 'GET',
                            data: { year: year, month: month, day: day, source: source },
                            success: function(data) {
                                renderCharts($('#chartTypeSelect').val(), data);
                            },
                            error: function(xhr, status, error) {
                                console.error(error);
                            }
                        });
                    }

                    // Function to render charts based on selected parameters
                    function renderCharts(chartType, data) {
                        var casesPerDay = data.cases_per_day || {};
                        var casesPerMonth = data.cases_per_month || {};
                        var casesPerYear = data.cases_per_year || {};


                        switch(chartType) {
                            case 'bar':
                                renderBarChart('Cases per Day', casesPerDay, 'casesPerDayChart');
                                renderBarChart('Cases per Month', casesPerMonth, 'casesPerMonthChart');
                                renderBarChart('Cases per Year', casesPerYear, 'casesPerYearChart');
                                break;
                            case 'line':
                                renderLineChart('Cases per Day', casesPerDay, 'casesPerDayChart');
                                renderLineChart('Cases per Month', casesPerMonth, 'casesPerMonthChart');
                                renderLineChart('Cases per Year', casesPerYear, 'casesPerYearChart');
                                break;
                            case 'pie':
                                renderPieChart('Cases per Day', casesPerDay, 'casesPerDayChart');
                                renderPieChart('Cases per Month', casesPerMonth, 'casesPerMonthChart');
                                renderPieChart('Cases per Year', casesPerYear, 'casesPerYearChart');
                                break;
                            default:
                                console.error('Invalid chart type selected');
                        }
                    }

                    // Event listener for year, month, day, and source changes
                    $('#yearSelect, #monthSelect, #daySelect, #sourceSelect').change(function() {
                        var selectedYear = $('#yearSelect').val();
                        var selectedMonth = $('#monthSelect').val();
                        var selectedDay = $('#daySelect').val();
                        var selectedSource = $('#sourceSelect').val();
                        fetchData(selectedYear, selectedMonth, selectedDay, selectedSource);
                    });

                    // Initial loading with default values
                    fetchData(currentYear, $('#monthSelect').val(), $('#daySelect').val(), 'NCRP');

                    // Event listener for chart type change
                    $('#chartTypeSelect').change(function() {
                        fetchData($('#yearSelect').val(), $('#monthSelect').val(), $('#daySelect').val(), $('#sourceSelect').val());
                    });

                    // Function to render bar chart
                    function renderBarChart(title, values, elementId) {
                        var ctx = document.getElementById(elementId);
                        window[elementId] = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: Object.keys(values),
                                datasets: [{
                                    label: title,
                                    data: Object.values(values),
                                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    }

                    // Function to render line chart
                    function renderLineChart(title, values, elementId) {
                        var ctx = document.getElementById(elementId);
                        window[elementId] = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: Object.keys(values),
                                datasets: [{
                                    label: title,
                                    data: Object.values(values),
                                    fill: false,
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    }

                    // Function to render pie chart
                    function renderPieChart(title, values, elementId) {
                        var ctx = document.getElementById(elementId);
                        window[elementId] = new Chart(ctx, {
                            type: 'pie',
                            data: {
                                labels: Object.keys(values),
                                datasets: [{
                                    label: title,
                                    data: Object.values(values),
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.2)',
                                        'rgba(54, 162, 235, 0.2)',
                                        'rgba(255, 206, 86, 0.2)',
                                        'rgba(75, 192, 192, 0.2)',
                                        'rgba(153, 102, 255, 0.2)',
                                        'rgba(255, 159, 64, 0.2)'
                                    ],
                                    borderColor: [
                                        'rgba(255, 99, 132, 1)',
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 206, 86, 1)',
                                        'rgba(75, 192, 192, 1)',
                                        'rgba(153, 102, 255, 1)',
                                        'rgba(255, 159, 64, 1)'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                    },
                                    title: {
                                        display: true,
                                        text: title
                                    }
                                }
                            }
                        });
                    }
                });
            </script>

@endsection
