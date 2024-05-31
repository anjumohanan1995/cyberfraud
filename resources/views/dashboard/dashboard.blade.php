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
                            <a href="#">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Project
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-auto">
                <div class="d-flex right-page">
                    {{--  <div class="d-flex justify-content-center mr-5">
                        <div class="">
                            <span class="d-block">
                                <span class="label">EXPENSES</span>
                            </span>
                            <span class="value"> $53,000 </span>
                        </div>
                        <div class="ml-3 mt-2">
                            <span class="sparkline_bar">
                                <canvas width="52" height="30"
                                    style="
                                                    display: inline-block;
                                                    width: 52px;
                                                    height: 30px;
                                                    vertical-align: top;
                                                ">
                                </canvas>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center">
                        <div class="">
                            <span class="d-block">
                                <span class="label">PROFIT</span>
                            </span>
                            <span class="value"> $34,000 </span>
                        </div>
                        <div class="ml-3 mt-2">
                            <span class="sparkline_bar31">
                                <canvas width="52" height="30"
                                    style="
                                                    display: inline-block;
                                                    width: 52px;
                                                    height: 30px;
                                                    vertical-align: top;
                                                ">
                                </canvas>
                            </span>
                        </div>
                    </div>  --}}
                </div>
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
                                        <h6>Total Cases</h6>
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
                    {{-- <a href="{{url('filter-case-data')}}"> --}}
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
                                    <h6>New Cases</h6>
                                    <ul>
                                        <li>
                                            <strong>Number Of New Cases:</strong>
                                            <span>{{ $newComplaints }}</span>
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
                    {{-- </a> --}}
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
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
                                            <span>5</span>
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
                                    <h6>Amount Retrived</h6>
                                    <ul>
                                        <li>
                                            <strong>Total Amount Retrived:</strong>
                                            <span>$15,425</span>
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
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <div class="row">
                <div class="container mt-5">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h2 class="card-title">Please Select Date</h2>
                                </div>
                                <div class="card-body">
                                    <input type="date" id="datePicker" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h2 class="card-title">Cases per Day</h2>
                                </div>
                                <div class="card-body">
                                    <canvas id="casesPerDayChart" width="800" height="400"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h2 class="card-title">Cases per Month</h2>
                                </div>
                                <div class="card-body">
                                    <canvas id="casesPerMonthChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h2 class="card-title">Cases per Year</h2>
                                </div>
                                <div class="card-body">
                                    <canvas id="casesPerYearChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Include Chart.js library -->


<!-- Create canvas element for the chart -->


