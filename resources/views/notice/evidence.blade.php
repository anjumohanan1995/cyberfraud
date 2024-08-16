@extends('layouts.app')
@php
use App\Models\RolePermission;
use Illuminate\Support\Facades\Auth;
$user = Auth::user();
            $role = $user->role;
            $permission = RolePermission::where('role', $role)->first();
            $permissions = $permission && is_string($permission->permission) ? json_decode($permission->permission, true) : ($permission->permission ?? []);
            $sub_permissions = $permission && is_string($permission->sub_permissions) ? json_decode($permission->sub_permissions, true) : ($permission->sub_permissions ?? []);
            if ($sub_permissions || $user->role == 'Super Admin') {
                $hasShowESTFPermission = in_array('Show Evidence Source Type Filter', $sub_permissions);
                $hasShowETFPermission = in_array('Show Evidence Type Filter', $sub_permissions);
                $hasShowStatusFPermission = in_array('Show Notice Status Filter', $sub_permissions);
                $hasShowNoticeTypePermission = in_array('Show Notice Type Filter', $sub_permissions);
                $hasGenerateTokenPermission = in_array('Generate Token', $sub_permissions);
            } else{
                    $hasShowTTypePermission = $hasShowBankPermission = $hasShowFilledByPermission = $hasShowComplaintRepoPermission = $hasShowFIRLodgePermission = $hasShowStatusPermission = $hasShowSearchByPermission = $hasShowSubCategoryPermission = false;
                }

@endphp
@section('content')
<div class="container-fluid">
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2">Hi, welcome back!</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Notice Management</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Generate Notice</li>
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
<div class="container mt-5">
    {{-- <div id="error-message" style="display: none; color: red;"></div> --}}
    <!-- Trigger the modal with a button -->
    <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#evidenceModal">Against Evidence</button>


    <!-- Modal -->
    <div id="evidenceModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Select Evidence</h4>
                    <div class="invalid-feedback"></div>
                    <button type="button" class="close" data-dismiss="modal" onclick="closeModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="evidenceForm">
                        <div class="form-group">
                            <label for="source_type">Source Type:</label>
                            <select class="form-control" id="source_type" name="source_type">
                                <option value="">--Select--</option>
                                <option value="ncrp">NCRP</option>
                                <option value="other">Other</option>
                            </select>

                        </div>

                        <div class="form-group">
                            <label for="from_date">From Date:</label>
                            <input type="date" class="form-control" id="from_date" name="from_date">

                        </div>

                        <div class="form-group">
                            <label for="to_date">To Date:</label>
                            <input type="date" class="form-control" id="to_date" name="to_date">

                        </div>

                        <div class="form-group">
                            <label for="ack_no">Acknowledgement Number:</label>
                            <input type="text" class="form-control" id="ack_no" name="ack_no">

                        </div>

                        <div class="form-group">
                            <label for="case_no">Case Number:</label>
                            <input type="text" class="form-control" id="case_no" name="case_no">

                        </div>

                        <div class="form-group">
                            <label for="evidence_type_ncrp">Evidence Type:</label>
                            <select class="form-control" id="evidence_type_ncrp" name="evidence_type_ncrp">
                                <option value="">--Select--</option>
                                @foreach ($evidence_types as $et)
                                @if ($et->name != 'mobile' && $et->name != 'whatsapp')
                                <option value="{{ $et->_id }}">{{ $et->name }}</option>
                            @endif
                                @endforeach
                            </select>

                        </div>

                        <div class="form-group">
                            <label for="evidence_type_other">Evidence Type:</label>
                            <select class="form-control" id="evidence_type_other" name="evidence_type_other">
                                <option value="">--Select--</option>
                                @foreach ($evidence_types as $et)
                                @if ($et->name != 'mobile' && $et->name != 'whatsapp')
                                    <option value="{{ $et->name }}"> {{ $et->name }} </option>
                                @endif
                                @endforeach
                            </select>

                        </div>

                        <div class="form-group">
                            <label for="status">Status:</label>

                            <select class="form-control" id="status" name="status">
                                <option value="">--Select--</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="reported">Reported</option>
                            </select>


                        </div>

                        <button type="button" class="btn btn-primary" id="submitEvidence">Submit</button><br><br>
                        <div id="errorDiv" style="display: none; color: red;"></div>
                    </form>

                </div>
            </div>


        </div>
    </div>


