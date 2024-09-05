@extends('layouts.app')
@section('content')
@php
use App\Models\RolePermission;
use Illuminate\Support\Facades\Auth;
$user = Auth::user();
            $role = $user->role;
            $permission = RolePermission::where('role', $role)->first();
            $permissions = $permission && is_string($permission->permission) ? json_decode($permission->permission, true) : ($permission->permission ?? []);
            $sub_permissions = $permission && is_string($permission->sub_permissions) ? json_decode($permission->sub_permissions, true) : ($permission->sub_permissions ?? []);
            if ($sub_permissions || $user->role == 'Super Admin') {
                $hasShowNCRPMailMergePermission = in_array('Show NCRP mail Merge', $sub_permissions);
                $hasViewNCRPStatus = in_array('View / Update NCRP Evidence Status', $sub_permissions);

            } else{
                $hasShowNCRPMailMergePermission = $hasViewNCRPStatus = false;
                }

@endphp


<style>
    .tabs-menu1 ul li a {
        padding: 10px 20px 11px 20px;
        display: block;
        color: #282f53;
        text-decoration: none;
    }

    .tabs-menu1 ul li a.active {
        border-bottom: 3px solid #3858f9;
        color: #3858f9; /* Optional: Change color when active */
    }

    /* Spinner animation */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Button with spinner */
.status_recheck {
    position: relative;
    overflow: hidden;
    background-color: #f0f0f0; /* Adjust background color of the button */
    padding: 10px 20px; /* Adjust padding for button size */
    border: 1px solid #ccc; /* Button border */
    color: #333; /* Button text color */
    cursor: pointer; /* Change cursor to pointer on hover */
}

.status_recheck .spinner {
    position: absolute;
    top: 30%; /* Position at the center vertically */
    left: 40%; /* Position at the center horizontally */
    transform: translate(-50%, -50%); /* Center the spinner precisely */
    border: 3px solid rgba(0, 0, 0, 0.1); /* Adjust spinner border */
    border-top-color: #007bff; /* Blue spinner color */
    border-radius: 50%;
    width: 20px;
    height: 20px;
    animation: spin 1s linear infinite;
    display: none; /* Initially hidden */
}

.status_recheck.loading .spinner {
    display: block; /* Show spinner when button is loading */
}
</style>

<link rel="stylesheet" href="path_to_bootstrap_css">
<link rel="stylesheet" href="path_to_font_awesome">
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

<div class="container-fluid">
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2">Hi, welcome back!</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Mail Merge List</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Mail Merge List</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row row-sm">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    <!-- Alert container for AJAX responses -->
                    <div id="alert_ajaxx" style="display: none;"></div>

                    <div class="row">
                        <div class="col-7">
                            <h4 class="card-title" style="display: inline;">All Evidence Corresponding to Acknowledgement No: <b style="color: red;">{{ $ack_no }}</b></h4>
                        </div>
