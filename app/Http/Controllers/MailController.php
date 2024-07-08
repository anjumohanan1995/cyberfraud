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

    public function mailMergeList($id, $ack_no)
    {
        return view('mailmerge.mailmergeList.mailmergelist', compact('id', 'ack_no'));
    }



    public function getMailmergeListNcrp(Request $request)
    {
        $acknowledgement_no = $request->ack_no;
        $evidence_type_id = $request->website_id;
        // dd($acknowledgement_no);

        // Initialize DataTable variables
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows per page
        $searchValue = $request->get('search')['value']; // Search value

        // Build the query
        $query = Evidence::where('evidence_type_id', $evidence_type_id)
                         ->where('ack_no', $acknowledgement_no);
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
            $editButton = '<div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Notice Type
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="' . route('get-mailmerge-preview', ['id' => $record->evidence_type_id, 'option' => '91crpc_79itact', 'ack_no' => $record->ack_no, 'document_id' => $record->_id,'registrar' => $record->registrar]) . '">Notice U/s 91 CrPC & 79(3)(b) of IT Act</a>
                <a class="dropdown-item" href="' . route('get-mailmerge-preview', ['id' => $record->evidence_type_id, 'option' => '91crpc', 'ack_no' => $record->ack_no, 'document_id' => $record->_id,'registrar' => $record->registrar]) . '">Notice U/s 91 CrPC</a>
                <a class="dropdown-item" href="' . route('get-mailmerge-preview', ['id' => $record->evidence_type_id, 'option' => '79itact', 'ack_no' => $record->ack_no, 'document_id' => $record->_id,'registrar' => $record->registrar]) . '">Notice U/s 79(3)(b) of IT Act</a>
            </div>
        </div>';

            $data_arr[] = [
                "id" => $i,
                "acknowledgement_no" => $record->ack_no,
                "evidence_type" => $record->evidence_type,
                "url" => $record->url,
                "mobile" => $record->mobile,
                "domain" => $record->domain,
                "ip" => $record->ip,
                "registrar" => $record->registrar,
                "registry_details" => $record->registry_details,
                "edit" => $editButton,
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


    public function mailMergeListOther($evidence_type, $case_no)
    {
        return view('mailmerge.mailmergeList.mailmergelistOther', compact('evidence_type', 'case_no'));
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
        $query = ComplaintOthers::where('case_number', $case_no)
                         ->where('evidence_type', $evidence_type);
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
            $editButton = '<div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Notice Type
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="' . route('get-mailmerge-preview', ['evidence_type' => $record->evidence_type, 'option' => '91crpc_79itact', 'case_no' => $record->case_number, 'document_id' => $record->_id,'registrar' => $record->registrar]) . '">Notice U/s 91 CrPC & 79(3)(b) of IT Act</a>
                <a class="dropdown-item" href="' . route('get-mailmerge-preview', ['evidence_type' => $record->evidence_type, 'option' => '91crpc', 'case_no' => $record->case_number, 'document_id' => $record->_id,'registrar' => $record->registrar]) . '">Notice U/s 91 CrPC</a>
                <a class="dropdown-item" href="' . route('get-mailmerge-preview', ['evidence_type' => $record->evidence_type, 'option' => '79itact', 'case_no' => $record->case_number, 'document_id' => $record->_id,'registrar' => $record->registrar]) . '">Notice U/s 79(3)(b) of IT Act</a>
            </div>
        </div>';

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








    public function mailMergePreview(Request $request)
    {

        $evidence_type = $request->evidence_type;
        $case_no = $request->case_no;
        $acknowledgement_no = $request->ack_no;
        $evidence_type_id = $request->id;
        $option = $request->option;
        $document_id = $request->document_id;

        if ($evidence_type && $case_no) {
            $otherData = ComplaintOthers::find(new ObjectId($document_id));
        } elseif($acknowledgement_no && $evidence_type_id){
            $ncrpData = Evidence::find(new ObjectId($document_id));
        }

        // $domain_name = $otherData->domain;
        // dd($domain_name);
        // Initialize variables

    if (isset($ncrpData)) {
        switch ($option) {
            case '91crpc_79itact':
                $sub = "Notice U/s 91 CrPC & 79(3)(b) of IT Act";
                $number = $ncrpData->ack_no;
                $url = $ncrpData->url;
                $domain_name = $ncrpData->domain;
                $domain_id = '123';
                $registrar = $ncrpData->registrar;
                break;
            case '91crpc':
                $sub = "Notice U/s 91 CrPC";
                $number = $ncrpData->ack_no;
                $url = $ncrpData->url;
                $domain_name = $ncrpData->domain;
                $domain_id = '123';
                $registrar = $ncrpData->registrar;
                break;
            case '79itact':
                $sub = "Notice U/s 79(3)(b) of IT Act";
                $number = $ncrpData->ack_no;
                $url = $ncrpData->url;
                $domain_name = $ncrpData->domain;
                $domain_id = '123';
                $registrar = $ncrpData->registrar;
                break;
        }
    }

    if (isset($otherData)) {
        switch ($option) {
            case '91crpc_79itact':
                $sub = "Notice U/s 91 CrPC & 79(3)(b) of IT Act";
                $number = $otherData->case_number;
                $url = $otherData->url;
                $domain_name = $otherData->domain;
                $domain_id = '123';
                $registrar = $otherData->registrar;
                break;
            case '91crpc':
                $sub = "Notice U/s 91 CrPC";
                $number = $otherData->case_number;
                $url = $otherData->url;
                $domain_name = $otherData->domain;
                $domain_id = '123';
                $registrar = $otherData->registrar;
                break;
            case '79itact':
                $sub = "Notice U/s 79(3)(b) of IT Act";
                $number = $otherData->case_number;
                $url = $otherData->url;
                $domain_name = $otherData->domain;
                $domain_id = '123';
                $registrar = $otherData->registrar;
                break;
        }
    }

    dd();


        // Determine the view based on $option
        $viewName = 'mailmerge.' . $option . 'Preview';

        // Check if the view exists, otherwise fallback to a default view
        if (view()->exists($viewName)) {
            return view($viewName, compact('sub', 'number', 'url', 'domain_name', 'domain_id','registrar','evidence_type','case_no','acknowledgement_no','evidence_type_id'));
        }
    }


    public function sendEmail(Request $request)
    {
        $registrarName = $request->input('registrar');
        $evidence_type = $request->input('evidence_type');
        $case_no = $request->input('case_no');
        $ack_no = $request->input('acknowledgement_no');
        $id = $request->input('evidence_type_id');
        // dd($registrarName);
        $documents = Registrar::where('registrar', $registrarName)->get();

            // Check if documents were found for the given registrar
            if ($documents->isEmpty()) {
                if ($evidence_type && $case_no) {
                    return view('mailmerge.mailmergeList.mailmergelistOther', compact('evidence_type', 'case_no'))
                        ->withErrors(['error' => 'No documents found for the selected registrar.']);
                } elseif ($ack_no && $id) {
                    return view('mailmerge.mailmergeList.mailmergelist', compact('id', 'ack_no'))
                        ->withErrors(['error' => 'No documents found for the selected registrar.']);
                }
            }
        // dd($documents);
        $recipientEmails = []; // Initialize an empty array to store email addresses

        foreach ($documents as $document) {
            $emailIds = $document->email_id;

                // Check if $emailIds is empty
        if (empty($emailIds)) {
            if ($evidence_type && $case_no) {
                return view('mailmerge.mailmergeList.mailmergelistOther', compact('evidence_type', 'case_no'))
                    ->withErrors(['error' => 'No email addresses found for the selected registrar.']);
            } elseif ($ack_no && $id) {
                return view('mailmerge.mailmergeList.mailmergelist', compact('id', 'ack_no'))
                    ->withErrors(['error' => 'No email addresses found for the selected registrar.']);
            }
        }

            // Add each email in $emailIds to $demoEmails array
            foreach ($emailIds as $email) {
                $recipientEmails[] = $email;
            }
        }

        // Merge demo emails with any provided emails from the form
        $emails = array_merge($recipientEmails, explode(',', $request->input('emails')));

        // Extract content and subject from the form submission
        $sub = $request->input('sub');
        $number = $request->input('number');
        $url = $request->input('url');
        $domain_name = $request->input('domain_name');
        $domain_id = $request->input('domain_id');
        // dd($sub);

      // Send email to each recipient
      foreach ($emails as $email) {
        // Validate email format before sending (optional but recommended)
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

            // ---------------FOR MULTIPLE MAIL INTERATION CODE---------

            // // Example for 'aravind' mailer
            // $mailerName = 'aravind';
            // Mail::mailer($mailerName)->to($email)->send(new MailMergePreview($sub, $number, $url, $domain_name, $domain_id, $mailerName));

            // // Example for 'rajmohan' mailer
            // $mailerName = 'rajmohan';
            // Mail::mailer($mailerName)->to($email)->send(new MailMergePreview($sub, $number, $url, $domain_name, $domain_id, $mailerName));

            // // // Example for 'sreejith' mailer
            // $mailerName = 'sreejith';
            // Mail::mailer($mailerName)->to($email)->send(new MailMergePreview($sub, $number, $url, $domain_name, $domain_id, $mailerName));

            // ---------------FOR MULTIPLE MAIL INTERATION CODE END---------


            Mail::to($email)->send(new MailMergePreview($sub, $number, $url, $domain_name, $domain_id));
        } else {
            // Handle invalid email address (log or skip)
            continue;
        }
    }
    // Set success message in session
    Session::flash('success', 'Emails sent successfully.');

    // Return the appropriate view with success message
    if ($evidence_type && $case_no) {
        return view('mailmerge.mailmergeList.mailmergelistOther', compact('evidence_type', 'case_no'));
    } elseif ($ack_no && $id) {
        return view('mailmerge.mailmergeList.mailmergelist', compact('id', 'ack_no'));
    }
}
}