<!-- Modal Structure -->
<div id="noticeTable_ncrp" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Notice Table</h4>
                <button type="button" class="close" data-dismiss="modal" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Notice - NCRP</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Notice U/Sec. 94 of BNSS & 79(3)(b) of IT Act 2000 - social media</td>
                            <td><button class="btn btn-primary" id="generate-notice-1" data-notice="Notice U/Sec. 94 of BNSS & 79(3)(b) of IT Act 2000 - NCRP - social media">Generate Notice</button></td>
                        </tr>
                        <tr>
                            <td>Notice U/Sec. 94 of BNSS & 79(3)(b) of IT Act 2000 - website</td>
                            <td><button class="btn btn-primary" id="generate-notice-2" data-notice="Notice U/Sec. 94 of BNSS & 79(3)(b) of IT Act 2000 - NCRP - website">Generate Notice</button></td>
                        </tr>
                        <tr>
                            <td>Notice U/sec 79(3)(b) of IT Act - social media</td>
                            <td><button class="btn btn-primary" id="generate-notice-3" data-notice="Notice U/sec 79(3)(b) of IT Act - NCRP - social media">Generate Notice</button></td>
                        </tr>
                        <tr>
                            <td>Notice U/sec 79(3)(b) of IT Act - website</td>
                            <td><button class="btn btn-primary" id="generate-notice-4" data-notice="Notice U/sec 79(3)(b) of IT Act - NCRP - website">Generate Notice</button></td>
                        </tr>
                        <tr>
                            <td>Notice U/Sec.94 BNSS Act 2023 - social media</td>
                            <td><button class="btn btn-primary" id="generate-notice-5" data-notice="Notice U/Sec.94 BNSS Act 2023 - NCRP - social media">Generate Notice</button></td>
                        </tr>
                        <tr>
                            <td>Notice U/Sec.94 BNSS Act 2023 - website</td>
                            <td><button class="btn btn-primary" id="generate-notice-6" data-notice="Notice U/Sec.94 BNSS Act 2023 - NCRP - website">Generate Notice</button></td>
                        </tr>
                        {{-- <tr>
                            <td>For All Notice Type Above</td>
                            <td><button class="btn btn-primary" id="generate-notice-6" data-notice="all_ncrp">Generate Notice</button></td>
                        </tr> --}}
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Modal Structure -->
<div id="noticeTable_other" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Notice Table</h4>
                <button type="button" class="close" data-dismiss="modal" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Notice - Other</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Assign unique IDs to each button for handling clicks -->
                        <tr>
                            <td>Notice U/Sec. 94 of BNSS & 79(3)(b) of IT Act 2000 - social media</td>
                            <td><button class="btn btn-primary" id="generate-notice-1" data-notice="Notice U/Sec. 94 of BNSS & 79(3)(b) of IT Act 2000 - Other - social media">Generate Notice</button></td>
                        </tr>
                        <tr>
                            <td>Notice U/Sec. 94 of BNSS & 79(3)(b) of IT Act 2000 - website</td>
                            <td><button class="btn btn-primary" id="generate-notice-2" data-notice="Notice U/Sec. 94 of BNSS & 79(3)(b) of IT Act 2000 - Other - website">Generate Notice</button></td>
                        </tr>
                        <tr>
                            <td>Notice U/sec 79(3)(b) of IT Act - social media</td>
                            <td><button class="btn btn-primary" id="generate-notice-3" data-notice="Notice U/sec 79(3)(b) of IT Act - Other - social media">Generate Notice</button></td>
                        </tr>
                        <tr>
                            <td>Notice U/sec 79(3)(b) of IT Act - website</td>
                            <td><button class="btn btn-primary" id="generate-notice-4" data-notice="Notice U/sec 79(3)(b) of IT Act - Other - website">Generate Notice</button></td>
                        </tr>
                        <tr>
                            <td>Notice U/Sec.94 BNSS Act 2023 - social media</td>
                            <td><button class="btn btn-primary" id="generate-notice-5" data-notice="Notice U/Sec.94 BNSS Act 2023 - Other - social media">Generate Notice</button></td>
                        </tr>
                        <tr>
                            <td>Notice U/Sec.94 BNSS Act 2023 - website</td>
                            <td><button class="btn btn-primary" id="generate-notice-6" data-notice="Notice U/Sec.94 BNSS Act 2023 - Other - website">Generate Notice</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Structure -->
