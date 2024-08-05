@extends('layouts.app')
@section('content')

<style>
    .cke_notification_warning {
        background: #c83939;
        border: 1px solid #902b2b;
        display: none !important;
    }
    </style>

<div class="container-fluid">
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2">Hi, welcome back!</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Notice Management</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Notice Update</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- /breadcrumb -->

    <!-- main-content-body -->
    <div class="row row-sm">
        <div class="col-md-12 col-xl-12">
            <div class="card overflow-hidden review-project">
                <div class="card-body">
                    <div class="m-4 d-flex justify-content-between">
                        <div id="alert_ajaxx" style="display:none"></div>
                        @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show w-100" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif
                        <div class="alert alert-success-one alert-dismissible fade show w-100" role="alert" style="display:none">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
<div class="container-fluid">
    <div class="notice-header">
        <h4>Edit Notice</h4>
    </div>

    <form action="{{ route('notices.update', $notice->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="section">
            <label for="content">Notice Content:</label>
            <textarea id="content" name="content" class="ckeditor form-control">

                {{ $notice->content }}
            </textarea>
        </div>

        <div class="footer">
            <button type="submit" class="btn btn-success">Update</button>
            <a href="{{ route('notices.show', $notice->id) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

</div>
</div>
</div>
</div>
<!-- /row -->
</div>

<script src="https://cdn.ckeditor.com/4.16.1/standard/ckeditor.js"></script>
<script>
    // Replace the textarea with CKEditor
    CKEDITOR.replace('content', {
        height: 600
    });
</script>


@endsection
