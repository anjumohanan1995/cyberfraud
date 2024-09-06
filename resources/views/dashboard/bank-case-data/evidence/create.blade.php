@extends('layouts.app')
@php
    use Illuminate\Support\Facades\Crypt as CryptFacade;
    $id = request()->segment(count(request()->segments()));
    $new_id = CryptFacade::decrypt($id);
@endphp


{{-- @dd($id); --}}
@section('content')
    <!-- container -->
    <style>
    .ev-type{
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }

    .remove-btn {
        margin-left: 10px;
        cursor: pointer;
    }

    .left-margin {
    margin-left: 100px; /* Adjust the value as needed */
}
</style>
<!-- Include Selectize CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/css/selectize.default.min.css">

<!-- Include jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<!-- Include Selectize JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/js/standalone/selectize.min.js"></script>



    @php
    use Illuminate\Support\Facades\Crypt;
    $id = request()->segment(count(request()->segments()));
    $new_id = Crypt::decrypt($id);
@endphp

@php
$excludedTypes = ['website', 'mobile', 'whatsapp'];
$filteredEvidenceTypes = array_filter($evidenceTypes->pluck('name')->toArray(), function($type) use ($excludedTypes) {
    return !in_array($type, $excludedTypes);
});
@endphp

    <div class="container-fluid">
        <!-- breadcrumb -->
        <div class="breadcrumb-header justify-content-between">
            <div>
                <h4 class="content-title mb-2">
                   NCRP Case Data !
                </h4>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('case-data.view', ['id' => @$id ]) }}">Case Data</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Add Evidence
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
                                <h4 class="card-title mg-b-10">
                                    Add Evidence!
                                </h4>
                                <div class="task-box primary col-1">
                                    <a class="text-white" data-toggle="tooltip" data-placement="top"
                                        title="Back" href="{{ route('case-data.view', ['id' => @$id ]) }}">
                                        <h3 class="mb-0"><i class="ti ti-arrow-left"></i></h3>
                                    </a>
                                </div>
                            </div>

                            @if (session('success'))
                            <div class="alert alert-success col-6">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                        <div class="alert alert-danger">
                            {!! session('error') !!}
                        </div>
                    @endif

                            <div class="table-responsive mb-0">
                                <form id="evidenceForm" action="{{ route('evidence.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="acknowledgement_number" value="{{ $new_id }}">

                                    <div id="evidence_fields">
                                        @foreach (old('evidence_type', ['']) as $index => $oldEvidenceType)
                                            <div class="evidence-fields">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                <div class="form-group ev-type">
                                                    <label for="evidence_type_{{ $index }}">Evidence type:</label>
                                                    <select class="form-control evidence_type" name="evidence_type[]" required>
                                                        <option value="">Select Option</option>
                                                        @foreach($evidenceTypes as $evidenceType)
                                                            <option value="{{ $evidenceType->name }}" {{ $oldEvidenceType == $evidenceType->name ? 'selected' : '' }} data-id="{{ $evidenceType->id }}">
                                                                {{ $evidenceType->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="evidence_type_id[]" class="evidence_type_id" value="">
                                                    @error('evidence_type.' . $index)
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                            </div>
                                                </div>

                                                <!-- Dynamic fields will be inserted here -->
                                                <div class="dynamicFields">
                                                    @if ($oldEvidenceType == 'website')
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="url_{{ $index }}">URL:</label>
                                                                    <input type="text" name="url[]" class="form-control" value="{{ old('url.' . $index) }}" placeholder="Enter URL" oninput="extractDomain(this)" required>
                                                                    @if ($errors->has('url.' . $index))
                                                                        <span class="text-danger">{{ $errors->first('url.' . $index) }}</span>
                                                                    @endif

                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="domain_{{ $index }}">Domain:</label>
                                                                    <input type="text" name="domain[]" class="form-control" value="{{ old('domain.' . $index) }}" placeholder="Enter Domain" required>
                                                                    @error('domain.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="registry_details_{{ $index }}">Registry Details:</label>
                                                                    <input type="text" name="registry_details[]" class="form-control" value="{{ old('registry_details.' . $index) }}" placeholder="Enter Registry Details" required>
                                                                    @error('registry_details.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="ip_{{ $index }}">IP:</label>
                                                                    <input type="text" name="ip[]" class="form-control" value="{{ old('ip.' . $index) }}" placeholder="Enter IP" required>
                                                                    @error('ip.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="registrar_{{ $index }}">Registrar:</label>
                                                                    <input type="text" name="registrar[]" class="form-control" value="{{ old('registrar.' . $index) }}" placeholder="Enter Registrar" required>
                                                                    @error('registrar.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="pdf_{{ $index }}">Document: <span style="color: red;">(PDF files are allowed-less than 2MB in size.)</span></label>
                                                                    <input type="file" name="pdf[]" class="form-control" multiple required>
                                                                    @error('pdf.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="screenshots_{{ $index }}">Screenshots: <span style="color: red;">(JPEG, BMP, or PNG files are allowed-less than 2MB in size.)</span></label>
                                                                    <input type="file" name="screenshots[]" class="form-control" multiple required>
                                                                    @error('screenshots.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="remarks_{{ $index }}">Remarks:</label>
                                                                    <textarea name="remarks[]" cols="30" rows="5" class="form-control" required>{{ old('remarks.' . $index) }}</textarea>
                                                                    @error('remarks.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="category_{{ $index }}">Category:</label>
                                                                    <select class="form-control category" name="category[]" required>
                                                                        <option value="">Select Option</option>
                                                                        <option value="phishing" {{ $oldEvidenceType == 'phishing' ? 'selected' : '' }}>Phishing</option>
                                                                        <option value="malware" {{ $oldEvidenceType == 'malware' ? 'selected' : '' }}>Malware</option>
                                                                        <option value="fraud" {{ $oldEvidenceType == 'fraud' ? 'selected' : '' }}>Fraud</option>
                                                                        <option value="other" {{ $oldEvidenceType == 'other' ? 'selected' : '' }}>Other</option>
                                                                    </select>
                                                                    @error('category.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="ticket_{{ $index }}">Content Removal:</label>
                                                                    <input type="text" name="ticket[]" class="form-control" value="{{ old('ticket.' . $index) }}" placeholder="Enter Content Removal" required>
                                                                    @error('ticket.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="ticket_{{ $index }}">Data disclosure:</label>
                                                                    <input type="text" name="data_disclosure[]" class="form-control" value="{{ old('data_disclosure.' . $index) }}" placeholder="Enter Data disclosure" required>
                                                                    @error('data_disclosure.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="ticket_{{ $index }}">Preservation:</label>
                                                                    <input type="text" name="preservation[]" class="form-control" value="{{ old('preservation.' . $index) }}" placeholder="Enter preservation" required>
                                                                    @error('preservation.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>

                                                            <input type="hidden" name="country_code[]" value="{{ old('country_code.' . $index) }}">
                                                            <input type="hidden" name="mobile[]" value="{{ old('mobile.' . $index) }}">

                                                        </div>
                                                        @elseif ($oldEvidenceType == 'mobile' || $oldEvidenceType == 'whatsapp')
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="country_code">Country:</label>
                                                                    <select name="country_code[]" id="country_code" class="form-control" required>
                                                                        <option value="">Select Country</option>
                                                                        @foreach($countries as $country)
                                                                            <option value="{{ $country->international_dialing }}"
                                                                                {{ in_array($country->international_dialing, old('country_code', [])) ? 'selected' : '' }}>
                                                                                {{ $country->country }} ({{ $country->international_dialing }})
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('country_code.*')
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="mobile">Mobile:</label>
                                                                    <input type="tel" name="mobile[]" id="mobileInput" class="form-control" placeholder="Enter Mobile"
                                                                        value="{{ old('mobile.0') }}" required>
                                                                    @error('mobile.0')
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6 hid-field">
                                                                <div class="form-group">
                                                                    <input type="text" name="url[]" class="form-control" value="{{ old('url.0') }}" placeholder="Enter URL" hidden>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="pdf_{{ $index }}">Document: <span style="color: red;">(PDF files are allowed - less than 2MB in size.)</span></label>
                                                                    <input type="file" name="pdf[]" class="form-control" multiple required>
                                                                    @error('pdf.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="screenshots_{{ $index }}">Screenshots: <span style="color: red;">(JPEG, BMP, or PNG files are allowed - less than 2MB in size.)</span></label>
                                                                    <input type="file" name="screenshots[]" class="form-control" multiple required>
                                                                    @error('screenshots.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6 hid-field">
                                                                <div class="form-group">
                                                                    <input type="text" name="domain[]" class="form-control" value="{{ old('domain.0') }}" placeholder="Enter Domain" hidden>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6 hid-field">
                                                                <div class="form-group">
                                                                    <input type="text" name="registry_details[]" class="form-control" value="{{ old('registry_details.0') }}" placeholder="Enter Registry Details" hidden>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6 hid-field">
                                                                <div class="form-group">
                                                                    <input type="text" name="ip[]" class="form-control" value="{{ old('ip.0') }}" placeholder="Enter IP" hidden>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6 hid-field">
                                                                <div class="form-group">
                                                                    <input type="text" name="registrar[]" class="form-control" value="{{ old('registrar.0') }}" placeholder="Enter Registrar" hidden>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="remarks_{{ $index }}">Remarks:</label>
                                                                    <textarea name="remarks[]" cols="30" rows="5" class="form-control" required>{{ old('remarks.' . $index) }}</textarea>
                                                                    @error('remarks.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="category_{{ $index }}">Category:</label>
                                                                    <select class="form-control category" name="category[]" required>
                                                                        <option value="">Select Option</option>
                                                                        <option value="phishing" {{ old('category.' . $index) == 'phishing' ? 'selected' : '' }}>Phishing</option>
                                                                        <option value="malware" {{ old('category.' . $index) == 'malware' ? 'selected' : '' }}>Malware</option>
                                                                        <option value="fraud" {{ old('category.' . $index) == 'fraud' ? 'selected' : '' }}>Fraud</option>
                                                                        <option value="other" {{ old('category.' . $index) == 'other' ? 'selected' : '' }}>Other</option>
                                                                    </select>
                                                                    @error('category.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="ticket_{{ $index }}">Content Removal Ticket:</label>
                                                                    <input type="text" name="ticket[]" class="form-control" value="{{ old('ticket.' . $index) }}" placeholder="Enter Content Removal Ticket" required>
                                                                    @error('ticket.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="data_disclosure_{{ $index }}">Data Disclosure Ticket:</label>
                                                                    <input type="text" name="data_disclosure[]" class="form-control" value="{{ old('data_disclosure.' . $index) }}" placeholder="Enter Data Disclosure Ticket" required>
                                                                    @error('data_disclosure.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="preservation_{{ $index }}">Preservation Ticket:</label>
                                                                    <input type="text" name="preservation[]" class="form-control" value="{{ old('preservation.' . $index) }}" placeholder="Enter Preservation Ticket" required>
                                                                    @error('preservation.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>

                                                        </div>
                                                    @elseif (in_array($oldEvidenceType, $filteredEvidenceTypes))
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="url_{{ $index }}">URL:</label>
                                                                    <input type="text" name="url[]" class="form-control" value="{{ old('url.' . $index) }}" placeholder="Enter URL" required>
                                                                    @error('url.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror

                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="url_{{ $index }}">Post/Account/profile/group/e-mail.</label>
                                                                    <input type="text" name="domain[]" class="form-control" value="{{ old('domain.' . $index) }}" placeholder="Enter Domain" required>
                                                                    @error('domain.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror

                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="pdf_{{ $index }}">Document: <span style="color: red;">(PDF files are allowed-less than 2MB in size.)</span></label>
                                                                    <input type="file" name="pdf[]" class="form-control" multiple required>
                                                                    @error('pdf.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="screenshots_{{ $index }}">Screenshots: <span style="color: red;">(JPEG, BMP, or PNG files are allowed-less than 2MB in size.)</span></label>
                                                                    <input type="file" name="screenshots[]" class="form-control" multiple required>
                                                                    @error('screenshots.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="remarks_{{ $index }}">Remarks:</label>
                                                                    <textarea name="remarks[]" cols="30" rows="5" class="form-control" required>{{ old('remarks.' . $index) }}</textarea>
                                                                    @error('remarks.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="category_{{ $index }}">Category:</label>
                                                                    <select class="form-control category" name="category[]" required>
                                                                        <option value="">Select Option</option>
                                                                        <option value="phishing" {{ $oldEvidenceType == 'phishing' ? 'selected' : '' }}>Phishing</option>
                                                                        <option value="malware" {{ $oldEvidenceType == 'malware' ? 'selected' : '' }}>Malware</option>
                                                                        <option value="fraud" {{ $oldEvidenceType == 'fraud' ? 'selected' : '' }}>Fraud</option>
                                                                        <option value="other" {{ $oldEvidenceType == 'other' ? 'selected' : '' }}>Other</option>
                                                                    </select>
                                                                    @error('category.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="ticket_{{ $index }}">Content Removal:</label>
                                                                    <input type="text" name="ticket[]" class="form-control" value="{{ old('ticket.' . $index) }}" placeholder="Enter Content Removal" required>
                                                                    @error('ticket.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="ticket_{{ $index }}">Data disclosure:</label>
                                                                    <input type="text" name="data_disclosure[]" class="form-control" value="{{ old('data_disclosure.' . $index) }}" placeholder="Enter Data disclosure" required>
                                                                    @error('data_disclosure.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="ticket_{{ $index }}">Preservation:</label>
                                                                    <input type="text" name="preservation[]" class="form-control" value="{{ old('preservation.' . $index) }}" placeholder="Enter preservation" required>
                                                                    @error('preservation.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>

                                                            <!-- Hidden Fields for Update -->
                                                            <input type="hidden" name="country_code[]" value="{{ old('country_code.' . $index) }}">
                                                            <input type="hidden" name="mobile[]" value="{{ old('mobile.' . $index) }}">
                                                            <input type="hidden" name="registry_details[]" value="{{ old('registry_details.' . $index) }}">
                                                            <input type="hidden" name="ip[]" value="{{ old('ip.' . $index) }}">
                                                            <input type="hidden" name="registrar[]" value="{{ old('registrar.' . $index) }}">


                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <button type="button" class="btn btn-danger btn-sm remove-btn left-margin" style="display: none;">
                                                        <i class="fas fa-trash-alt"></i> <!-- Font Awesome delete icon -->
                                                    </button><br>
                                                </div><br>
                                            </div>
                                        @endforeach
                                    </div>

                                    <button type="button" id="addMore" class="btn btn-primary">Add More</button>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /row -->
        </div>
        <!-- /row -->
    </div>
    <style>
        .hid-field{
            display: none;
        }
    </style>
<script>
    function extractDomain(input) {
        var url = input.value;
        var domainInput = input.parentNode.parentNode.nextElementSibling.querySelector("input[name='domain[]']");
        var domain = extractDomainFromUrl(url);
        domainInput.value = domain;
    }

    function extractDomainFromUrl(url) {
        var match = url.match(/:\/\/(www[0-9]?\.)?(.[^/:]+)/i);
        if (match != null && match.length > 2 && typeof match[2] === 'string' && match[2].length > 0) {
            return match[2];
        } else {
            return null;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize evidence_type_id based on the selected value of evidence_type
        document.querySelectorAll('.evidence-fields').forEach(function(evidenceField) {
            var select = evidenceField.querySelector('.evidence_type');
            var hiddenInput = evidenceField.querySelector('.evidence_type_id');

            // Set the hidden input value based on the selected option's data-id attribute
            var selectedOption = select.options[select.selectedIndex];
            if (selectedOption) {
                hiddenInput.value = selectedOption.getAttribute('data-id');
            }
        });

        // Add event listener for the "Add More" button
        document.getElementById('addMore').addEventListener('click', function() {
            var evidenceFields = document.getElementById('evidence_fields');
            var evidenceTemplate = document.querySelector('.evidence-fields').cloneNode(true);

            // Clear previous selected values and input fields
            var selects = evidenceTemplate.querySelectorAll('.evidence_type');
            selects.forEach(function(select) {
                select.selectedIndex = 0;
            });

            var inputs = evidenceTemplate.querySelectorAll('input, textarea');
            inputs.forEach(function(input) {
                input.value = '';
            });

            var dynamicFields = evidenceTemplate.querySelector('.dynamicFields');
            dynamicFields.innerHTML = '';

            // Show the remove button for the newly added field
            var removeButton = evidenceTemplate.querySelector('.remove-btn');
            removeButton.style.display = 'inline-block';
            removeButton.addEventListener('click', function() {
                evidenceTemplate.remove();
            });

            evidenceFields.appendChild(evidenceTemplate);
        });

        // Event delegation to handle change events for dynamically added select elements
        document.getElementById('evidence_fields').addEventListener('change', function(event) {
            var target = event.target;
            if (target && target.classList.contains('evidence_type')) {
                var option = target.value;
                var dynamicFields = target.closest('.evidence-fields').querySelector('.dynamicFields');
                var hiddenInput = target.closest('.evidence-fields').querySelector('.evidence_type_id');
                var selectedOption = target.options[target.selectedIndex];

                // Set the hidden input value based on the selected option's data-id attribute
                hiddenInput.value = selectedOption.getAttribute('data-id');

                dynamicFields.innerHTML = ''; // Clear previous fields

                    if (option !== "") {
                        switch (option) {
                            case 'website':
                            var index = dynamicFields.querySelectorAll('.row').length + 1;
                                dynamicFields.innerHTML = `
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="url_${index}">URL:</label>
                                                <input type="text" name="url[]" class="form-control" placeholder="Enter URL" oninput="extractDomain(this)" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="domain">Domain:</label>
                                                <input type="text" name="domain[]" class="form-control" placeholder="Enter Domain" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="registry_details">Registry Details:</label>
                                                <input type="text" name="registry_details[]" class="form-control" placeholder="Enter Registry Details" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="ip">IP:</label>
                                                <input type="text" name="ip[]" class="form-control" placeholder="Enter IP" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="registrar">Registrar:</label>
                                                <input type="text" name="registrar[]" class="form-control" placeholder="Enter Registrar" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="pdf">Document: <span style="color: red;">(PDF files are allowed-less than 2MB in size.)</span></label>
                                                <input type="file" name="pdf[]" class="form-control" multiple required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="screenshots">Screenshots: <span style="color: red;">(JPEG, BMP, or PNG files are allowed-less than 2MB in size.)</span></label>
                                                <input type="file" name="screenshots[]" class="form-control" multiple required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="remarks">Remarks:</label>
                                                <textarea name="remarks[]" cols="30" rows="5" class="form-control" required></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="category">Category:</label>
                                                <select class="form-control category" name="category[]" required>
                                                    <option value="">Select Option</option>
                                                    <option value="phishing">Phishing</option>
                                                    <option value="malware">Malware</option>
                                                    <option value="fraud">Fraud</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="ticket">Content Removal Ticket:</label>
                                                <input type="text" name="ticket[]" class="form-control" placeholder="Enter Content Removal Ticket" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="ticket">Data Disclosure Ticket:</label>
                                                <input type="text" name="data_disclosure[]" class="form-control" placeholder="Enter Data Disclosure Ticket" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="ticket">Preservation Ticket:</label>
                                                <input type="text" name="preservation[]" class="form-control" placeholder="Enter Preservation Ticket" required>
                                            </div>
                                        </div>
                                                                                                             <div class="form-group">
                                        <input type="text" name="mobile[]" class="form-control" value="" placeholder="Enter mobile" hidden>

       </div>
                                                                            <div class="form-group">
                                        <input type="text" name="country_code[]" class="form-control" value="" placeholder="Enter country code" hidden>

       </div>
                                    </div>
                                `;
                                break;
                                case 'mobile':
                                case 'whatsapp':
                                var index = dynamicFields.querySelectorAll('.row').length + 1;
                                dynamicFields.innerHTML = `
                                    <div class="row">
                                          <div class="col-md-6">
                                              <div class="form-group">
                                                  <label for="country_code">Country:</label>
                                                  <select name="country_code[]" id="country_code" class="form-control" required>
                                                      <option value="">Select Country</option>
                                                      @foreach($countries as $country)
                                                          <option value="{{ $country->international_dialing }}">{{ $country->country }} ({{ $country->international_dialing }})</option>
                                                      @endforeach
                                                  </select>
                                              </div>
                                          </div>


                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="mobile">Mobile:</label>
                                                <input type="tel" name="mobile[]" id="mobileInput" class="form-control" placeholder="Enter Mobile" required>

                                            </div>
                                         </div>
                                         <div class="form-group">
                                        <input type="text" name="url[]" class="form-control" value="" placeholder="Enter url" hidden>

                                          </div>


                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="pdf">Document: <span style="color: red;">(PDF files are allowed-less than 2MB in size.)</span></label>
                                                <input type="file" name="pdf[]" class="form-control" multiple required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="screenshots">Screenshots: <span style="color: red;">(JPEG, BMP, or PNG files are allowed-less than 2MB in size.)</span></label>
                                                <input type="file" name="screenshots[]" class="form-control" multiple required>
                                            </div>
                                        </div>
                                        <div class="col-md-6 hid-field">
                                            <div class="form-group">
                                        <input type="text" name="domain[]" class="form-control" value="" placeholder="Enter Domain" hidden>

       </div>
                                        </div>
                                        <div class="col-md-6 hid-field">
                                            <div class="form-group">
                                        <input type="text" name="registry_details[]" class="form-control" value="" placeholder="Enter Registry Details" hidden> </div>
                                        </div>
                                        <div class="col-md-6 hid-field">
                                            <div class="form-group">
                                       <input type="text" name="ip[]" class="form-control" value="" placeholder="Enter IP" hidden> </div>
                                        </div>
                                        <div class="col-md-6 hid-field">
                                            <div class="form-group">
                                        <input type="text" name="registrar[]" class="form-control" value="" placeholder="Enter Registrar" hidden></div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="remarks">Remarks:</label>
                                                <textarea name="remarks[]" cols="30" rows="5" class="form-control" required></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="category">Category:</label>
                                                <select class="form-control category" name="category[]" required>
                                                    <option value="">Select Option</option>
                                                    <option value="phishing">Phishing</option>
                                                    <option value="malware">Malware</option>
                                                    <option value="fraud">Fraud</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="ticket">Content Removal Ticket:</label>
                                                <input type="text" name="ticket[]" class="form-control" placeholder="Enter Content Removal Ticket" required>
                                            </div>
                                        </div>
                                                  <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="ticket">Data Disclosure Ticket:</label>
                                                <input type="text" name="data_disclosure[]" class="form-control" placeholder="Enter Data Disclosure Ticket" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="ticket">Preservation Ticket:</label>
                                                <input type="text" name="preservation[]" class="form-control" placeholder="Enter Preservation Ticket" required>
                                            </div>
                                        </div>

                                    </div>
                                `;
                                break;

                            // case 'instagram':
                            // case 'telegram':
                            // case 'facebook':
                            // case 'linkedin':
                            // case 'skype':
                            // case 'gmail':
                            // case 'youtube':
                            // case 'mobile numbers':
                            // case 'olx ad':
                            // case 'twitter':
                            // case 'other':
                            default:
                            var index = dynamicFields.querySelectorAll('.row').length + 1;
                                dynamicFields.innerHTML = `
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="url_${index}">URL:</label>
                                                <input type="text" name="url[]" class="form-control" placeholder="Enter URL" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="domain">Post/Account/profile/group/e-mail.</label>
                                                 <input type="text" name="domain[]" class="form-control" value="" placeholder="Enter Domain" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="pdf">Document: <span style="color: red;">(PDF files are allowed-less than 2MB in size.)</span></label>
                                                <input type="file" name="pdf[]" class="form-control" multiple required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="screenshots">Screenshots: <span style="color: red;">(JPEG, BMP, or PNG files are allowed-less than 2MB in size.)</span></label>
                                                <input type="file" name="screenshots[]" class="form-control" multiple required>
                                            </div>
                                        </div>
                                        <div class="col-md-6 hid-field">
                                            <div class="form-group">


       </div>
                                        </div>
                                        <div class="col-md-6 hid-field">
                                            <div class="form-group">
                                        <input type="text" name="registry_details[]" class="form-control" value="" placeholder="Enter Registry Details" hidden> </div>
                                        </div>
                                        <div class="col-md-6 hid-field">
                                            <div class="form-group">
                                       <input type="text" name="ip[]" class="form-control" value="" placeholder="Enter IP" hidden> </div>
                                        </div>
                                        <div class="col-md-6 hid-field">
                                            <div class="form-group">
                                        <input type="text" name="registrar[]" class="form-control" value="" placeholder="Enter Registrar" hidden></div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="remarks">Remarks:</label>
                                                <textarea name="remarks[]" cols="30" rows="5" class="form-control" required></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="category">Category:</label>
                                                <select class="form-control category" name="category[]" required>
                                                    <option value="">Select Option</option>
                                                    <option value="phishing">Phishing</option>
                                                    <option value="malware">Malware</option>
                                                    <option value="fraud">Fraud</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="ticket">Content Removal Ticket:</label>
                                                <input type="text" name="ticket[]" class="form-control" placeholder="Enter Content Removal Ticket" required>
                                            </div>
                                        </div>
                                                  <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="ticket">Data Disclosure Ticket:</label>
                                                <input type="text" name="data_disclosure[]" class="form-control" placeholder="Enter Data Disclosure Ticket" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="ticket">Preservation Ticket:</label>
                                                <input type="text" name="preservation[]" class="form-control" placeholder="Enter Preservation Ticket" required>
                                            </div>
                                        </div>
                                                                                                                                        <div class="form-group">
                                        <input type="text" name="mobile[]" class="form-control" value="" placeholder="Enter mobile" hidden>

       </div>
                                                                            <div class="form-group">
                                        <input type="text" name="country_code[]" class="form-control" value="" placeholder="Enter country code" hidden>

       </div>
                                    </div>

                                    </div>
                                `;
                                break;
                        }
                    }
                }
            });
        });
    </script>
    <!-- Initialize Select2 -->
    <script>
        $(document).ready(function() {
            $('#country_code').select2({
                placeholder: "Search for a country",
                allowClear: true // Optional: Add a clear button
            });
        });
    </script>


@endsection