<div id="noticeTable_ncrp_website" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Notice Table</h4>
                <button type="button" class="close" data-dismiss="modal" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Notice - NCRP - Website</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Notice U/Sec. 94 of BNSS & 79(3)(b) of IT Act 2000 - website</td>
                            <td><button class="btn btn-primary" id="generate-notice-1" data-notice="Notice U/Sec. 94 of BNSS & 79(3)(b) of IT Act 2000 - NCRP - website">Generate Notice</button></td>
                        </tr>
                        <tr>
                            <td>Notice U/sec 79(3)(b) of IT Act - website</td>
                            <td><button class="btn btn-primary" id="generate-notice-2" data-notice="Notice U/sec 79(3)(b) of IT Act - NCRP - website">Generate Notice</button></td>
                        </tr>
                        <tr>
                            <td>Notice U/Sec.94 BNSS Act 2023 - website</td>
                            <td><button class="btn btn-primary" id="generate-notice-3" data-notice="Notice U/Sec.94 BNSS Act 2023 - NCRP - website">Generate Notice</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>


<!-- Modal Structure -->
<div id="noticeTable_ncrp_social_media" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Notice Table</h4>
                <button type="button" class="close" data-dismiss="modal" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Notice - NCRP - Social Media</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Notice U/Sec. 94 of BNSS & 79(3)(b) of IT Act 2000 - social media</td>
                            <td><button class="btn btn-primary" id="generate-notice-1" data-notice="Notice U/Sec. 94 of BNSS & 79(3)(b) of IT Act 2000 - NCRP - social media">Generate Notice</button></td>
                        </tr>
                        <tr>
                            <td>Notice U/sec 79(3)(b) of IT Act - social media</td>
                            <td><button class="btn btn-primary" id="generate-notice-2" data-notice="Notice U/sec 79(3)(b) of IT Act - NCRP - social media">Generate Notice</button></td>
                        </tr>
                        <tr>
                            <td>Notice U/Sec.94 BNSS Act 2023 - social media</td>
                            <td><button class="btn btn-primary" id="generate-notice-3" data-notice="Notice U/Sec.94 BNSS Act 2023 - NCRP - social media">Generate Notice</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>


<!-- Modal Structure -->
<div id="noticeTable_other_website" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Notice Table</h4>
                <button type="button" class="close" data-dismiss="modal" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Notice - Other - Website</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Notice U/Sec. 94 of BNSS & 79(3)(b) of IT Act 2000 - website</td>
                            <td><button class="btn btn-primary" id="generate-notice-1" data-notice="Notice U/Sec. 94 of BNSS & 79(3)(b) of IT Act 2000 - Other - website">Generate Notice</button></td>
                        </tr>
                        <tr>
                            <td>Notice U/sec 79(3)(b) of IT Act - website</td>
                            <td><button class="btn btn-primary" id="generate-notice-2" data-notice="Notice U/sec 79(3)(b) of IT Act - Other - website">Generate Notice</button></td>
                        </tr>
                        <tr>
                            <td>Notice U/Sec.94 BNSS Act 2023 - website</td>
                            <td><button class="btn btn-primary" id="generate-notice-3" data-notice="Notice U/Sec.94 BNSS Act 2023 - Other - website">Generate Notice</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>


