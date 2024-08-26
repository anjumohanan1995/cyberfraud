@extends('layouts.app')
@section('content')

<style>
    .cke_notification_warning {
        background: #c83939;
        border: 1px solid #902b2b;
        display: none !important;
    }
    .approve img{
        height:550px !important;
        width:550px !important;
        }
</style>

<div class="container-fluid">
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2">Notice Management !</h4>
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
                                    {!! $notice->content !!}
                                </textarea>
                            </div>
                            <div class="footer">
                                <button type="submit" class="btn btn-success">Update</button>
                                 @if($role=='Super Admin')
                                    @if (!$notice->approve_id)
                                        <a href="#" id="approve-button" class="btn btn-info w-auto me-2" onclick="approveContent(event)">Approve</a>
                                    @else
                                        <a href="#" class="btn btn-success w-auto me-2" disabled>Approved</a>
                                    @endif
                                @endif
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
    // Initialize CKEditor
    CKEDITOR.replace('content', {
        height: 600,
        allowedContent: true // Allows all HTML content to be included
    });

    function approveContent(event) {
        event.preventDefault(); // Prevent the default anchor action

        // Fetch the user's signature URL
        const signatureUrl = "{{ asset($user_sign[0]->sign) }}";
        const signatureHtml = `
    <div style="text-align: right;" class="approve">
        <p><strong>Approved by:</strong></p>
        <img src="${signatureUrl}" alt="Signature" style="max-width: 250px; height: auto;">
        <p><strong>{{$user_sign[0]->sign_name}}</strong></p>
        <p><strong>{{$user_sign[0]->sign_designation}}</strong></p>
    </div>
    `.replace(/\s+/g, ' ').trim();

        // Get CKEditor instance
        const editor = CKEDITOR.instances.content;

        // Get current content
        const currentContent = editor.getData();

        // Append the signature to the end of the content
        const updatedContent = currentContent + signatureHtml;

        // Set the updated content in the editor
        editor.setData(updatedContent);

        // Disable the button and change text immediately
        const approveButton = document.getElementById('approve-button');
        if (approveButton) {
            approveButton.textContent = 'Approved';
            approveButton.classList.remove('btn-info');
            approveButton.classList.add('btn-success');
            approveButton.disabled = true;
            approveButton.style.pointerEvents = 'none'; // Ensure the button does not respond to clicks
        }

        // Send the updated content to the server via an AJAX request
        fetch(`/notices/{{ $notice->id }}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ content: updatedContent })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Optional: Further UI updates if necessary
            } else {
                console.error('Failed to approve content.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
</script>

@endsection
