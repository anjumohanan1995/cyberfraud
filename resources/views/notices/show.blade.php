@extends('layouts.app')

@section('content')

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        line-height: 1.6;
    }
    .container-fluid {
        padding: 20px;
    }
    .notice-header {
        text-align: center;
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 20px;
    }
    .section {
        margin-bottom: 20px;
    }
    .section-title {
        font-weight: bold;
        margin-bottom: 10px;
        font-size: 1.25rem;
    }
    .details-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    .details-table td {
        padding: 8px;
        border: 1px solid #ddd;
    }
    .details-table th {
        padding: 8px;
        border: 1px solid #ddd;
        text-align: left;
        background-color: #f8f9fa;
    }
    .footer {
        margin-top: 20px;
        text-align: center;
    }
    .footer p {
        margin: 5px 0;
    }
    .btn {
        display: inline-block;
        padding: 10px 20px;
        font-size: 16px;
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #fff;
        background-color: #007bff;
        border-radius: 5px;
        border: none;
        cursor: pointer;
    }
    .btn-secondary {
        background-color: #6c757d;
    }
    .btn-secondary:hover {
        background-color: #5a6268;
    }
    .btn-success {
        background-color: #28a745;
    }
    .btn-success:hover {
        background-color: #218838;
    }
    .signature {
        margin-top: 20px;
        text-align: center;
    }
    .signature img {
        max-width: 100%;
        height: auto;
    }
    .card-body {
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    min-height: 1px;
    padding: 1.25rem;
    height: 1000;
}
</style>

<style>
    .custom-dropdown {
        position: relative; /* Make sure dropdown container is relative */
        display: inline-block;
    }

    .custom-dropdown-content {
        display: none;
        position: absolute; /* Absolute positioning to place dropdown relative to the parent */
        background-color: #f9f9f9;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    }

    .custom-dropdown-content a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }

    .custom-dropdown-content a:hover {
        background-color: #f1f1f1;
    }

    .custom-dropdown:hover .custom-dropdown-content {
        display: block; /* Show dropdown on hover */
    }

    .custom-dropdown:hover .custom-dropdown-button {
        background-color: #3e8e41;
    }
</style>

<!-- Local Bootstrap CSS -->
<link href="/path/to/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<!-- jsPDF Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<!-- html2canvas Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.5.0-beta4/html2canvas.min.js"></script>


<div class="container-fluid">
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2">Hi, welcome back!</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Notice Management</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Notice view</li>
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

                    <div class="notice-content" id="notice-content">
                        {!! htmlspecialchars_decode($notice->content) !!}
                    </div>

                    <a href="{{ route('notices.index') }}" class="btn btn-secondary">Back to List</a>
                    <a href="{{ route('notices.edit', $notice->id) }}" class="btn btn-success">Update</a>
                    <a href="#" class="btn btn-primary" onclick="downloadContent()">Download Content</a>

                    <div class="custom-dropdown">
                        <button class="custom-dropdown-button btn btn-success">Follow</button>
                        <div class="custom-dropdown-content">
                            @foreach($users as $user)
                                <a href="#" data-user-id="{{ $user->id }}">{{ $user->role }}</a>
                            @endforeach
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
    <!-- /row -->
</div>


<!-- Local Bootstrap JS with Popper.js -->
<script src="/path/to/bootstrap.bundle.min.js"></script>
<!-- Bootstrap Bundle with Popper.js -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
    document.querySelectorAll('.custom-dropdown-content a').forEach(item => {
        item.addEventListener('click', event => {
            event.preventDefault();

            const userId = event.target.getAttribute('data-user-id');

            fetch(`/notices/{{ $notice->id }}/follow`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ user_id: userId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Followed user successfully!');
                } else {
                    alert('Failed to follow user.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });

</script>

<script>
    function downloadContent() {
    const { jsPDF } = window.jspdf;

    // Capture the HTML content with html2canvas
    html2canvas(document.getElementById('notice-content')).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const pdf = new jsPDF('p', 'mm', 'a4');
        const imgWidth = 210; // A4 width in mm
        const pageHeight = 295; // A4 height in mm
        const imgHeight = canvas.height * imgWidth / canvas.width;
        let heightLeft = imgHeight;

        let position = 0;

        pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;

        // Add another page if needed
        while (heightLeft >= 0) {
            position = heightLeft - imgHeight;
            pdf.addPage();
            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }

        pdf.save('notice-content.pdf');
    });
}

</script>

@endsection