<!-- Modal Structure -->
<div id="noticeTable_other_social_media" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Notice Table</h4>
                <button type="button" class="close" data-dismiss="modal" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Notice - Other - Social Media</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Notice U/Sec. 94 of BNSS & 79(3)(b) of IT Act 2000 - social media</td>
                            <td><button class="btn btn-primary" id="generate-notice-1" data-notice="Notice U/Sec. 94 of BNSS & 79(3)(b) of IT Act 2000 - Other - social media">Generate Notice</button></td>
                        </tr>
                        <tr>
                            <td>Notice U/sec 79(3)(b) of IT Act - social media</td>
                            <td><button class="btn btn-primary" id="generate-notice-2" data-notice="Notice U/sec 79(3)(b) of IT Act - Other - social media">Generate Notice</button></td>
                        </tr>
                        <tr>
                            <td>Notice U/Sec.94 BNSS Act 2023 - social media</td>
                            <td><button class="btn btn-primary" id="generate-notice-3" data-notice="Notice U/Sec.94 BNSS Act 2023 - Other - social media">Generate Notice</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>




</div>
</div>
</div>
</div>
<!-- /row -->
</div>







<script>
$(document).ready(function(){
    $('#submitEvidence').click(function(){
        var source_type = $('#source_type').val();
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        var ack_no = $('#ack_no').val();
        var case_no = $('#case_no').val();
        var evidence_type_ncrp = $('#evidence_type_ncrp').val();
        var evidence_type_other = $('#evidence_type_other').val();
        var status = $('#status').val();

                // Validate form data
                var errors = [];
        if (!source_type) errors.push('Source type is required.');
        if (!from_date) errors.push('From date is required.');
        if (!to_date) errors.push('To date is required.');
        // Add more validation checks as needed

        if (errors.length > 0) {
            // Show errors
            $('#errorDiv').html('<ul>' + errors.map(function(error) {
                return '<li>' + error + '</li>';
            }).join('') + '</ul>').show();
            return; // Stop execution if there are validation errors
        }


        $.ajax({
            url: "{{ route('evidenceStore') }}", // Replace with your route
            type: "POST",
            data: {
                source_type: source_type,
                from_date: from_date,
                to_date: to_date,
                ack_no: ack_no,
                case_no: case_no,
                evidence_type_ncrp: evidence_type_ncrp,
                evidence_type_other: evidence_type_other,
                status: status,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                // console.log(response);
                // alert(response.error_messages);
                if (response.error_messages && response.error_messages.length > 0) {
                    // Show errors
                    $('#errorDiv').html('<ul>' + response.error_messages.map(function(error) {
                        return '<li>' + error + '</li>';
                    }).join('') + '</ul>').show();
                }  else  {
                    alert("Data submitted successfully");
                    // Handle successful response and show modals based on conditions
                    // Your existing logic to show modals based on the response

            // },

    // Access the data from the response
    var data = response.data;
    var source_type = response.source_type;
    var evidence_type_ncrp = response.evidence_type_ncrp;
    var evidence_type_ncrp_name = response.evidence_type_ncrp_name;
    var evidence_type_other = response.evidence_type_other;


    // Process the data as needed
   // console.log(data);

    // Hide all modals first to ensure they are correctly toggled
    $('#evidenceModal').modal('hide');
    $('#noticeTable_ncrp').modal('hide');
    $('#noticeTable_ncrp_website').modal('hide');
    $('#noticeTable_ncrp_social_media').modal('hide');
    $('#noticeTable_other').modal('hide');
    $('#noticeTable_other_website').modal('hide');
    $('#noticeTable_other_social_media').modal('hide');

    // Show the appropriate table based on the conditions
    if (source_type == 'ncrp') {
        if (evidence_type_ncrp_name == 'website' && data) {
            $('#noticeTable_ncrp_website').modal('show'); // Show the website table
        } else if (evidence_type_ncrp_name != 'website' && data && evidence_type_ncrp_name != null) {
            $('#noticeTable_ncrp_social_media').modal('show');
        } else if (source_type == 'ncrp' && data) {
            $('#noticeTable_ncrp').modal('show'); // Show the general table
    }
}

    if (source_type == 'other') {
        if (evidence_type_other == 'website'  && data) {
            $('#noticeTable_other_website').modal('show'); // Show the website table
        } else if (evidence_type_other != 'website' && data && evidence_type_other != null) {
            $('#noticeTable_other_social_media').modal('show');
        } else if (source_type == 'other' && data) {
            $('#noticeTable_other').modal('show'); // Show the general table
    }
}

          // Attach click event handlers to "Generate Notice" buttons
          $('#noticeTable_ncrp .btn').click(function(){
                    var noticeId = $(this).data('notice');
                    // alert(noticeId);// Get the notice ID from button data attribute
                    generateNotice(noticeId, data, source_type); // Call function to generate notice with ID and data
                });
                          // Attach click event handlers to "Generate Notice" buttons
          $('#noticeTable_ncrp_social_media .btn').click(function(){
                    var noticeId = $(this).data('notice');
                    // alert(noticeId);// Get the notice ID from button data attribute
                    generateNotice(noticeId, data, source_type); // Call function to generate notice with ID and data
                });
                          // Attach click event handlers to "Generate Notice" buttons
          $('#noticeTable_ncrp_website .btn').click(function(){
                    var noticeId = $(this).data('notice');
                    // alert(noticeId);// Get the notice ID from button data attribute
                    generateNotice(noticeId, data, source_type); // Call function to generate notice with ID and data
                });
                          // Attach click event handlers to "Generate Notice" buttons
          $('#noticeTable_other .btn').click(function(){
                    var noticeId = $(this).data('notice');
                    // alert(noticeId);// Get the notice ID from button data attribute
                    generateNotice(noticeId, data, source_type); // Call function to generate notice with ID and data
                });
                          // Attach click event handlers to "Generate Notice" buttons
          $('#noticeTable_other_social_media .btn').click(function(){
                    var noticeId = $(this).data('notice');
                    // alert(noticeId);// Get the notice ID from button data attribute
                    generateNotice(noticeId, data, source_type); // Call function to generate notice with ID and data
                });
                          // Attach click event handlers to "Generate Notice" buttons
          $('#noticeTable_other_website .btn').click(function(){
                    var noticeId = $(this).data('notice');
                    // alert(noticeId);// Get the notice ID from button data attribute
                    generateNotice(noticeId, data, source_type); // Call function to generate notice with ID and data
                });
            }
            },
            error: function(xhr, status, error){
            // Clear previous errors
            $('.text-danger').text('');

            // Display new errors
            if (xhr.responseJSON.errors) {
                $.each(xhr.responseJSON.errors, function(key, error) {
                    $('#'+key+'_error').text(error[0]);
                });
                }
            }
        });
    });

    // Function to generate notice
    function generateNotice(noticeId, data, source_type) {
        // console.log(data)
        $.ajax({
            url: "{{ route('generate.notice') }}", // Replace with your route to generate notice
            type: "POST",
            data: {
                notice_id: noticeId,
                data: data,
                source_type: source_type,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                alert("Notice generated successfully");
                // location.reload();
                // Process the response if needed
            },
            error: function(xhr, status, error) {
                alert("An error occurred while generating notice");
            }
        });
    }

});
</script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Hide all fields initially
        $('#ack_no').closest('.form-group').hide();
        $('#case_no').closest('.form-group').hide();
        $('#evidence_type_ncrp').closest('.form-group').hide();
        $('#evidence_type_other').closest('.form-group').hide();

        $('#source_type').change(function() {
            var selectedValue = $(this).val();

            if (selectedValue === 'ncrp') {
                // Show Acknowledgement Number and hide Case Number
                $('#ack_no').closest('.form-group').show();
                $('#evidence_type_ncrp').closest('.form-group').show();
                $('#case_no').closest('.form-group').hide();
                $('#evidence_type_other').closest('.form-group').hide();
            } else if (selectedValue === 'other') {
                // Show Case Number and hide Acknowledgement Number
                $('#ack_no').closest('.form-group').hide();
                $('#evidence_type_ncrp').closest('.form-group').hide();
                $('#case_no').closest('.form-group').show();
                $('#evidence_type_other').closest('.form-group').show();
            } else {
                // Hide both fields if no valid option is selected
                $('#ack_no').closest('.form-group').hide();
                $('#evidence_type_ncrp').closest('.form-group').hide();
                $('#case_no').closest('.form-group').hide();
                $('#evidence_type_other').closest('.form-group').hide();
            }
        });
    });
    </script>

<script>
    function closeModal() {
        location.reload();
    }
</script>





@endsection
