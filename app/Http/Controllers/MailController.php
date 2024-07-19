<?php

namespace App\Http\Controllers;

use App\Mail\MailMergePreview;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\Evidence;
use App\Models\Registrar;
use App\Models\EvidenceType;
use App\Models\ComplaintOthers;
use MongoDB\BSON\ObjectId;
use Illuminate\Support\Facades\Session;

class MailController extends Controller
{

    public function mailMergeList($ack_no)
    {
        $website = Evidence::where('ack_no', $ack_no)
        ->where('evidence_type', 'website')
        ->get();
        return view('mailmerge.mailmergeList.mailmergelist', compact('website', 'ack_no'));
    }



    public function getMailmergeListNcrp(Request $request)
    {
        $acknowledgement_no = $request->ack_no;
        // $evidence_type_id = $request->website_id;
        // dd($evidence_type_id);
        // dd($acknowledgement_no);

        // Initialize DataTable variables
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows per page
        $searchValue = $request->get('search')['value']; // Search value

        // Build the query
        $query = Evidence::where('ack_no', $acknowledgement_no);
                        //  dd($query);

        // Apply search filter
        if (!empty($searchValue)) {
            $query = $query->where(function($q) use ($searchValue) {
                $q->where('url', 'like', '%'.$searchValue.'%')
                  ->orWhere('domain', 'like', '%'.$searchValue.'%')
                  ->orWhere('ip', 'like', '%'.$searchValue.'%')
                  ->orWhere('registrar', 'like', '%'.$searchValue.'%')
                  ->orWhere('registry_details', 'like', '%'.$searchValue.'%')
                  ->orWhere('mobile', 'like', '%'.$searchValue.'%');
            });
        }

        // Total records count
        $totalRecords = $query->count();

        // Get paginated data
        $records = $query->skip($start)->take($rowperpage)->get();
        // dd($records);

        // Format data for DataTable
        $data_arr = [];
        $i = $start;

        foreach ($records as $record) {
            $i++;


            $editButton = $this->generateEditButton($record);


        $mailStatus = '';
        if($record->evidence_type == 'website'){
            $mailStatus = '
            <div>
                <span class="badge badge-info">Reported-M- ' . $record->mail_status_count . '</span>
            </div><br>
              <div>
                <span class="badge badge-info">Reported-P- ' . $record->portal_status_count . '</span>
            </div>';
        }



        $status = '';

        $statusOptions = [
            ['value' => "reported", 'label' => 'Reported', 'class' => 'badge-success'],
            ['value' => "active", 'label' => 'Active', 'class' => 'badge-primary'],
            ['value' => "inactive", 'label' => 'Inactive', 'class' => 'badge-secondary'],
        ];

        foreach ($statusOptions as $option) {
            $checked = ($record->reported_status == $option['value']) ? 'checked' : '';
            $status .= '
            <div class="form-check form-check">
                <input
                    type="radio"
                    id="statusRadio_' . $record->_id . '_' . $option['value'] . '"
                    name="statusRadio_' . $record->_id . '"
                    class="form-check-input status-radio"
                    value="' . $option['value'] . '"
                    ' . $checked . '
                    data-id="' . $record->_id . '"
                    onchange="toggleReportStatus(this)">
                <label class="form-check-label badge ' . $option['class'] . '" for="statusRadio_' . $record->_id . '_' . $option['value'] . '">
                    ' . $option['label'] . '
                </label>
            </div>';
        }


            $data_arr[] = [
                "id" => $i,
                // "acknowledgement_no" => $record->ack_no,
                "evidence_type" => $record->evidence_type,
                "url" => $record->url,
                "mobile" => $record->mobile,
                "domain" => $record->domain,
                "ip" => $record->ip,
                "registrar" => $record->registrar,
                "registry_details" => $record->registry_details,
                "portal_link" => $editButton,
                "mail_status" => $mailStatus,
                "status" => $status,
            ];
        }


        // Prepare response for DataTable
        $response = [
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data_arr
        ];

        return response()->json($response);
    }

    private function generateEditButton($record)
{
    if ($record->evidence_type == 'website') {
        // Generate edit button HTML with tooltip and icon
        // dd($record->portal_status_count);
        $editButton = '
        <div class="d-flex align-items-center">
            <div>
                <a class="btn btn-primary" href="' . route('get-portal-link', ['registrar' => $record->registrar]) . '">
                    <small>
                        <i class="fas fa-link" data-toggle="tooltip" data-placement="top" title="Portal Link"></i>
                    </small>
                </a>
            </div>
        <div style="margin-left: 10px;">
            <button class="btn btn-success" onclick="showPortalModal(\'' . $record->_id . '\', \'' . $record->reported_status . '\')">
                <i class="fas fa-list" data-toggle="tooltip" data-placement="top" title="Portal Status Count"></i>
            </button>
        </div>
        </div>
    ';
    } else {
        $editButton = ''; // If not a website, return empty button
    }

    return $editButton;
}


