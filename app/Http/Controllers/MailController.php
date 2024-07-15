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

            $editButton = "Portal link";

        //     $editButton = '<div class="dropdown">
        //     <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        //         Portal Link
        //     </button>
        //     <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        //         <a class="dropdown-item" href="' . route('get-mailmerge-preview', ['id' => $record->evidence_type_id, 'option' => '91crpc_79itact', 'ack_no' => $record->ack_no, 'document_id' => $record->_id,'registrar' => $record->registrar]) . '">Notice U/s 91 CrPC & 79(3)(b) of IT Act</a>
        //         <a class="dropdown-item" href="' . route('get-mailmerge-preview', ['id' => $record->evidence_type_id, 'option' => '91crpc', 'ack_no' => $record->ack_no, 'document_id' => $record->_id,'registrar' => $record->registrar]) . '">Notice U/s 91 CrPC</a>
        //         <a class="dropdown-item" href="' . route('get-mailmerge-preview', ['id' => $record->evidence_type_id, 'option' => '79itact', 'ack_no' => $record->ack_no, 'document_id' => $record->_id,'registrar' => $record->registrar]) . '">Notice U/s 79(3)(b) of IT Act</a>
        //     </div>
        // </div>';

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
                "edit" => $editButton,
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
            $editButton = "Portal link";
        //     $editButton = '<div class="dropdown">
        //     <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        //         Portal link
        //     </button>
        //     <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        //         <a class="dropdown-item" href="' . route('get-mailmerge-preview', ['evidence_type' => $record->evidence_type, 'option' => '91crpc_79itact', 'case_no' => $record->case_number, 'document_id' => $record->_id,'registrar' => $record->registrar]) . '">Notice U/s 91 CrPC & 79(3)(b) of IT Act</a>
        //         <a class="dropdown-item" href="' . route('get-mailmerge-preview', ['evidence_type' => $record->evidence_type, 'option' => '91crpc', 'case_no' => $record->case_number, 'document_id' => $record->_id,'registrar' => $record->registrar]) . '">Notice U/s 91 CrPC</a>
        //         <a class="dropdown-item" href="' . route('get-mailmerge-preview', ['evidence_type' => $record->evidence_type, 'option' => '79itact', 'case_no' => $record->case_number, 'document_id' => $record->_id,'registrar' => $record->registrar]) . '">Notice U/s 79(3)(b) of IT Act</a>
        //     </div>
        // </div>';

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
                "edit" => $editButton,
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








    // public function mailMergePreview(Request $request)
    // {

    //     $evidence_type = $request->evidence_type;
    //     $case_no = $request->case_no;
    //     $acknowledgement_no = $request->ack_no;
    //     $evidence_type_id = $request->id;
    //     $option = $request->option;
    //     $document_id = $request->document_id;

    //     if ($evidence_type && $case_no) {
    //         $otherData = ComplaintOthers::find(new ObjectId($document_id));
    //     } elseif($acknowledgement_no && $evidence_type_id){
    //         $ncrpData = Evidence::find(new ObjectId($document_id));
    //     }

    //     // $domain_name = $otherData->domain;
    //     // dd($domain_name);
    //     // Initialize variables

    // if (isset($ncrpData)) {
    //     switch ($option) {
    //         case '91crpc_79itact':
    //             $sub = "Notice U/s 91 CrPC & 79(3)(b) of IT Act";
    //             $number = $ncrpData->ack_no;
    //             $url = $ncrpData->url;
    //             $domain_name = $ncrpData->domain;
    //             $domain_id = '123';
    //             $registrar = $ncrpData->registrar;
    //             break;
    //         case '91crpc':
    //             $sub = "Notice U/s 91 CrPC";
    //             $number = $ncrpData->ack_no;
    //             $url = $ncrpData->url;
    //             $domain_name = $ncrpData->domain;
    //             $domain_id = '123';
    //             $registrar = $ncrpData->registrar;
    //             break;
    //         case '79itact':
    //             $sub = "Notice U/s 79(3)(b) of IT Act";
    //             $number = $ncrpData->ack_no;
    //             $url = $ncrpData->url;
    //             $domain_name = $ncrpData->domain;
    //             $domain_id = '123';
    //             $registrar = $ncrpData->registrar;
    //             break;
    //     }
    // }

    // if (isset($otherData)) {
    //     switch ($option) {
    //         case '91crpc_79itact':
    //             $sub = "Notice U/s 91 CrPC & 79(3)(b) of IT Act";
    //             $number = $otherData->case_number;
    //             $url = $otherData->url;
    //             $domain_name = $otherData->domain;
    //             $domain_id = '123';
    //             $registrar = $otherData->registrar;
    //             break;
    //         case '91crpc':
    //             $sub = "Notice U/s 91 CrPC";
    //             $number = $otherData->case_number;
    //             $url = $otherData->url;
    //             $domain_name = $otherData->domain;
    //             $domain_id = '123';
    //             $registrar = $otherData->registrar;
    //             break;
    //         case '79itact':
    //             $sub = "Notice U/s 79(3)(b) of IT Act";
    //             $number = $otherData->case_number;
    //             $url = $otherData->url;
    //             $domain_name = $otherData->domain;
    //             $domain_id = '123';
    //             $registrar = $otherData->registrar;
    //             break;
    //     }
    // }


    //     // Determine the view based on $option
    //     $viewName = 'mailmerge.' . $option . 'Preview';

    //     // Check if the view exists, otherwise fallback to a default view
    //     if (view()->exists($viewName)) {
    //         return view($viewName, compact('sub', 'number', 'url', 'domain_name', 'domain_id','registrar','evidence_type','case_no','acknowledgement_no','evidence_type_id'));
    //     }
    // }


    public function sendEmail(Request $request)
    {
        // Retrieve data from the request
        $statusType = $request->statusType;
        $noticeType = $request->noticeType;
        $ack_no = $request->ack_no;
        $case_no = $request->case_no;
        $caseData = $request->caseData;

        // Initialize arrays to hold data and notices
        $noticeData = [];
        $data = [];

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

        // Check if data is empty
        if ($data->isEmpty()) {
            return redirect()->back()->withErrors(['message' => 'There is no data available for corresponding status type']);
        }

        // Initialize arrays to hold registrars and documents
        $registrars = [];
        $documents = [];

        // Extract unique registrars from $data
        $registrars = $data->pluck('registrar')->unique()->toArray();
        // dd($registrars);

        // Fetch documents for each registrar
        $documents = Registrar::whereIn('registrar', $registrars)->get();
        // dd($documents);

        // Check if documents were found for the given registrar
        if ($documents->isEmpty()) {
            return redirect()->back()->withErrors(['message' => 'No documents found for the selected registrar.']);
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

        // dd($noticeData);

        // Initialize an array to store recipient emails
        $recipientEmails = [];

        // Extract email addresses from documents
        foreach ($documents as $document) {
            $emailIds = $document->email_id;
            // print_r($emailIds);

            // Check if $emailIds is empty
            if (empty($emailIds)) {
                return redirect()->back()->withErrors(['message' => 'No email addresses found for the selected registrar.']);
            }

            // Add each email in $emailIds to $recipientEmails array
            foreach ($emailIds as $email) {
                $recipientEmails[] = $email;
            }
        }

        // Merge demo emails with any provided emails from the form
        $emails = array_merge($recipientEmails, explode(',', $request->input('emails')));
        // dd($emails);

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
                // print_r($email);
                // print_r($notice);
                // Uncomment the line below to send the email
                Mail::to($email)->send(new MailMergePreview([$notice]));
                }
            }
            // Uncomment the line below to send the email
            // Mail::to($email)->send(new MailMergePreview([$notice]));
        }
    }
}
            // // Print the notice related to this registrar
            // if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            //     print_r($notice);
            //     // Uncomment the line below to send the email
            //     // Mail::to($email)->send(new MailMergePreview([$notice]));
            //     }
            // }

    // Debug message to confirm the function has executed
    // dd("hello");
    return redirect()->back()->with('status', 'Emails sent successfully.');

    }



}
