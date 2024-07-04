<!-- resources/views/mailmerge/preview.blade.php -->

@extends('layouts.app')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show w-100" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

    <div class="container" style="background-color: #ffffff; padding: 20px;">
        <h2>Preview</h2>

        {{-- Display your preview data --}}
        <form action="{{ route('send-email') }}" method="POST">
            @csrf

            <div class="mb-3">
                <p><strong>Sub:</strong><span style="color: blue;">{{ $sub }}</span></p>
            </div>

            <div class="mb-3">
                <p><strong>Salutation:</strong><span style="color: green;">Team Register name</span></p>
            </div>

            <div class="mb-3">
                <p><strong>Content:</strong></p>
                <p>
                    A complaint in NO: <span style="font-weight: bold;">{{ $number }}</span> is reported at National Cyber Crime Reporting Portal (NCRP) for financial fraud in which an Unlawful Website with the URL
                    <a href="{{ $url }}" target="_blank" style="color: red;">{{ $url }}</a> is involved and it is found that the website is hosted in your registry for propagating cyber fraud. Hence it is directed to provide the details of the below mentioned website by return.
                </p>
            </div>

            <div class="mb-3">
                <p><strong>Alleged Website Details:</strong></p>
                <p>
                    <a href="{{ $url }}" target="_blank" style="color: red;">{{ $url }}</a><br>
                    Domain Name: <span style="color: purple;">{{ $domain_name }}</span><br>
                    Registry Domain ID: <span style="color: orange;">{{ $domain_id }}</span>
                </p>
            </div>

            <div class="mb-3">
                <p><strong>Details Required:</strong></p>
                <ol>
                    <li>Registration details including:
                        <ul>
                            <li>Email ID</li>
                            <li>Mobile phone numbers</li>
                            <li>IP address with Date and Time</li>
                            <li>Mode of payment details for registration</li>
                        </ul>
                    </li>
                    <li>Any other Sub domains with the above registration email id or mobile number.</li>
                    <li>Registration Details as mentioned in (1) for domain identified under (2)</li>
                </ol>
            </div>

            <p>Urgent action and confirmation is solicited by return.</p>

            <input type="hidden" name="sub" value="{{ $sub }}">
            <input type="hidden" name="number" value="{{ $number }}">
            <input type="hidden" name="url" value="{{ $url }}">
            <input type="hidden" name="domain_name" value="{{ $domain_name }}">
            <input type="hidden" name="domain_id" value="{{ $domain_id }}">
            <input type="hidden" name="registrar" value="{{ $registrar }}">
            <input type="hidden" name="evidence_type" value="{{ $evidence_type }}">
            <input type="hidden" name="case_no" value="{{ $case_no }}">
            <input type="hidden" name="acknowledgement_no" value="{{ $acknowledgement_no }}">
            <input type="hidden" name="evidence_type_id" value="{{ $evidence_type_id }}">

            {{-- Submit button --}}
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Send Email</button>
            </div>
        </form>
    </div>
@endsection
