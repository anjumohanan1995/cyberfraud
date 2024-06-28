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
                            <a href="#">Case Data Management</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Case Data
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
                                <div id="alert_ajaxx" style="display:none">

                                </div>
                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show w-100" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                                <div class="alert alert-success-one alert-dismissible fade show w-100" role="alert"
                                    style="display:none">

                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>
                            <div class=" m-4 d-flex justify-content-between">
                                <h4 class="card-title mg-b-10">
                                    All Case Data
                                </h4>
                                {{-- <div class="col-md-1 col-6 text-center">
                                    <div class="task-box primary  mb-0">
                                        <a class="text-white" href="{{ route('modus.create') }}">
                                            <p class="mb-0 tx-12">Add </p>
                                            <h3 class="mb-0"><i class="fa fa-plus"></i></h3>
                                        </a>
                                    </div>
                                </div> --}}
                            </div>
                            <form id="complaint-form">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="from-date">From Date:</label>
                                            <input type="date" class="form-control" id="from-date" name="from_date">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="to-date">To Date:</label>
                                            <input type="date" class="form-control" id="to-date" name="to_date">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="mobile">Complaint Mobile no: </label>
                                            <input type="text" class="form-control" id="mobile" name="mobile">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="type">Transaction Type:</label><br>
                                            <select class="form-control" id="type">
                                                <option>--Select--</option>
                                                <option value="bank">Bank</option>
                                                <option value="wallet">Wallet/PG/PA</option>
                                                <option value="merchant">Merchant</option>
                                                <option value="insurance">Insurance</option>
                                            </select>
                                            <br>

                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="options">Bank/Wallet/Merchant/Insurance:</label>
                                            <select class="form-control" id="options"></select>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="filled-by">Filled by(within 24 hrs):</label>
                                            <select class="form-control" id="filled-by" name="filled-by">
                                                <option value="">All</option>
                                                <option value="citizen">Citizen</option>
                                                <option value="cyber">Cyber</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="filled_by_who">Complaint Reported:</label>
                                            <select class="form-control" id="filled_by_who" name="filled_by_who">
                                                <option value="">All</option>
                                                <option value="citizen">Through Helpline(1930)</option>
                                                <option value="cyber">Cyber Crime Portal</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="acknowledgement_no">Acknowledgement No: </label>
                                            <input type="text" class="form-control" id="acknowledgement_no"
                                                name="acknowledgement_no">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="fir_lodge">FIR Lodge:</label>
                                            <select class="form-control" id="fir_lodge" name="fir_lodge">
                                                <option value="">--Select--</option>
                                                <option value="1">Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="com_status">Status </label>
                                            <select class="form-control" id="com_status">
                                                <option value="empty">--Select--</option>
                                                <option value="1">Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div>
                                    </div>



                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="search-by">Search by:</label>
                                            <select class="form-control" id="search-by" name="search-by" onchange="showTextBox()">
                                                <option>--Select--</option>
                                                <option value="account_id">Account ID/Account Number</option>
                                                <option value="account_id">UPI ID</option>
                                                <option value="transaction_id">Transaction ID/UTR/RRN Number</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4" id="account-id-input" style="display: none;">
                                        <div class="form-group">
                                            <label for="account-id">Enter Account ID/Account Number/UPI ID:</label>
                                            <input type="text" class="form-control" id="account-id" name="account-id">
                                        </div>
                                    </div>
                                    <div class="col-md-4" id="transaction-id-input" style="display: none;">
                                        <div class="form-group">
                                            <label for="transaction-id">Enter Transaction ID/UTR/RRN Number:</label>
                                            <input type="text" class="form-control" id="transaction-id" name="transaction-id">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="sub-category">Sub category:</label>
                                            <select class="form-control" id="sub-category" name="sub-category">
                                                <option value="">--Select--</option>
                                                <option value="#">Aadhar Enabled Payment System (AEPS)</option>
                                                <option value="#">Business Email Compromise/Email Takeover</option>
                                                <option value="#">Debit/Credit Card Fraud/Sim Swap Fraud</option>
                                                <option value="#">Demat/Depository Fraud</option>
                                                <option value="#">E-Wallet Related Fraud</option>
                                                <option value="#">Fraud Call/Vishing</option>
                                                <option value="#">Internet Banking Related Fraud</option>
                                                <option value="#">UPI Related Frauds</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>


                                <div class="row">
                                    {{-- <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="search-by">Search by:</label>
                                            <select class="form-control" id="search-by" name="search-by">
                                                <option value="">--Select--</option>
                                                <option value="account_id">Account ID/Account Number/UPI ID</option>
                                                <option value="transaction_id">Transaction ID/UTR/RRN Number</option>
                                            </select>
                                        </div>
                                    </div> --}}
                                    <div class="col-md-2" hidden>
                                        <div class="form-group">
                                            <label for="acknowledgement_no">Acknowledgement No: </label>
                                            <input type="text" class="form-control" id="acknowledgement_no"
                                                name="acknowledgement_no">
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive mb-0">
                                <table id="example"
                                    class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap table-striped">
                                    <thead>
                                        <tr>
                                            <th>SL No</th>
                                            <th>Acknowledgement No</th>
                                            <th>District/Police Station</th>
                                            <th>Complainant Name & Mobile number</th>
                                            <th>Transaction ID/UTR Number</th>
                                            <th>Bank/Wallet/Merchant</th>
                                            <th>Account ID</th>
                                            <th>Amount</th>
                                            <th>Entry Date</th>
                                            <th>Current Status</th>
                                            <th>Date of Action</th>
                                            <th>Action Taken By</th>
                                            <th>ACTION</th>
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
        <div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="statusModalLabel">Update Case Status</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" data-id="" id="complaint-id">
                        <div class="form-group">
                            <label for="complaint-status">Status:</label>
                            <select id="complaint-status" class="form-control">
                                <option value="Started">Started</option>
                                <option value="Ongoing">Ongoing</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="submitStatus()">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="{{ asset('css/toastr.min.css') }}">
    <style>
        .popup {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            border: 1px solid #ccc;
            padding: 20px;
            background: #fff;
            z-index: 1000;
        }

        .popup-overlay {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
    </style>
    <script src="{{ asset('js/toastr.js') }}"></script>

    @if (session('status'))
        <script>
            toastr.success('{{ session('status') }}', 'Success!')
        </script>
    @endif
    <script>
        function confirmActivation(identifier) {
            var isChecked = $(identifier).prop('checked');
            var confirmationMessage = isChecked ? "Do you want to activate this link?" :
                "Do you want to deactivate this link?";

            if (confirm(confirmationMessage)) {
                activateLink(identifier);
            } else {
                // Revert the checkbox state if the user cancels the action
                $(identifier).prop('checked', !isChecked);
            }
        }
        function upStatus(ackno) {
           // alert($(ackno).data('id'));
            $('#complaint-id').val($(ackno).data('id'));
            $('#statusModal').modal('show');
        }

        function submitStatus() {
            var ackno = $('#complaint-id').val();
            var status = $('#complaint-status').val();

            $.ajax({
                url: '/update-complaint-status',
                type: 'POST',
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: JSON.stringify({
                    ackno: ackno,
                    status: status
                }),
                success: function(response) {
    //alert(response.message);

    // Get the current page number
    var table = $('#example').DataTable();
    var currentPage = table.page();

    // Reload the table data
    table.ajax.reload(function() {
        // Set the page to the previously saved page number
        table.page(currentPage).draw(false);
    });

    // Hide the modal
    $('#statusModal').modal('hide');
},
                error: function(xhr) {
                    alert("Error: " + xhr.responseJSON.message);
                }
            });
        }
        function selfAssign(ackno) {
            //  alert("dsf");
            var user_id = '{{ Auth::user()->id }}';
            var ack_id = $(ackno).data('id');
           // alert(ack_id);
            $.ajax({
                type: "GET",
                dataType: "json",
                url: 'assignedTo',
                data: {
                    'userid': user_id,
                    'acknowledgement_no': ack_id
                },
                success: function(response) {
   // alert(response.message);

    // Get the current page number
    var table = $('#example').DataTable();
    var currentPage = table.page();

    // Reload the table data
    table.ajax.reload(function() {
        // Set the page to the previously saved page number
        table.page(currentPage).draw(false);
    });

    // Hide the modal
    $('#statusModal').modal('hide');
}
            });
        }
        function activateLink(identifier) {
            //  alert("dsf");
            var status = $(identifier).prop('checked') == true ? 1 : 0;
            var ack_id = $(identifier).data('id');
            //  alert(ack_id);
            $.ajax({
                type: "GET",
                dataType: "json",
                url: 'activateLink',
                data: {
                    'status': status,
                    'ack_id': ack_id
                },
                success: function(data) {
                    console.log(data.status)
                    toastr.success(data.status, 'Success!');
                    // $('#example').DataTable().ajax.reload();
                }
            });
        }
        $("#type").on('change', function() {
            var type = document.getElementById("type").value;
            var dropdown = document.getElementById("options");

            dropdown.innerHTML = ""; // Clear previous options

            if (type === "bank") {
                // Assuming `banks` is defined somewhere in your code
                var banks = @json($banks); // Convert the PHP array to JSON

                banks.forEach(function(bank) {
                    var option = document.createElement("option");
                    option.text = bank.name; // Accessing the `name` property of each bank
                    dropdown.add(option);
                });
            }

            if (type === "wallet") {
                // Assuming `walet` is defined somewhere in your code
                var wallets = @json($wallets); // Convert the PHP array to JSON

                wallets.forEach(function(wallet) {
                    var option = document.createElement("option");
                    option.text = wallet.name; // Accessing the `name` property of each bank
                    dropdown.add(option);
                });
            }

            if (type === "merchant") {
                // Assuming `walet` is defined somewhere in your code
                var merchants = @json($merchants); // Convert the PHP array to JSON

                merchants.forEach(function(merchant) {
                    var option = document.createElement("option");
                    option.text = merchant.name; // Accessing the `name` property of each bank
                    dropdown.add(option);
                });
            }

            if (type === "insurance") {
                // Assuming `walet` is defined somewhere in your code
                var insurances = @json($insurances); // Convert the PHP array to JSON

                insurances.forEach(function(insurance) {
                    var option = document.createElement("option");
                    option.text = insurance.name; // Accessing the `name` property of each bank
                    dropdown.add(option);
                });
            }
            // Add similar logic for other types if needed
        });

        $(document).ready(function(){
            var table = $('#example').DataTable({
                processing: true,
                serverSide: true,

                "ajax": {
                    "url": "{{ route('get.datalist') }}",
                    "data": function(d) {
                        return $.extend({}, d, {});
                    }
                },
                columns: [{
                        data: 'id'
                    },

                    {
                        data: 'acknowledgement_no'
                    },
                    {
                        data: 'district'
                    },

                    {
                        data: 'complainant_name'
                    },

                    {
                        data: 'transaction_id'
                    },
                    {
                        data: 'bank_name'
                    },
                    {
                        data: 'account_id'
                    },
                    {
                        data: 'amount'
                    },
                    {
                        data: 'entry_date'
                    },
                    {
                        data: 'current_status'
                    },
                    {
                        data: 'date_of_action'
                    },
                    {
                        data: 'action_taken_by_name'
                    },

                    {
                        data: 'edit'
                    }
                ],
                "order": [0, 'desc'],
                'ordering': true
            });
            $('#example').on('click', '.editable', function() {
                var $editable = $(this);
                var oldValue = $editable.text();
                var ackno = $editable.data('ackno');
                var transaction = $editable.data('transaction');
                var $input = $('<input type="text">').val(oldValue).addClass('edit-input');
                $editable.empty().append($input);
                $input.focus().select();
                $input.on('blur', function() {
                    var newValue = $(this).val();
                    $editable.text(newValue);
                    $.ajax({
                        url: '{{ route('edit.datalist') }}',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        data: {
                            ackno: ackno,
                            transaction: transaction,
                            amount: oldValue,
                            new_amount: newValue
                        },
                        success: function(response) {
                            console.log(response);

                        },
                        error: function(xhr, status, error) {
                            $editable.text(oldData);
                            alert(response.message);
                        }
                    });

                    // Remove input field
                    $(this).remove();
                });

            })

            // Form submission event handler
            $('#complaint-form').submit(function(event) {
                event.preventDefault(); // Prevent default form submission

                var from_date = $("#from-date").val();
                var to_date = $("#to-date").val();
                var mobile = $("#mobile").val();
                var acknowledgement_no = $("#acknowledgement_no").val();
                var filled_by = $("#filled-by").val();
                var search_by = $("#search-by").val();
                var options = $("#options").val();
                var com_status = $("#com_status").val();
                var fir_lodge = $("#fir_lodge").val();
                var filled_by_who = $("#filled_by_who").val();
                var account_id = $("#account-id").val();
                var transaction_id = $("#transaction-id").val();

                // Construct the URL with query parameters
                var url = "{{ route('get.datalist') }}?from_date=" + from_date + "&to_date=" + to_date +
                    "&mobile=" + mobile + "&acknowledgement_no=" + acknowledgement_no + "&filled_by=" +
                    filled_by + "&search_by=" + search_by + "&options=" + options + "&com_status=" +
                    com_status + "&fir_lodge=" + fir_lodge + "&filled_by_who=" + filled_by_who + "&account_id=" +
                    account_id + "&transaction_id=" + transaction_id;

                // Reload DataTable with new data based on selected filters
                table.ajax.url(url).load();
            });

        });

    function showTextBox() {
        var selectedValue = document.getElementById("search-by").value;
        if (selectedValue === "account_id") {
            document.getElementById("account-id-input").style.display = "block";
            document.getElementById("transaction-id-input").style.display = "none";
        } else if (selectedValue === "transaction_id") {
            document.getElementById("transaction-id-input").style.display = "block";
            document.getElementById("account-id-input").style.display = "none";
        } else {
            document.getElementById("account-id-input").style.display = "none";
            document.getElementById("transaction-id-input").style.display = "none";
            document.getElementById("account-id").value = "";
            document.getElementById("transaction-id").value = "";
        }
    }
</script>
@endsection