    public function getPortalLink($registrar)
    {
        // Fetch the portal link based on the registrar
        $portal = Registrar::where('registrar', $registrar)->first();

        if ($portal) {
            // Redirect to the portal_link if found
            return redirect()->away($portal->portal_link);
        } else {
            // Handle case where registrar is not found
            abort(404, 'Registrar not found');
        }
    }

    public function updatePortalCount(Request $request)
    {
        // Retrieve the input values from the request
        $portalCount = $request->input('portalCount');
        $case_no = $request->input('case_no');
        $portalstatusType = $request->input('portalstatusType');
        $registrarId = $request->input('registrarId');
        $caseData = $request->input('caseData');
        // dd($registrarId);

        $data = collect();

        // Retrieve data based on $caseData type
        if ($caseData == "ncrp") {
            $data = Evidence::where('_id', $registrarId)
                            ->where('evidence_type', 'website')
                            ->where('reported_status', $portalstatusType)
                            ->first();
        } elseif ($caseData == "other") {
            $data = ComplaintOthers::where('_id', $registrarId)
                                   ->where('evidence_type', 'website')
                                   ->where('reported_status', $portalstatusType)
                                   ->first();
        }

        if (!$data) {
            return response()->json(['error' => 'There is no data available for corresponding status type!'], 400);
        }

        // Check the value of portalstatusType
        if (in_array($portalstatusType, ['active', 'inactive'])) {
            // Update reported_status to 'reported'
            $data->reported_status = 'reported';
        }

        // Update portal_status_count
        $data->portal_status_count = $portalCount;
// dd($data);
        // Save the changes to the database
        $data->save();

        return response()->json(['success' => 'Portal count and status updated successfully']);
    }


    public function mailMergeListOther($case_no)
    {
        $website = ComplaintOthers::where('case_number', $case_no)
        ->where('evidence_type', 'website')
        ->get();
        return view('mailmerge.mailmergeList.mailmergelistOther', compact('website', 'case_no'));
    }

    public function getMailmergeListOther(Request $request)
    {
        $evidence_type = $request->evidence_type;
        $case_no = $request->case_no;
        // dd($case_no);

                // dd($acknowledgement_no);

        // Initialize DataTable variables
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows per page
        $searchValue = $request->get('search')['value']; // Search value

        // Build the query
        $query = ComplaintOthers::where('case_number', $case_no);
                        //  dd($query);

        // Apply search filter
        if (!empty($searchValue)) {
            $query = $query->where(function($q) use ($searchValue) {
                $q->where('url', 'like', '%'.$searchValue.'%')
                  ->orWhere('domain', 'like', '%'.$searchValue.'%')
                  ->orWhere('ip', 'like', '%'.$searchValue.'%')
                  ->orWhere('registrar', 'like', '%'.$searchValue.'%')
                  ->orWhere('registry_details', 'like', '%'.$searchValue.'%')
                  ->orWhere('mobile', 'like', '%'.$searchValue.'%');
            });
        }

        // Total records count
        $totalRecords = $query->count();

        // Get paginated data
        $records = $query->skip($start)->take($rowperpage)->get();
        // dd($records);

        // Format data for DataTable
        $data_arr = [];
        $i = $start;

        foreach ($records as $record) {
            // dd($record->_id);
            $i++;


            $editButton = $this->generateEditButton($record);

            $mailStatus = '';
            if($record->evidence_type == 'website'){
                $mailStatus = '
                <div>
                    <span class="badge badge-info">Reported-M- ' . $record->mail_status_count . '</span>
                </div><br>
              <div>
                <span class="badge badge-info">Reported-P- ' . $record->portal_status_count . '</span>
            </div>';
            }

        $status = '';

        $statusOptions = [
            ['value' => "reported", 'label' => 'Reported', 'class' => 'badge-success'],
            ['value' => "active", 'label' => 'Active', 'class' => 'badge-primary'],
            ['value' => "inactive", 'label' => 'Inactive', 'class' => 'badge-secondary'],
        ];

        foreach ($statusOptions as $option) {
            $checked = ($record->reported_status == $option['value']) ? 'checked' : '';
            $status .= '
            <div class="form-check form-check">
                <input
                    type="radio"
                    id="statusRadio_' . $record->_id . '_' . $option['value'] . '"
                    name="statusRadio_' . $record->_id . '"
                    class="form-check-input status-radio"
                    value="' . $option['value'] . '"
                    ' . $checked . '
                    data-id="' . $record->_id . '"
                    onchange="toggleReportStatuOther(this)">
                <label class="form-check-label badge ' . $option['class'] . '" for="statusRadio_' . $record->_id . '_' . $option['value'] . '">
                    ' . $option['label'] . '
                </label>
            </div>';
        }

            $data_arr[] = [

                "id" => $i,
                "case_number" => $record->case_number,
                "evidence_type" => $record->evidence_type,
                "url" => $record->url,
                "domain" => $record->domain,
                "ip" => $record->ip,
                "registrar" => $record->registrar,
                "registry_details" => $record->registry_details,
                "portal_link" => $editButton,
                "mail_status" => $mailStatus,
                "status" => $status,
            ];
        }

        // Prepare response for DataTable
        $response = [
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data_arr
        ];

        return response()->json($response);


    }