@if($hasShowNCRPMailMergePermission)
                        <div class="col-2">
                            @if ($website->isNotEmpty())
                            <button id="statusBtn" class="btn btn-success" style="margin-left: 10px;">
                                <i class="fas fa-envelope" data-toggle="tooltip" data-placement="top" title="Mail Merge"></i>
                            </button>

                             <button class="btn btn-success btn-small status_recheck" style="margin-bottom:0px;margin-left:5px;font-size:smaller"
                             data-type="ncrp" data-ackno="{{ $ack_no }}" title="NCRP Recheck"> Recheck <i class="fa fa-sync" ></i></button>
                            {{-- <button id="portalBtn" class="btn btn-success" style="margin-left: 10px;">

                                <i class="fas fa-link" data-toggle="tooltip" data-placement="top" title="Portal Link"></i>
                              </button> --}}
                            @endif
                        </div>
                        @endif
                        <div class="col-3">

                        </div>
                    </div>

                        <!-- status Modal -->
    <div class="modal fade" id="status-popup" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Mail Merge</h2>
                    <button type="button" class="btn-close" onclick="closeModal()" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadOrderForm" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="statusType" class="form-label">Status:</label>
                            <select id="statusType" class="form-select" name="statusType" required>
                                <option value="">Select Status Type</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="reported">Reported</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="noticeType" class="form-label">Notice Type:</label>
                            <select id="noticeType" class="form-select" name="noticeType" required>
                                <option value="">Select Notice Type</option>
                            </select>
                        </div>

                        <div class="text-center">
                            <button type="button" class="btn btn-success" id="sendMail">Send Mail</button>
                        </div>
                        <div class="text-center" style="margin-top: 20px;">
                            <div id="loader" style="display: none;">
                                <p style="color: red;">
                                    Sending emails... Please wait.
                                    <i class="fa fa-spinner fa-spin"></i> <!-- Font Awesome spinner -->
                                </p>
                                <!-- Add your loader animation here -->
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Portal Modal -->
<div class="modal fade" id="portal-popup" tabindex="-1" role="dialog" aria-labelledby="portalPopupLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="portalPopupLabel">Enter Portal Count</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
                            <!-- Hidden input to store registrar ID -->
                            <input type="hidden" id="registrarId" name="registrarId">
          <form>
            <div class="form-group">
                <label for="portalstatusType" class="form-label">Status:</label>
                <select id="portalstatusType" class="form-select" name="portalstatusType" required>
                    <!-- Options will be populated by JavaScript -->
                </select>
            </div>
            <div class="form-group">
              <label for="portalCount">Portal Count</label>
              <input type="number" class="form-control" id="portalCount" placeholder="Enter portal count">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="savePortalCount">Save changes</button>
        </div>
      </div>
    </div>
  </div>




                    <input type="hidden" id="caseData" value="ncrp">
                    <input type="hidden" id="ack_no" value="{{ $ack_no }}">

                    <div class="table-responsive">
                        <table id="ncrp" class="table table-hover table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>SL No</th>
                                    {{-- <th>Acknowledgement No</th> --}}
                                    <th>Evidence Type</th>
                                    <th>URL/Mobile</th>
                                    {{-- <th>Mobile</th> --}}
                                    <th>Domain</th>
                                    <th>IP</th>
                                    <th>Registrar</th>
                                    <th>Registry Details</th>
                                    <th>Generate Notice</th>
                                    <th>Portal link</th>
                                    <th>Reported Status</th>
                                    @if($hasViewNCRPStatus)<th>Status</th>@endif
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="path_to_bootstrap_js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<link rel="stylesheet" href="{{ asset('css/toastr.min.css') }}">
<script src="{{ asset('js/toastr.js') }}"></script>


<script src="path_to_bootstrap_js"></script>



<link rel="stylesheet" href="{{ asset('css/toastr.min.css') }}">
<script src="{{ asset('js/toastr.js') }}"></script>

<script>
    $(document).ready(function() {
        // Show the upload modal when the button is clicked
        $('#statusBtn').on('click', function() {
            $('#status-popup').modal('show');
        });
    }); // <-- Added closing parenthesis for $(document).ready() function

    function closeModal() {
    $('#status-popup').modal('hide');
    // or use vanilla JavaScript approach


}


</script>

<script>
    $(document).ready(function() {
        $('#statusType').change(function() {
            var selectedStatus = $(this).val();
            var noticeTypeDropdown = $('#noticeType');

            // Clear previous options
            noticeTypeDropdown.empty();

            // Populate options based on selected status
            if (selectedStatus === 'active') {
                noticeTypeDropdown.append('<option value="Notice U/s 91 CrPC & 79(3)(b) of IT Act">Notice U/s 91 CrPC & 79(3)(b) of IT Act</option>');
                noticeTypeDropdown.append('<option value="Notice U/s 91 CrPC">Notice U/s 91 CrPC</option>');
                noticeTypeDropdown.append('<option value="Notice U/s 79(3)(b) of IT Act">Notice U/s 79(3)(b) of IT Act</option>');
                noticeTypeDropdown.append('<option value="For All Notice Type">For All Notice Type</option>');
            } else if (selectedStatus === 'inactive') {
                noticeTypeDropdown.append('<option value="Notice U/s 91 CrPC">Notice U/s 91 CrPC</option>');
            } else if (selectedStatus === 'reported') {
                noticeTypeDropdown.append('<option value="Notice U/s 91 CrPC & 79(3)(b) of IT Act">Notice U/s 91 CrPC & 79(3)(b) of IT Act</option>');
                noticeTypeDropdown.append('<option value="Notice U/s 91 CrPC">Notice U/s 91 CrPC</option>');
                noticeTypeDropdown.append('<option value="Notice U/s 79(3)(b) of IT Act">Notice U/s 79(3)(b) of IT Act</option>');
                noticeTypeDropdown.append('<option value="For All Notice Type">For All Notice Type</option>');
            }
            // You can add more conditions for other status types if needed

        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#sendMail').on('click', function() {
    var statusType = $('#statusType').val();
    var noticeType = $('#noticeType').val();
    var ack_no = $('#ack_no').val();
    var caseData = $('#caseData').val();

    // Validate if both fields are selected
    if (statusType && noticeType) {
        $('#loader').show();
        $.ajax({
            type: 'POST',
            url: '{{ route("send-email") }}',
            data: {
                statusType: statusType,
                noticeType: noticeType,
                ack_no: ack_no,
                caseData: caseData,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#alert_ajaxx').html('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                    response.success +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span></button></div>').show();
                $('#status-popup').modal('hide');
                $('#ncrp').DataTable().ajax.reload();
            },
            error: function(xhr, status, error) {
                var errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'Error sending email.';
                $('#alert_ajaxx').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                    errorMessage +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span></button></div>').show();
                console.error('Error sending email:', error);
                $('#status-popup').modal('hide');
            },
            complete: function() {
                $('#loader').hide();
            }
        });
    } else {
        alert('Please select both Status Type and Notice Type.');
    }
});
    });
