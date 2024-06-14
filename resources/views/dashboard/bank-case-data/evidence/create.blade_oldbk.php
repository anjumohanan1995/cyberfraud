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
                            <a href="#">Bank Case Data</a>
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
                                    <!-- Hidden field to store acknowledgement number -->
                                    <input type="hidden" name="acknowledgement_number" value="{{ request()->segment(count(request()->segments())) }}">

                                    <div id="evidence_fields">
                                        @foreach (old('evidence_type', ['']) as $index => $oldEvidenceType)
                                            <div class="evidence-fields">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="evidence_type_{{ $index }}">Evidence type:</label>
                                                            <select class="form-control evidence_type" name="evidence_type[]" required>
                                                                <option value="">Select Option</option>
                                                                <option value="website" {{ $oldEvidenceType == 'website' ? 'selected' : '' }}>Website</option>
                                                                <option value="instagram" {{ $oldEvidenceType == 'instagram' ? 'selected' : '' }}>Instagram</option>
                                                                <option value="telegram" {{ $oldEvidenceType == 'telegram' ? 'selected' : '' }}>Telegram</option>
                                                                <option value="facebook" {{ $oldEvidenceType == 'facebook' ? 'selected' : '' }}>Facebook</option>
                                                                <option value="linkedin" {{ $oldEvidenceType == 'linkedin' ? 'selected' : '' }}>LinkedIn</option>
                                                                <option value="skype" {{ $oldEvidenceType == 'skype' ? 'selected' : '' }}>Skype</option>
                                                                <option value="gmail" {{ $oldEvidenceType == 'gmail' ? 'selected' : '' }}>Gmail</option>
                                                                <option value="youtube" {{ $oldEvidenceType == 'youtube' ? 'selected' : '' }}>Youtube</option>
                                                                <option value="mobile numbers" {{ $oldEvidenceType == 'mobile numbers' ? 'selected' : '' }}>Mobile Numbers</option>
                                                                <option value="olx ad" {{ $oldEvidenceType == 'olx ad' ? 'selected' : '' }}>OLX Ad</option>
                                                                <option value="twitter" {{ $oldEvidenceType == 'twitter' ? 'selected' : '' }}>Twitter</option>
                                                                <option value="other" {{ $oldEvidenceType == 'other' ? 'selected' : '' }}>Other</option>
                                                            </select>
                                                            @error('evidence_type.' . $index)
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="dynamicFields">
                                                    @if ($oldEvidenceType == 'website')
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="url_{{ $index }}">URL:</label>
                                                                    <input type="text" name="url[]" class="form-control" value="{{ old('url.' . $index) }}" placeholder="Enter URL">
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
                                                                    <label for="pdf_{{ $index }}">PDF:</label>
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
                                                                    <label for="pdf_{{ $index }}">PDF:</label>
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
                                                        </div>
                                                    @endif
                                                </div>
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

                evidenceFields.appendChild(evidenceTemplate);
            });

            // Event delegation to handle change events for dynamically added select elements
            document.getElementById('evidence_fields').addEventListener('change', function(event) {
                var target = event.target;
                if (target && target.classList.contains('evidence_type')) {
                    var option = target.value;
                    var dynamicFields = target.closest('.evidence-fields').querySelector('.dynamicFields');
                    dynamicFields.innerHTML = ''; // Clear previous fields

                    if (option !== "") {
                        switch (option) {
                            case 'website':
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
                                                <label for="pdf">PDF:</label>
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
                                                <label for="pdf">PDF:</label>
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