<script>
    $(document).ready(function () {
        $('#datePicker').change(function () {
        var specifiedDate = $(this).val();

        // Make the AJAX request with the specified date
        fetchComplaintChartData(specifiedDate);
    });

    // Function to fetch complaint chart data
    function fetchComplaintChartData(specifiedDate) {
        $.ajax({
            url: '{{ route("complaints.chart") }}',
            method: 'GET',
            data: { specified_date: specifiedDate }, // Pass the specified date to the backend
            success: function (data) {
                renderChart(data);
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    }

    // Function to render the chart (same as before)
    function renderChart(data) {
        // Process data for each group (day, month, year)
        renderBarChart('Cases per Day', data.casesPerDay, '#casesPerDayChart');
        renderBarChart('Cases per Month', data.casesPerMonth, '#casesPerMonthChart');
        renderBarChart('Cases per Year', data.casesPerYear, '#casesPerYearChart');
    }


            var ctx = document.getElementById(elementId).getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: title,
                        data: values,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });

    });
</script>




            </div>
            <!-- /row -->
            <!-- row -->
            {{-- <div class="row row-sm">
                <div class="col-lg-6 col-xl-4 col-md-12 col-sm-12" hidden>
                    <div class="card overflow-hidden latest-tasks">
                        <div class="">
                            <div class="d-flex justify-content-between pl-4 pt-4 pr-4">
                                <h4 class="card-title mg-b-10">
                                    Latest Task
                                </h4>
                                <i class="mdi mdi-dots-horizontal text-gray">
                                </i>
                            </div>
                            <div class="">
                                <ul class="nav nav-tabs nav-tabs-line nav-tabs-line-brand nav-tabs-bold" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active show" data-toggle="tab" href="#tasktab-1"
                                            role="tab" aria-selected="false">
                                            Today
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#tasktab-2" role="tab"
                                            aria-selected="false">
                                            Week
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#tasktab-3" role="tab"
                                            aria-selected="true">
                                            Month
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body pt-3">
                            <div class="tab-content">
                                <div class="tab-pane fade active show" id="tasktab-1" role="tabpanel">
                                    <div class="">
                                        <div class="tasks">
                                            <div class="task-line primary active">
                                                <a href="#" class="label">
                                                    XML Import &amp;
                                                    Export
                                                </a>
                                                <div class="time">
                                                    12:00 PM
                                                </div>
                                            </div>
                                            <span class="add-delete-task">
                                                <a href="#" class="btn btn-link">
                                                    <i class="fa fa-edit">
                                                    </i>
                                                </a>
                                                <a href="" class="btn btn-link">
                                                    <i class="fa fa-trash">
                                                    </i>
                                                </a>
                                            </span>
                                            <div class="checkbox">
                                                <label class="check-box">
                                                    <label class="ckbox">
                                                        <input checked="" type="checkbox" />
                                                        <span>
                                                        </span>
                                                    </label>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="tasks">
                                            <div class="task-line pink">
                                                <a href="#" class="label">
                                                    Database
                                                    Optimization
                                                </a>
                                                <div class="time">
                                                    02:13 PM
                                                </div>
                                            </div>
                                            <span class="add-delete-task">
                                                <a href="#" class="btn btn-link">
                                                    <i class="fa fa-edit">
                                                    </i>
                                                </a>
                                                <a href="" class="btn btn-link">
                                                    <i class="fa fa-trash">
                                                    </i>
                                                </a>
                                            </span>
                                            <div class="checkbox">
                                                <label class="check-box">
                                                    <label class="ckbox">
                                                        <input type="checkbox" />
                                                        <span>
                                                        </span>
                                                    </label>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="tasks">
                                            <div class="task-line success">
                                                <a href="#" class="label">
                                                    Create
                                                    Wireframes
                                                </a>
                                                <div class="time">
                                                    06:20 PM
                                                </div>
                                            </div>
                                            <span class="add-delete-task">
                                                <a href="#" class="btn btn-link">
                                                    <i class="fa fa-edit">
                                                    </i>
                                                </a>
                                                <a href="" class="btn btn-link">
                                                    <i class="fa fa-trash">
                                                    </i>
                                                </a>
                                            </span>
                                            <div class="checkbox">
                                                <label class="check-box">
                                                    <label class="ckbox">
                                                        <input type="checkbox" />
                                                        <span>
                                                        </span>
                                                    </label>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="tasks">
                                            <div class="task-line warning">
                                                <a href="#" class="label">
                                                    Develop MVP
                                                </a>
                                                <div class="time">
                                                    10: 00 PM
                                                </div>
                                            </div>
                                            <span class="add-delete-task">
                                                <a href="#" class="btn btn-link">
                                                    <i class="fa fa-edit">
                                                    </i>
                                                </a>
                                                <a href="" class="btn btn-link">
                                                    <i class="fa fa-trash">
                                                    </i>
                                                </a>
                                            </span>
                                            <div class="checkbox">
                                                <label class="check-box">
                                                    <label class="ckbox">
                                                        <input type="checkbox" />
                                                        <span>
                                                        </span>
                                                    </label>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="tasks">
                                            <div class="task-line teal">
                                                <a href="#" class="label">
                                                    Design Ecommerce
                                                </a>
                                                <div class="time">
                                                    10: 00 PM
                                                </div>
                                            </div>
                                            <span class="add-delete-task">
                                                <a href="#" class="btn btn-link">
                                                    <i class="fa fa-edit">
                                                    </i>
                                                </a>
                                                <a href="" class="btn btn-link">
                                                    <i class="fa fa-trash">
                                                    </i>
                                                </a>
                                            </span>
                                            <div class="checkbox">
                                                <label class="check-box">
                                                    <label class="ckbox">
                                                        <input type="checkbox" />
                                                        <span>
                                                        </span>
                                                    </label>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="tasks mb-0">
                                            <div class="task-line purple">
                                                <a href="#" class="label">
                                                    Fix Validation
                                                    Issues
                                                </a>
                                                <div class="time">
                                                    12: 00 AM
                                                </div>
                                            </div>
                                            <span class="add-delete-task">
                                                <a href="#" class="btn btn-link">
                                                    <i class="fa fa-edit">
                                                    </i>
                                                </a>
                                                <a href="" class="btn btn-link">
                                                    <i class="fa fa-trash">
                                                    </i>
                                                </a>
                                            </span>
                                            <div class="checkbox">
                                                <label class="check-box">
                                                    <label class="ckbox">
                                                        <input type="checkbox" />
                                                        <span>
                                                        </span>
                                                    </label>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="tasktab-2" role="tabpanel">
                                    <div class="">
                                        <div class="tasks">
                                            <div class="task-line teal">
                                                <a href="#" class="label">
                                                    Management
                                                    meeting
                                                </a>
                                                <div class="time">
                                                    06:30 AM
                                                </div>
                                            </div>
                                            <span class="add-delete-task">
                                                <a href="#" class="btn btn-link">
                                                    <i class="fa fa-edit">
                                                    </i>
                                                </a>
                                                <a href="" class="btn btn-link">
                                                    <i class="fa fa-trash">
                                                    </i>
                                                </a>
                                            </span>
                                            <div class="checkbox">
                                                <label class="check-box">
                                                    <label class="ckbox">
                                                        <input type="checkbox" />
                                                        <span>
                                                        </span>
                                                    </label>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="tasks">
                                            <div class="task-line danger">
                                                <a href="#" class="label">
                                                    Connect API to
                                                    pages
                                                </a>
                                                <div class="time">
                                                    08:00 AM
                                                </div>
                                            </div>
                                            <span class="add-delete-task">
                                                <a href="#" class="btn btn-link">
                                                    <i class="fa fa-edit">
                                                    </i>
                                                </a>
                                                <a href="" class="btn btn-link">
                                                    <i class="fa fa-trash">
                                                    </i>
                                                </a>
                                            </span>
                                            <div class="checkbox">
                                                <label class="check-box">
                                                    <label class="ckbox">
                                                        <input type="checkbox" />
                                                        <span>
                                                        </span>
                                                    </label>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="tasks">
                                            <div class="task-line purple">
                                                <a href="#" class="label">
                                                    Icon change in
                                                    Redesign App
                                                </a>
                                                <div class="time">
                                                    11:20 AM
                                                </div>
                                            </div>
                                            <span class="add-delete-task">
                                                <a href="#" class="btn btn-link">
                                                    <i class="fa fa-edit">
                                                    </i>
                                                </a>
                                                <a href="" class="btn btn-link">
                                                    <i class="fa fa-trash">
                                                    </i>
                                                </a>
                                            </span>
                                            <div class="checkbox">
                                                <label class="check-box">
                                                    <label class="ckbox">
                                                        <input type="checkbox" />
                                                        <span>
                                                        </span>
                                                    </label>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="tasks">
                                            <div class="task-line warning">
                                                <a href="#" class="label">
                                                    Test new
                                                    features in
                                                    tablets
                                                </a>
                                                <div class="time">
                                                    02: 00 PM
                                                </div>
                                            </div>
                                            <span class="add-delete-task">
                                                <a href="#" class="btn btn-link">
                                                    <i class="fa fa-edit">
                                                    </i>
                                                </a>
                                                <a href="" class="btn btn-link">
                                                    <i class="fa fa-trash">
                                                    </i>
                                                </a>
                                            </span>
                                            <div class="checkbox">
                                                <label class="check-box">
                                                    <label class="ckbox">
                                                        <input type="checkbox" />
                                                        <span>
                                                        </span>
                                                    </label>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="tasks">
                                            <div class="task-line success">
                                                <a href="#" class="label">
                                                    Design Logo
                                                </a>
                                                <div class="time">
                                                    04: 00 PM
                                                </div>
                                            </div>
                                            <span class="add-delete-task">
                                                <a href="#" class="btn btn-link">
                                                    <i class="fa fa-edit">
                                                    </i>
                                                </a>
                                                <a href="" class="btn btn-link">
                                                    <i class="fa fa-trash">
                                                    </i>
                                                </a>
                                            </span>
                                            <div class="checkbox">
                                                <label class="check-box">
                                                    <label class="ckbox">
                                                        <input type="checkbox" />
                                                        <span>
                                                        </span>
                                                    </label>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="tasks mb-0">
                                            <div class="task-line primary">
                                                <a href="#" class="label">
                                                    Project Launch
                                                </a>
                                                <div class="time">
                                                    06: 00 PM
                                                </div>
                                            </div>
                                            <span class="add-delete-task">
                                                <a href="#" class="btn btn-link">
                                                    <i class="fa fa-edit">
                                                    </i>
                                                </a>
                                                <a href="" class="btn btn-link">
                                                    <i class="fa fa-trash">
                                                    </i>
                                                </a>
                                            </span>
                                            <div class="checkbox">
                                                <label class="check-box">
                                                    <label class="ckbox">
                                                        <input type="checkbox" />
                                                        <span>
                                                        </span>
                                                    </label>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="tasktab-3" role="tabpanel">
                                    <div class="">
                                        <div class="tasks">
                                            <div class="task-line info">
                                                <a href="#" class="label">
                                                    Design a Landing
                                                    Page
                                                </a>
                                                <div class="time">
                                                    06:12 AM
                                                </div>
                                            </div>
                                            <span class="add-delete-task">
                                                <a href="#" class="btn btn-link">
                                                    <i class="fa fa-edit">
                                                    </i>
                                                </a>
                                                <a href="" class="btn btn-link">
                                                    <i class="fa fa-trash">
                                                    </i>
                                                </a>
                                            </span>
                                            <div class="checkbox">
                                                <label class="check-box">
                                                    <label class="ckbox">
                                                        <input type="checkbox" />
                                                        <span>
                                                        </span>
                                                    </label>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="tasks">
                                            <div class="task-line danger">
                                                <a href="#" class="label">
                                                    Food Delivery
                                                    Mobile
                                                    Application
                                                </a>
                                                <div class="time">
                                                    3:00 PM
                                                </div>
                                            </div>
                                            <span class="add-delete-task">
                                                <a href="#" class="btn btn-link">
                                                    <i class="fa fa-edit">
                                                    </i>
                                                </a>
                                                <a href="" class="btn btn-link">
                                                    <i class="fa fa-trash">
                                                    </i>
                                                </a>
                                            </span>
                                            <div class="checkbox">
                                                <label class="check-box">
                                                    <label class="ckbox">
                                                        <input type="checkbox" />
                                                        <span>
                                                        </span>
                                                    </label>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="tasks">
                                            <div class="task-line warning">
                                                <a href="#" class="label">
                                                    Export Database
                                                    values
                                                </a>
                                                <div class="time">
                                                    03:20 PM
                                                </div>
                                            </div>
                                            <span class="add-delete-task">
                                                <a href="#" class="btn btn-link">
                                                    <i class="fa fa-edit">
                                                    </i>
                                                </a>
                                                <a href="" class="btn btn-link">
                                                    <i class="fa fa-trash">
                                                    </i>
                                                </a>
                                            </span>
                                            <div class="checkbox">
                                                <label class="check-box">
                                                    <label class="ckbox">
                                                        <input type="checkbox" />
                                                        <span>
                                                        </span>
                                                    </label>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="tasks">
                                            <div class="task-line pink">
                                                <a href="#" class="label">
                                                    Write Simple
                                                    Python Script
                                                </a>
                                                <div class="time">
                                                    04: 00 PM
                                                </div>
                                            </div>
                                            <span class="add-delete-task">
                                                <a href="#" class="btn btn-link">
                                                    <i class="fa fa-edit">
                                                    </i>
                                                </a>
                                                <a href="" class="btn btn-link">
                                                    <i class="fa fa-trash">
                                                    </i>
                                                </a>
                                            </span>
                                            <div class="checkbox">
                                                <label class="check-box">
                                                    <label class="ckbox">
                                                        <input type="checkbox" />
                                                        <span>
                                                        </span>
                                                    </label>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="tasks">
                                            <div class="task-line success">
                                                <a href="#" class="label">
                                                    Write Simple
                                                    Anugalr Program
                                                </a>
                                                <div class="time">
                                                    05: 00 PM
                                                </div>
                                            </div>
                                            <span class="add-delete-task">
                                                <a href="#" class="btn btn-link">
                                                    <i class="fa fa-edit">
                                                    </i>
                                                </a>
                                                <a href="" class="btn btn-link">
                                                    <i class="fa fa-trash">
                                                    </i>
                                                </a>
                                            </span>
                                            <div class="checkbox">
                                                <label class="check-box">
                                                    <label class="ckbox">
                                                        <input type="checkbox" />
                                                        <span>
                                                        </span>
                                                    </label>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="tasks mb-0">
                                            <div class="task-line primary">
                                                <a href="#" class="label">
                                                    Design PSD files
                                                </a>
                                                <div class="time">
                                                    06: 00 PM
                                                </div>
                                            </div>
                                            <span class="add-delete-task">
                                                <a href="#" class="btn btn-link">
                                                    <i class="fa fa-edit">
                                                    </i>
                                                </a>
                                                <a href="" class="btn btn-link">
                                                    <i class="fa fa-trash">
                                                    </i>
                                                </a>
                                            </span>
                                            <div class="checkbox">
                                                <label class="check-box">
                                                    <label class="ckbox">
                                                        <input type="checkbox" />
                                                        <span>
                                                        </span>
                                                    </label>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-4" hidden>
                    <div class="card overflow-hidden">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mg-b-10 mt-2">
                                    Projects Workload
                                </h4>
                                <i class="mdi mdi-dots-horizontal text-gray">
                                </i>
                            </div>
                            <p class="tx-12 text-muted mb-0">
                                In the Project Workload report,
                                their remaining assignments,
                                completion dates, whether their work
                                is at-risk.
                                <a href="">Learn more</a>
                            </p>
                        </div>
                        <div class="card-body">
                            <div class="">
                                <div class="row justify-content-md-center">
                                    <div class="col-sm-12">
                                        <div class="">
                                            <canvas id="chartDonut" class="ht-175 drop-shadow" width="170"
                                                height="170"
                                                style="
                                                                display: block;
                                                            ">
                                                <ul class="1-legend">
                                                    <li>
                                                        <span
                                                            style="
                                                                            background-color: #3858f9;
                                                                        ">
                                                        </span>External
                                                    </li>
                                                    <li>
                                                        <span
                                                            style="
                                                                            background-color: #f09819;
                                                                        ">
                                                        </span>Internal
                                                    </li>
                                                    <li>
                                                        <span
                                                            style="
                                                                            background-color: #3cba92;
                                                                        ">
                                                        </span>Other
                                                    </li>
                                                </ul>
                                            </canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="pt-3">
                                <div class="row">
                                    <div class="col-sm-8">
                                        <h5 class="mb-0 tx-15 d-flex">
                                            <span class="legend bg-primary-gradient brround">
                                            </span>40.32%
                                        </h5>
                                        <p class="text-muted tx-13 mb-0">
                                            External
                                        </p>
                                    </div>
                                    <div class="col-sm-4">
                                        <span id="sparkel1">
                                            <canvas width="94" height="30"
                                                style="
                                                                display: inline-block;
                                                                width: 94.6562px;
                                                                height: 30px;
                                                                vertical-align: top;
                                                            ">
                                            </canvas>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="pt-3">
                                <div class="row">
                                    <div class="col-sm-8">
                                        <h6 class="mb-0 tx-15 d-flex">
                                            <span class="legend bg-danger-gradient brround">
                                            </span>40.73%
                                        </h6>
                                        <p class="text-muted tx-13 mb-0">
                                            Internal
                                        </p>
                                    </div>
                                    <div class="col-sm-4">
                                        <span id="sparkel2">
                                            <canvas width="94" height="30"
                                                style="
                                                                display: inline-block;
                                                                width: 94.6562px;
                                                                height: 30px;
                                                                vertical-align: top;
                                                            ">
                                            </canvas>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="pt-3">
                                <div class="row">
                                    <div class="col-sm-8">
                                        <h6 class="mb-0 tx-15 d-flex">
                                            <span class="legend bg-success-gradient brround">
                                            </span>50.12%
                                        </h6>
                                        <p class="text-muted tx-13 mb-0">
                                            Other
                                        </p>
                                    </div>
                                    <div class="col-sm-4">
                                        <span id="sparkel3">
                                            <canvas width="94" height="30"
                                                style="
                                                                display: inline-block;
                                                                width: 94.6562px;
                                                                height: 30px;
                                                                vertical-align: top;
                                                            ">
                                            </canvas>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-xl-4 col-md-12 col-sm-12" hidden>
                    <div class="card card-dashboard-events">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mg-b-10">
                                    Upcoming Events
                                </h4>
                                <i class="mdi mdi-dots-horizontal text-gray">
                                </i>
                            </div>
                            <p class="tx-12 text-muted mb-0">
                                It had the latest news and notes
                                from the championship, while
                                previewing the upcoming event..
                                <a href="">Learn more</a>
                            </p>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <div class="list-group-item border-top-0">
                                    <div class="event-indicator bg-primary-gradient"></div>
                                    <label>Nov 20 <span>Tuesday</span>
                                    </label>
                                    <h6>
                                        PH World Mall Lantern
                                        Festival
                                    </h6>
                                    <p>
                                        <strong>8AM - 4PM</strong>
                                        Bay Area, San Francisco
                                    </p>
                                    <small>
                                        <span class="tx-danger">Sold Out</span>
                                        (3000 tickets sold)</small>
                                </div>
                                <div class="list-group-item">
                                    <div class="event-indicator bg-danger-gradient"></div>
                                    <label>Nov 23 <span>Friday</span>
                                    </label>
                                    <h6>
                                        Asia Pacific Generation
                                        Workshop
                                    </h6>
                                    <p>
                                        <strong>8AM - 5PM</strong>
                                        Singapore
                                    </p>
                                    <small>
                                        <span class="tx-warning">Sold Out Soon</span>
                                        (12 tickets left)</small>
                                </div>
                                <div class="list-group-item border-bottom-0">
                                    <div class="event-indicator bg-info-gradient"></div>
                                    <label>Nov 23 <span>Friday</span>
                                    </label>
                                    <h6>
                                        Korea Smart Device Trade
                                        Show
                                    </h6>
                                    <p>
                                        <strong>8AM - 5PM</strong>
                                        Singapore
                                    </p>
                                    <small>
                                        <span class="tx-success">Free Registration</span>
                                        (Limited seats only)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /row -->
            <!-- row -->
            <div class="row row-sm">
                <div class="col-md-12 col-xl-12">
                    <div class="card overflow-hidden review-project" hidden>
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mg-b-10">
                                    All Projects
                                </h4>
                                <i class="mdi mdi-dots-horizontal text-gray">
                                </i>
                            </div>
                            <p class="tx-12 text-muted mb-3">
                                A project is an activity to meet the
                                creation of a unique product or
                                service and thus activities that are
                                undertaken to accomplish routine
                                activities cannot be considered
                                projects.
                                <a href="">Learn more</a>
                            </p>
                            <div class="table-responsive mb-0">
                                <table
                                    class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap table-striped">
                                    <thead>
                                        <tr>
                                            <th>Project</th>
                                            <th>Team Members</th>
                                            <th>Categorie</th>
                                            <th>Created</th>
                                            <th>Status</th>
                                            <th>Deadline</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="project-contain">
                                                    <h6 class="mb-1 tx-13">
                                                        Angular
                                                        Project
                                                    </h6>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="image-grouped">
                                                    <img class="profile-img brround" alt="profile image"
                                                        src="img/2.jpg" />
                                                </div>
                                            </td>
                                            <td>Web Design</td>
                                            <td>01 Jan 2020</td>
                                            <td>
                                                <span class="badge badge-primary-gradient">Ongoing</span>
                                            </td>
                                            <td>15 March 2020</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="project-contain">
                                                    <h6 class="mb-1 tx-13">
                                                        PHP Project
                                                    </h6>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="image-grouped">
                                                    <img class="profile-img brround" alt="profile image"
                                                        src="img/7.jpg" />
                                                </div>
                                            </td>
                                            <td>Web Development</td>
                                            <td>03 March 2020</td>
                                            <td>
                                                <span class="badge badge-success-gradient">Ongoing</span>
                                            </td>
                                            <td>15 Jun 2020</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="project-contain">
                                                    <h6 class="mb-1 tx-13">
                                                        Python
                                                    </h6>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="image-grouped">
                                                    <img class="profile-img brround" alt="profile image"
                                                        src="img/15.jpg" />
                                                </div>
                                            </td>
                                            <td>Web Development</td>
                                            <td>15 March 2020</td>
                                            <td>
                                                <span class="badge badge-danger-gradient">Pending</span>
                                            </td>
                                            <td>15 March 2020</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="project-contain">
                                                    <h6 class="mb-1 tx-13">
                                                        Android App
                                                    </h6>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="image-grouped">
                                                    <img class="profile-img brround" alt="profile image"
                                                        src="img/16.jpg" />
                                                </div>
                                            </td>
                                            <td>Android</td>
                                            <td>15 March 2020</td>
                                            <td>
                                                <span class="badge badge-success-gradient">Ongoing</span>
                                            </td>
                                            <td>15 March 2020</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="project-contain">
                                                    <h6 class="mb-1 tx-13">
                                                        Mobile
                                                        Application
                                                    </h6>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="image-grouped">
                                                    <img class="profile-img brround" alt="profile image"
                                                        src="img/15.jpg" />
                                                </div>
                                            </td>
                                            <td>Android</td>
                                            <td>15 March 2020</td>
                                            <td>
                                                <span class="badge badge-pink-gradient">Ongoing</span>
                                            </td>
                                            <td>15 March 2020</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /row -->
            <!-- row -->
            <div class="row row-sm">
                <div class="col-lg-12 col-xl-4 col-sm-12" hidden>
                    <div class="card">
                        <div class="card-header pb-0 pt-4">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mg-b-10">
                                    Top Ongoing Projects
                                </h4>
                                <i class="mdi mdi-dots-horizontal text-gray">
                                </i>
                            </div>
                            <p class="tx-12 text-muted mb-0">
                                Project Description is a formally
                                written declaration of the project
                                and its idea and context .
                                <a href="">Learn more</a>
                            </p>
                        </div>
                        <div class="card-body p-0 m-scroll mh-350 mt-2">
                            <div class="list-group projects-list">
                                <a href="#"
                                    class="list-group-item list-group-item-action flex-column align-items-start border-top-0">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1 font-weight-semibold">
                                            PSD Projects
                                        </h6>
                                        <small class="text-danger">
                                            <i class="fa fa-caret-down mr-1">
                                            </i>5 days ago</small>
                                    </div>
                                    <p class="mb-0 text-muted mb-0 tx-12">
                                        Started:17-02-2020
                                    </p>
                                    <small class="text-muted">Lorem ipsum dolor sit amet,
                                        consectetuer adipiscing
                                        elit...</small>
                                </a>
                                <a href="#"
                                    class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1 font-weight-semibold">
                                            Wordpress Projects
                                        </h6>
                                        <small class="text-success">
                                            <i class="fa fa-caret-up mr-1">
                                            </i>2 days ago</small>
                                    </div>
                                    <p class="mb-0 text-muted mb-0 tx-12">
                                        Started:15-02-2020
                                    </p>
                                    <small class="text-muted">Lorem ipsum dolor sit amet,
                                        consectetuer adipiscing
                                        elit..</small>
                                </a>
                                <a href="#"
                                    class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1 font-weight-semibold">
                                            HTML &amp; CSS3 Projects
                                        </h6>
                                        <small class="text-danger">
                                            <i class="fa fa-caret-down mr-1">
                                            </i>1 days ago</small>
                                    </div>
                                    <p class="mb-0 text-muted mb-0 tx-12">
                                        Started:26-02-2020
                                    </p>
                                    <small class="text-muted">Lorem ipsum dolor sit amet,
                                        consectetuer adipiscing
                                        elit..</small>
                                </a>
                                <a href="#"
                                    class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1 font-weight-semibold">
                                            HTML &amp; CSS3 Projects
                                        </h6>
                                        <small class="text-danger">
                                            <i class="fa fa-caret-down mr-1">
                                            </i>1 days ago</small>
                                    </div>
                                    <p class="mb-0 text-muted mb-0 tx-12">
                                        Started:26-02-2020
                                    </p>
                                    <small class="text-muted">Lorem ipsum dolor sit amet,
                                        consectetuer adipiscing
                                        elit..</small>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-12" hidden>
                    <div class="card overflow-hidden">
                        <div class="card-header pb-0 pt-4">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mg-b-10">
                                    Activity
                                </h4>
                                <i class="mdi mdi-dots-horizontal text-gray">
                                </i>
                            </div>
                            <p class="tx-12 text-muted mb-0">
                                An activity is a scheduled phase in
                                a project plan with a distinct
                                beginning and end.
                                <a href="">Learn more</a>
                            </p>
                        </div>
                        <div class="card-body p-0">
                            <div class="activity Activity-scroll ps ps--active-y">
                                <div class="activity-list">
                                    <img src="img/6.jpg" alt="" class="img-activity" />
                                    <div class="time-activity">
                                        <div class="item-activity">
                                            <p class="mb-0">
                                                <span class="h6 mr-1">Adam
                                                    Berry</span>
                                                <span class="text-muted tx-13">
                                                    Add a new
                                                    projects</span>
                                                <span class="h6 ml-1 added-project">
                                                    AngularJS
                                                    Template</span>
                                            </p>
                                            <small class="text-muted">30 mins ago</small>
                                        </div>
                                    </div>
                                    <img src="img/9.jpg" alt="" class="img-activity" />
                                    <div class="time-activity">
                                        <div class="item-activity">
                                            <p class="mb-0">
                                                <span class="h6 mr-1">Irene
                                                    Hunter</span>
                                                <span class="text-muted tx-13">
                                                    Add a new
                                                    projects</span>
                                                <span class="h6 ml-1 added-project text-danger">Free HTML
                                                    Template</span>
                                            </p>
                                            <small class="text-muted">1 days ago</small>
                                        </div>
                                    </div>
                                    <img src="img/3.jpg" alt="" class="img-activity" />
                                    <div class="time-activity">
                                        <div class="item-activity">
                                            <p class="mb-0">
                                                <span class="h6 mr-1">John
                                                    Payne</span>
                                                <span class="text-muted tx-13">
                                                    Add a new
                                                    projects</span>
                                                <span class="h6 ml-1 added-project text-success">Free PSD
                                                    Template</span>
                                            </p>
                                            <small class="text-muted">3 days ago</small>
                                        </div>
                                    </div>
                                    <img src="img/4.jpg" alt="" class="img-activity" />
                                    <div class="time-activity">
                                        <div class="item-activity">
                                            <p class="mb-0">
                                                <span class="h6 mr-1">Julia
                                                    Hardacre</span>
                                                <span class="text-muted tx-13">
                                                    Add a new
                                                    projects</span>
                                                <span class="h6 ml-1 added-project text-warning">Free UI
                                                    Template</span>
                                            </p>
                                            <small class="text-muted">5 days ago</small>
                                        </div>
                                    </div>
                                    <img src="img/5.jpg" alt="" class="img-activity" />
                                    <div class="time-activity">
                                        <div class="item-activity">
                                            <p class="mb-0">
                                                <span class="h6 mr-1">Adam
                                                    Berry</span>
                                                <span class="text-muted tx-13">
                                                    Add a new
                                                    projects</span>
                                                <span class="h6 ml-1 added-project text-pink">
                                                    AngularJS
                                                    Template</span>
                                            </p>
                                            <small class="text-muted">30 mins ago</small>
                                        </div>
                                    </div>
                                    <img src="img/6.jpg" alt="" class="img-activity" />
                                    <div class="time-activity">
                                        <div class="item-activity">
                                            <p class="mb-0">
                                                <span class="h6 mr-1">Irene
                                                    Hunter</span>
                                                <span class="text-muted tx-13">
                                                    Add a new
                                                    projects</span>
                                                <span class="h6 ml-1 added-project text-purple">Free HTML
                                                    Template</span>
                                            </p>
                                            <small class="text-muted">1 days ago</small>
                                        </div>
                                    </div>
                                    <img src="img/16.jpg" alt="" class="img-activity" />
                                    <div class="time-activity">
                                        <div class="item-activity">
                                            <p class="mb-0">
                                                <span class="h6 mr-1">John
                                                    Payne</span>
                                                <span class="text-muted tx-13">
                                                    Add a new
                                                    projects</span>
                                                <span class="h6 ml-1 added-project text-success">Free PSD
                                                    Template</span>
                                            </p>
                                            <small class="text-muted">3 days ago</small>
                                        </div>
                                    </div>
                                    <img src="img/10.jpg" alt="" class="img-activity" />
                                    <div class="time-activity mb-0">
                                        <div class="item-activity mb-0">
                                            <p class="mb-0">
                                                <span class="h6 mr-1">Julia
                                                    Hardacre</span>
                                                <span class="text-muted tx-13">
                                                    Add a new
                                                    projects</span>
                                                <span class="h6 ml-1 added-project">Free UI
                                                    Template</span>
                                            </p>
                                            <small class="text-muted">5 days ago</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="ps__rail-x" style="left: 0px; top: 172px">
                                    <div class="ps__thumb-x" tabindex="0"
                                        style="
                                                        left: 0px;
                                                        width: 0px;
                                                    ">
                                    </div>
                                </div>
                                <div class="ps__rail-y"
                                    style="
                                                    top: 172px;
                                                    height: 344px;
                                                    right: 0px;
                                                ">
                                    <div class="ps__thumb-y" tabindex="0"
                                        style="
                                                        top: 115px;
                                                        height: 229px;
                                                    ">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-lg-6 col-xl-4" hidden>
                    <div class="card">
                        <div class="card-header pt-4 pb-0">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mg-b-10">
                                    Task Statistics
                                </h4>
                                <i class="mdi mdi-dots-horizontal text-gray">
                                </i>
                            </div>
                            <p class="tx-12 text-muted mb-0">
                                The Statistics You can also
                                summarize your data in a graphical
                                display, such as histograms
                                <a href="">Learn more</a>
                            </p>
                        </div>
                        <div class="pl-4 pr-4 pt-4 pb-3">
                            <div class="">
                                <div class="row">
                                    <div class="col-md-6 col-6 text-center">
                                        <div class="task-box primary mb-0">
                                            <p class="mb-0 tx-12">
                                                Total Tasks
                                            </p>
                                            <h3 class="mb-0">
                                                385
                                            </h3>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-6 text-center">
                                        <div class="task-box danger mb-0">
                                            <p class="mb-0 tx-12">
                                                Overdue Tasks
                                            </p>
                                            <h3 class="mb-0">19</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="task-stat pb-0">
                            <div class="d-flex tasks">
                                <div class="mb-0">
                                    <div class="h6 fs-15 mb-0">
                                        <i class="far fa-dot-circle text-primary mr-2">
                                        </i>Completed Tasks
                                    </div>
                                    <span class="text-muted tx-11 ml-4">8:00am - 10:30am</span>
                                </div>
                                <span class="float-right ml-auto">135</span>
                            </div>
                            <div class="d-flex tasks">
                                <div class="mb-0">
                                    <div class="h6 fs-15 mb-0">
                                        <i class="far fa-dot-circle text-pink mr-2">
                                        </i>Inprogress Tasks
                                    </div>
                                    <span class="text-muted tx-11 ml-4">8:00am - 10:30am</span>
                                </div>
                                <span class="float-right ml-auto">75</span>
                            </div>
                            <div class="d-flex tasks">
                                <div class="mb-0">
                                    <div class="h6 fs-15 mb-0">
                                        <i class="far fa-dot-circle text-success mr-2">
                                        </i>On Hold Tasks
                                    </div>
                                    <span class="text-muted tx-11 ml-4">8:00am - 10:30am</span>
                                </div>
                                <span class="float-right ml-auto">23</span>
                            </div>
                            <div class="d-flex tasks mb-0 border-bottom-0">
                                <div class="mb-0">
                                    <div class="h6 fs-15 mb-0">
                                        <i class="far fa-dot-circle text-purple mr-2">
                                        </i>Pending Tasks
                                    </div>
                                    <span class="text-muted tx-11 ml-4">8:00am - 10:30am</span>
                                </div>
                                <span class="float-right ml-auto">1</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
        <!-- /row -->
    </div>
    <!-- /container -->
@endsection