</script>



<script>
    $(document).ready(function() {
        // Initialize DataTable for #ncrp
        var tableNew = $('#ncrp').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('get.mailmergelist.ncrp') }}",
                data: function(d) {
                    // d.website_id = $('#website_id').val();
                    d.ack_no = $('#ack_no').val();
                },
            },
            columns: [
                { data: 'id' },
                // { data: 'acknowledgement_no' },
                { data: 'evidence_type' },
                { data: 'url' },
                //{ data: 'mobile' },
                { data: 'domain' },
                { data: 'ip' },
                { data: 'registrar' },
                { data: 'registry_details' },
                { data: 'notice_generation' },
                { data: 'portal_link' },
                { data: 'mail_status' },
                @if($hasViewNCRPStatus)
                { data: 'status' },
                @endif
            ],
            order: [0, 'desc'],
            ordering: true
        });
    });
</script>


<script>
    // Function to handle the onchange event of radio buttons
    function toggleReportStatus(radio) {
        var id = radio.getAttribute('data-id');
        var statusValue = radio.value;

        // Get CSRF token from meta tag
        var csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;

        // AJAX request to update the reported status
        $.ajax({
            url: '/update-reported-status/' + id,
            type: 'POST',
            data: {
                status: statusValue
            },
            headers: {
                'X-CSRF-TOKEN': csrfToken // Include CSRF token in headers
            },
            success: function(response) {
                // Update UI or handle success response
                //console.log('Status updated successfully.');
            },
            error: function(xhr, status, error) {
                // Handle error
                console.error('Error updating status:', error);
            }
        });
    }
</script>

<script>
    $(document).ready(function() {
        $('#savePortalCount').on('click', function() {
            var portalCount = $('#portalCount').val();
            var ack_no = $('#ack_no').val();
            var portalstatusType = $('#portalstatusType').val();
            var registrarId = $('#registrarId').val();
            var caseData = $('#caseData').val();

            $.ajax({
                url: '{{ route("update.portal.count") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    portalCount: portalCount,
                    ack_no: ack_no,
                    portalstatusType: portalstatusType,
                    registrarId: registrarId,
                    caseData: caseData,
                },
                success: function(response) {
                    $('#alert_ajaxx').html('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                            response.success +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                            '<span aria-hidden="true">&times;</span></button></div>').show();
                    $('#portal-popup').modal('hide');
                    $('#ncrp').DataTable().ajax.reload();
                },
                error: function(xhr, status, error) {
                    var errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'Error updating portal count.';
                    $('#alert_ajaxx').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                        errorMessage +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span></button></div>').show();
                    console.error('Error updating portal count:', error);
                    $('#portal-popup').modal('hide');
                }
            });
        });
    });
</script>


<script>
    function showPortalModal(id, reported_status) {
        $('#registrarId').val(id);

        // Update the options based on reported_status
        let selectElement = $('#portalstatusType');
        selectElement.empty(); // Clear existing options

        if (reported_status == "reported") {  // Fix comparison operator
            selectElement.append('<option value="reported">Reported</option>');
        } else {
            selectElement.append('<option value="">Select Status Type</option>');
            selectElement.append('<option value="active">Active</option>');
            selectElement.append('<option value="inactive">Inactive</option>');
            selectElement.append('<option value="reported">Reported</option>');
        }

        $('#portal-popup').modal('show');
    }
