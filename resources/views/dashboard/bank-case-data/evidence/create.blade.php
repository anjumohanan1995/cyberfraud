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
</style>

    </style>
    @php
    use Illuminate\Support\Facades\Crypt;
    $id = request()->segment(count(request()->segments()));
    $new_id = Crypt::decrypt($id);
@endphp

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

                        @if (session('error'))
                            <div class="alert alert-danger col-6">
                                {{ session('error') }}
                            </div>
                        @endif
                            <div class="table-responsive mb-0">
                                <form id="evidenceForm" action="{{ route('evidence.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="acknowledgement_number" value="{{ $new_id }}">

                                    <div id="evidence_fields">
                                        @foreach (old('evidence_type', ['']) as $index => $oldEvidenceType)
                                            <div class="evidence-fields">
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

                                                <!-- Dynamic fields will be inserted here -->
                                                <div class="dynamicFields">
                                                    @if ($oldEvidenceType == 'website')
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="url_{{ $index }}">URL:</label>
                                                                    <input type="text" name="url[]" class="form-control" value="{{ old('url.' . $index) }}" placeholder="Enter URL" oninput="extractDomain(this)">
                                                                    @if ($errors->has('url.' . $index))
                                                                        <span class="text-danger">{{ $errors->first('url.' . $index) }}</span>
                                                                    @endif

                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="domain_{{ $index }}">Domain:</label>
                                                                    <input type="text" name="domain[]" class="form-control" value="{{ old('domain.' . $index) }}" placeholder="Enter Domain">
                                                                    @error('domain.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="registry_details_{{ $index }}">Registry Details:</label>
                                                                    <input type="text" name="registry_details[]" class="form-control" value="{{ old('registry_details.' . $index) }}" placeholder="Enter Registry Details">
                                                                    @error('registry_details.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="ip_{{ $index }}">IP:</label>
                                                                    <input type="text" name="ip[]" class="form-control" value="{{ old('ip.' . $index) }}" placeholder="Enter IP" >
                                                                    @error('ip.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="registrar_{{ $index }}">Registrar:</label>
                                                                    <input type="text" name="registrar[]" class="form-control" value="{{ old('registrar.' . $index) }}" placeholder="Enter Registrar" >
                                                                    @error('registrar.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="pdf_{{ $index }}">Document:</label>
                                                                    <input type="file" name="pdf[]" class="form-control" multiple>
                                                                    @error('pdf.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="screenshots_{{ $index }}">Screenshots:</label>
                                                                    <input type="file" name="screenshots[]" class="form-control" multiple>
                                                                    @error('screenshots.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="remarks_{{ $index }}">Remarks:</label>
                                                                    <textarea name="remarks[]" cols="30" rows="5" class="form-control">{{ old('remarks.' . $index) }}</textarea>
                                                                    @error('remarks.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="category_{{ $index }}">Category:</label>
                                                                    <select class="form-control category" name="category[]">
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
                                                                    <label for="ticket_{{ $index }}">Ticket No:</label>
                                                                    <input type="text" name="ticket[]" class="form-control" value="{{ old('ticket.' . $index) }}" placeholder="Enter Ticket No" >
                                                                    @error('ticket.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @elseif (in_array($oldEvidenceType, ['instagram', 'telegram', 'facebook', 'linkedin', 'skype', 'gmail', 'youtube', 'mobile numbers', 'olx ad', 'twitter', 'other']))
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="url_{{ $index }}">URL:</label>
                                                                    <input type="text" name="url[]" class="form-control" value="{{ old('url.' . $index) }}" placeholder="Enter URL" >
                                                                    @error('url.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror

                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="pdf_{{ $index }}">Document:</label>
                                                                    <input type="file" name="pdf[]" class="form-control" multiple>
                                                                    @error('pdf.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="screenshots_{{ $index }}">Screenshots:</label>
                                                                    <input type="file" name="screenshots[]" class="form-control" multiple>
                                                                    @error('screenshots.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="remarks_{{ $index }}">Remarks:</label>
                                                                    <textarea name="remarks[]" cols="30" rows="5" class="form-control">{{ old('remarks.' . $index) }}</textarea>
                                                                    @error('remarks.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="category_{{ $index }}">Category:</label>
                                                                    <select class="form-control category" name="category[]">
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
                                                                    <label for="ticket_{{ $index }}">Ticket No:</label>
                                                                    <input type="text" name="ticket[]" class="form-control" value="{{ old('ticket.' . $index) }}" placeholder="Enter Ticket No" >
                                                                    @error('ticket.' . $index)
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>

                                                <button type="button" class="btn btn-danger remove-btn" style="display: none;">Remove</button>
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
                                dynamicFields.innerHTML = `
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="url">URL:</label>
                                                <input type="text" name="url[]" class="form-control" placeholder="Enter URL" oninput="extractDomain(this)">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="domain">Domain:</label>
                                                <input type="text" name="domain[]" class="form-control" placeholder="Enter Domain" >
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="registry_details">Registry Details:</label>
                                                <input type="text" name="registry_details[]" class="form-control" placeholder="Enter Registry Details" >
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="ip">IP:</label>
                                                <input type="text" name="ip[]" class="form-control" placeholder="Enter IP" >
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="registrar">Registrar:</label>
                                                <input type="text" name="registrar[]" class="form-control" placeholder="Enter Registrar" >
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="pdf">Document:</label>
                                                <input type="file" name="pdf[]" class="form-control" multiple>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="screenshots">Screenshots:</label>
                                                <input type="file" name="screenshots[]" class="form-control" multiple>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="remarks">Remarks:</label>
                                                <textarea name="remarks[]" cols="30" rows="5" class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="category">Category:</label>
                                                <select class="form-control category" name="category[]">
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
                                                <label for="ticket">Ticket No:</label>
                                                <input type="text" name="ticket[]" class="form-control" placeholder="Enter Ticket No" >
                                            </div>
                                        </div>
                                    </div>
                                `;
                                break;
                            case 'instagram':
                            case 'telegram':
                            case 'facebook':
                            case 'linkedin':
                            case 'skype':
                            case 'gmail':
                            case 'youtube':
                            case 'mobile numbers':
                            case 'olx ad':
                            case 'twitter':
                            case 'other':
                                dynamicFields.innerHTML = `
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="url">URL:</label>
                                                <input type="text" name="url[]" class="form-control" placeholder="Enter URL" >
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="pdf">Document:</label>
                                                <input type="file" name="pdf[]" class="form-control" multiple>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="screenshots">Screenshots:</label>
                                                <input type="file" name="screenshots[]" class="form-control" multiple>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="remarks">Remarks:</label>
                                                <textarea name="remarks[]" cols="30" rows="5" class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="category">Category:</label>
                                                <select class="form-control category" name="category[]">
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
                                                <label for="ticket">Ticket No:</label>
                                                <input type="text" name="ticket[]" class="form-control" placeholder="Enter Ticket No" >
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

@endsection
