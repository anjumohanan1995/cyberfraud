@extends('layouts.app')

@section('content')
    <!-- container -->
    <div class="container-fluid">
        <!-- breadcrumb -->
        <div class="breadcrumb-header justify-content-between">
            <div>
                <h4 class="content-title mb-2">
                    Mule account !
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <i class="fas fa-university"></i> Mule account
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-university"></i> Mule account
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

                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show w-100" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show w-100" role="alert">
                                        {{ session('error') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <div class=" m-4 d-flex justify-content-between">
                                <h4 class="card-title mg-b-10">
                                    Mule Account List
                                </h4>

                            </div>

                                {{-- <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="source_type">From Date</label>
                                                         <input type="date" class="form-control" id="from_date" name="from_date">
                                                        @error('from_date')
                                                        <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="source_type">To Date</label>
                                                         <input type="date" class="form-control"  id="to_date" name="to_date">
                                                        @error('to_date')
                                                        <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="source_type">Acknowledgement Number</label>
                                                         <input type="text" id="acknowledgement_number" class="form-control" name="acknowledgement_number">
                                                        @error('to_date')
                                                        <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                           <div class="row">
                                            <div class="col-md-3">
                                            <label for="source_type">Source Type</label>
                                                <select class="form-control" name="source_type" id="source_type">
                                                <option value="">--Select--</option>
                                                @foreach ($source_types as $st)
                                                 <option value="{{ $st->_id }}"> {{ $st->name }} </option>
                                                @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                            <label for="source_type">Evidence Type</label>
                                                <select class="form-control" name="evidence_type" id="evidence_type">
                                                <option value="">--Select--</option>
                                                @foreach ($evidence_types as $et)
                                                 <option value="{{ $et->_id }}"> {{ $et->name }} </option>
                                                @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                            <label for="source_type">Status</label>
                                                <select name="status" id="" class="form-control">
                                                <option value="1"> Active </option>
                                                <option value="0"> InActive </option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                            <label for="source_type">Notice Type</label>
                                                <select name="notice_type" id="" class="form-control">
                                                <option value="Type1"> Type1 </option>
                                                <option value="Type2"> Type2 </option>
                                                </select>
                                            </div>
                                           </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">

                                        <div class="col-md-2">
                                            <button id="notice_submit" type="button" class="btn btn-primary">Submit</button>
                                        </div>

                                    </div>
                                </form> --}}
                        <div class="table-responsive mb-0">
                        <table id="notice" class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap table-striped">
                        <thead>
                            <tr>
                                <th>SL No</th>
                                <th>Account Number</th>
                                <th>Bank Name</th>
                                <th>Notice Status</th>
                                {{-- <th>Domain</th> --}}
                                {{-- <th>IP</th> --}}
                                {{-- <th>Action</th> --}}
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        </table>
                        </div>
                        </div>

                    </div>

            </div>
        </div>
            <!-- /row -->




        </div>
        <!-- /row -->
    </div>
    <script>
        $(document).ready(function() {
            function display_notice_list() {
                if ($.fn.DataTable.isDataTable('#notice')) {
                    $('#notice').DataTable().destroy();
                }
                var tableNew = $('#notice').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('get_muleaccount_list') }}",
                        data: function(d) {
                            return $.extend({}, d, {});
                        }
                    },
                    columns: [
                        { data: 'id' },
                        { data: 'account_no_2' },
                        { data: 'bank' },
                        { data: 'status' },
                    ],
                    order: [0, 'asc'], // Sorting by SL No in ascending order
                    ordering: true
                });
            }

            display_notice_list();
        });
    </script>

{{-- <script>
    $(document).ready(function() {
        function display_notice_list(){

        if ($.fn.DataTable.isDataTable('#notice')){
          $('#notice').DataTable().destroy();
        }
        var tableNew = $('#notice').DataTable({
        processing: true,
        serverSide: true,

        ajax:{
        url: "{{ route('get_muleaccount_list') }}",
        data: function(d) {
        return $.extend({}, d, {
         });
        }
        },
        columns: [
        { data: 'id' },
        { data: 'account_no_2' },
        // { data: 'edit' }
        ],
        order: [0, 'desc'],
        ordering: true
        });

   }

   display_notice_list();
    });
</script> --}}


@endsection