</script>



<script>

$(document).ready(function(){
    $('.status_recheck').click(function(){

     var type = $(this).data('type');
     var ackno = $(this).data('ackno');

     var $button = $(this);
     var buttonText = $button.text().trim();
     $button.prop('disabled', true);
     $button.addClass('loading');
     var spinner = $('<div class="spinner"></div>');
     $button.append(spinner);

    $.ajax({
    url: "{{ route('url_status_recheck') }}",
    data:{type:type,ackno:ackno},
    success:function(response){

    $button.removeClass('loading');
    $button.prop('disabled', false); // Re-enable button
    spinner.remove(); // Remove spinner element

    console.log(response);
            if(response.success){

            toastr.success(' url status updated!');
            }
            else{

                toastr.error(' updation error!');
            }
            $('#ncrp').DataTable().ajax.reload();
    }
    });
    })

  $(document).on('click', '.check-status', function(e) {

       e.preventDefault();
       var url = $(this).data('url');
       var type = $(this).data('type');

       $.ajax({
        url:"{{ route('get_url_status') }}",
        data:{
            url:url,
            type:type
        },
        success:function(response){

            console.log(response.statuscode);
             var statuscode = response.statuscode !== null ? response.statuscode : 'Not updated.Recheck';
             var statustext = response.statustext !== null ? response.statustext : 'Not updated.Recheck';
             var htm = 'URL - ' +response.url + '<br> Status Code - '+statuscode+ '<br> Status - '+statustext+'';

             $('.url-display').html(htm);
             $('#showUrlStatus').modal('show');
        },
        error: function(xhr, status, error) {
            // Handle AJAX errors here
            console.error(xhr);
            var errorMessage = "Error fetching URL status.Recheck";
            $('.url-display').html(errorMessage);
            $('#showUrlStatus').modal('show');
        }
       })


     })

     function storeEvidence(identifier, idField) {
        // alert("hi");
        $.ajax({
            console.log("hi");
            url: "{{ route('evidenceStore') }}",
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                identifier: identifier,
                idField: idField
            },
            success: function(response) {
                console.log('Data saved successfully:', response);
            },
            error: function(xhr, status, error) {
                console.error('Error saving data:', error);
            }
        });
    }
})

</script>

<script>

    function storeEvidence(identifier, idField) {
        $.ajax({
            url: "{{ route('individualevidenceStore') }}",
            type: "POST",
            data: {
                identifier: identifier,
                idField: idField,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                // alert("Data submitted successfully");

                var data = response.data;
                var source_type = response.source_type;
                var evidence_type_ncrp = response.evidence_type_ncrp;

                // Hide all modals first to ensure they are correctly toggled
                $('#noticeTable_ncrp_website').modal('hide');
                $('#noticeTable_ncrp_social_media').modal('hide');

                // Remove any existing click event handlers to prevent multiple attachments
                $('#noticeTable_ncrp_website .btn').off('click');
                $('#noticeTable_ncrp_social_media .btn').off('click');

                if (source_type == 'ncrp') {
                        if (evidence_type_ncrp == 'website' && data) {
                            $('#noticeTable_ncrp_website').modal('show'); // Show the website table
                        } else if (evidence_type_ncrp != 'website' && data && evidence_type_ncrp != null) {
                            $('#noticeTable_ncrp_social_media').modal('show');
                        }
                }


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
            },
            error: function(xhr, status, error) {
                alert('Error: ' + error);
            }
        });
    }

            // Function to generate notice
            function generateNotice(noticeId, data, source_type) {
            console.log(data)
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
            // Check if response.success is defined and true
            if (response && response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message || "Notice generated successfully",
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            } else {
                // Handle undefined response or missing success field
                Swal.fire({
                    icon: 'warning',
                    title: 'No Evidence Found',
                    text: response.message || "No such evidence exists",
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            }
        },
        error: function(xhr) {
            if (xhr.status === 409) {
                Swal.fire({
                    icon: 'info',
                    title: 'Duplicate Notice',
                    text: xhr.responseJSON.message,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: "An error occurred while generating the notice",
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            }
        }
    });
}

    </script>




@endsection