    public function updatePortalCountother(Request $request)
    {
        // Validate the request
        $request->validate([
            'portalCount' => 'required|integer',
            'ack_no' => 'required',
        ]);

        // Get the portal count and ack_no from the request
        $portalCount = $request->input('portalCount');
        $ack_no = $request->input('ack_no');

        // Update the Evidence model where conditions match
        ComplaintOthers::where('ack_no', $ack_no)
                ->where('evidence_type', 'website')
                ->update(['portal_status_count' => $portalCount]);

        return response()->json(['success' => 'Portal count updated successfully!']);
    }

    public function sendEmail(Request $request)
    {
        // Retrieve data from the request
        $statusType = $request->statusType;
        $noticeType = $request->noticeType;
        $ack_no = $request->ack_no;
        $case_no = $request->case_no;
        $caseData = $request->caseData;

             // Example validation
    if (!$statusType || !$noticeType) {
        return response()->json(['error' => 'Please select both Status Type and Notice Type!'], 400);
    }

        // Initialize arrays to hold data and notices
        $noticeData = [];
        $data = collect();

        // Retrieve data based on $caseData type
        if ($caseData == "ncrp") {
            $data = Evidence::where('ack_no', $ack_no)
                            ->where('evidence_type', 'website')
                            ->where('reported_status', $statusType)
                            ->get();
        } elseif ($caseData == "other") {
            $data = ComplaintOthers::where('case_number', $case_no)
                                   ->where('evidence_type', 'website')
                                   ->where('reported_status', $statusType)
                                   ->get();
        }

        if ($data->isEmpty()) {
            return response()->json(['error' => 'There is no data available for corresponding status type!'], 400);
        }
        // dd($data);

        // Initialize arrays to hold registrars and documents
        $registrars = [];
        $documents = [];

        // Extract unique registrars from $data
        $registrars = $data->pluck('registrar')->unique()->toArray();

        // Fetch documents for each registrar
        $documents = Registrar::whereIn('registrar', $registrars)->get();

        // Check if documents were found for the given registrar
        if ($documents->isEmpty()) {
            return response()->json(['error' => 'No documents found for the selected registrar!'], 400);
        }

        // Prepare notice data based on notice type
        foreach ($data as $item) {
            // Handle 'For All Notice Type' case
            if ($noticeType == 'For All Notice Type') {
                $noticeData[] = [
                    'sub' => "Notice U/s 91 CrPC & 79(3)(b) of IT Act",
                    'number' => $caseData == "ncrp" ? $item->ack_no : $item->case_number,
                    'url' => $item->url,
                    'domain_name' => $item->domain,
                    'domain_id' => $item->registry_details,
                    'registrar' => $item->registrar,
                ];

                $noticeData[] = [
                    'sub' => "Notice U/s 91 CrPC",
                    'number' => $caseData == "ncrp" ? $item->ack_no : $item->case_number,
                    'url' => $item->url,
                    'domain_name' => $item->domain,
                    'domain_id' => $item->registry_details,
                    'registrar' => $item->registrar,
                ];

                $noticeData[] = [
                    'sub' => "Notice U/s 79(3)(b) of IT Act",
                    'number' => $caseData == "ncrp" ? $item->ack_no : $item->case_number,
                    'url' => $item->url,
                    'domain_name' => $item->domain,
                    'domain_id' => $item->registry_details,
                    'registrar' => $item->registrar,
                ];
            } else {
                // Handle other notice types individually
                switch ($noticeType) {
                    case 'Notice U/s 91 CrPC & 79(3)(b) of IT Act':
                        $noticeData[] = [
                            'sub' => "Notice U/s 91 CrPC & 79(3)(b) of IT Act",
                            'number' => $caseData == "ncrp" ? $item->ack_no : $item->case_number,
                            'url' => $item->url,
                            'domain_name' => $item->domain,
                            'domain_id' => $item->registry_details,
                            'registrar' => $item->registrar,
                        ];
                        break;

                    case 'Notice U/s 91 CrPC':
                        $noticeData[] = [
                            'sub' => "Notice U/s 91 CrPC",
                            'number' => $caseData == "ncrp" ? $item->ack_no : $item->case_number,
                            'url' => $item->url,
                            'domain_name' => $item->domain,
                            'domain_id' => $item->registry_details,
                            'registrar' => $item->registrar,
                        ];
                        break;

                    case 'Notice U/s 79(3)(b) of IT Act':
                        $noticeData[] = [
                            'sub' => "Notice U/s 79(3)(b) of IT Act",
                            'number' => $caseData == "ncrp" ? $item->ack_no : $item->case_number,
                            'url' => $item->url,
                            'domain_name' => $item->domain,
                            'domain_id' => $item->registry_details,
                            'registrar' => $item->registrar,
                        ];
                        break;
                }
            }
        }
// dd("ghfjh");
        // Initialize an array to store recipient emails
        $recipientEmails = [];

        // Extract email addresses from documents
        foreach ($documents as $document) {
            $emailIds = $document->email_id;

            // Check if $emailIds is empty
            if (empty($emailIds)) {
                return response()->json(['error' => 'No email addresses found for the selected registrar!'], 400);
            }

            // Add each email in $emailIds to $recipientEmails array
            foreach ($emailIds as $email) {
                $recipientEmails[] = $email;
            }
        }

        // Merge demo emails with any provided emails from the form
        $emails = array_merge($recipientEmails, explode(',', $request->input('emails')));

        // Send email to each recipient
        foreach ($emails as $email) {
            // Fetch the registrar data where the email exists in the email_id array
            $registrarData = Registrar::where('email_id', 'all', [$email])->get();

            foreach ($registrarData as $registrar) {
                // Check if $email exists in the $registrar->email_id array
                if (in_array($email, $registrar->email_id)) {
                    // Print the notice related to this registrar
                    foreach ($noticeData as $notice) {
                        if ($registrar->registrar == $notice['registrar']) {
                            // Print or process $notice
                            Mail::to($email)->send(new MailMergePreview([$notice]));
                        }
                    }
                }
            }
        }
        // dd($caseData);

        // Update models based on $caseData and $statusType
        if ($caseData == "ncrp") {
            if ($statusType == 'active' || $statusType == 'inactive') {
                $this->updateEvidenceModel('reported', 1, $ack_no, $statusType);
            } elseif ($statusType == 'reported') {
                $this->incrementEvidenceMailStatusCount($ack_no, $statusType);
            }
        } elseif ($caseData == "other") {
            if ($statusType == 'active' || $statusType == 'inactive') {
                $this->updateComplaintOthersModel('reported', 1, $case_no, $statusType);
            } elseif ($statusType == 'reported') {
                $this->incrementComplaintOthersMailStatusCount($case_no, $statusType);
            }
        }
        return response()->json(['success' => 'Email sent successfully.']);
    }


private function updateEvidenceModel($reportedStatsValue, $mailStatusCountValue, $ack_no, $statusType)
{
    Evidence::where('evidence_type', 'website')->where('ack_no', $ack_no)->where('reported_status', $statusType) // Add your condition here
        ->update([
            'reported_status' => $reportedStatsValue,
            'mail_status_count' => $mailStatusCountValue
        ]);
}

private function incrementEvidenceMailStatusCount($ack_no, $statusType)
{
    Evidence::where('evidence_type', 'website')->where('ack_no', $ack_no)->where('reported_status', $statusType) // Add your condition here
        ->increment('mail_status_count');
}

private function updateComplaintOthersModel($reportedStatsValue, $mailStatusCountValue, $case_no, $statusType)
{
    ComplaintOthers::where('evidence_type', 'website')->where('case_number', $case_no)->where('reported_status', $statusType) // Add your condition here
        ->update([
            'reported_status' => $reportedStatsValue,
            'mail_status_count' => $mailStatusCountValue
        ]);
}

private function incrementComplaintOthersMailStatusCount($case_no, $statusType)
{
    ComplaintOthers::where('evidence_type', 'website')->where('case_number', $case_no)->where('reported_status', $statusType) // Add your condition here
        ->increment('mail_status_count');
}

}

