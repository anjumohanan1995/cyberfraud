<?php

namespace App\Http\Controllers;

use App\Mail\MailMergePreview;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\Evidence;
use App\Models\Registrar;
use App\Models\EvidenceType;
use App\Models\ComplaintOthers;

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
            $editButton = '<div class="dropdown" hidden>
            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Edit Options
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="' . route('get-mailmerge-preview', ['id' => $record->evidence_type_id, 'option' => '91crpc_79itact', 'ack_no' => $record->ack_no]) . '">Notice U/s 91 CrPC & 79(3)(b) of IT Act</a>
                <a class="dropdown-item" href="' . route('get-mailmerge-preview', ['id' => $record->evidence_type_id, 'option' => '91crpc', 'ack_no' => $record->ack_no]) . '">Notice U/s 91 CrPC</a>
                <a class="dropdown-item" href="' . route('get-mailmerge-preview', ['id' => $record->evidence_type_id, 'option' => '79itact', 'ack_no' => $record->ack_no]) . '">Notice U/s 79(3)(b) of IT Act</a>
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
            $i++;
            $editButton = '<div class="dropdown" hidden>
            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Edit Options
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="' . route('get-mailmerge-preview', ['evidence_type' => $record->evidence_type, 'option' => '91crpc_79itact', 'case_no' => $record->case_number]) . '">Notice U/s 91 CrPC & 79(3)(b) of IT Act</a>
                <a class="dropdown-item" href="' . route('get-mailmerge-preview', ['evidence_type' => $record->evidence_type, 'option' => '91crpc', 'case_no' => $record->case_number]) . '">Notice U/s 91 CrPC</a>
                <a class="dropdown-item" href="' . route('get-mailmerge-preview', ['evidence_type' => $record->evidence_type, 'option' => '79itact', 'case_no' => $record->case_number]) . '">Notice U/s 79(3)(b) of IT Act</a>
            </div>
        </div>';

            $data_arr[] = [

                "id" => $i,
                "case_number" => $record->case_no,
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
        dd($option);

        // if ($evidence_type && $case_no){
        //     $data = ComplaintOthers::
        // }
// dd($id);
    // Check if $evidence_name is 'website', otherwise show an error
    // if ($evidence_name !== 'website') {
    //     return redirect()->back()->with('error', 'Please select evidence type "website" or the selected evidence type is not "website".');

        $evidence = Evidence::where('evidence_type_id', $id)->where('ack_no', $ack_no)->get();
dd($evidence);
        if (!$evidence) {
            abort(404, 'Evidence not found'); // Handle the case where evidence with $id is not found
        }

        // Initialize variables
        $mongo_id = $id;
        $sub = '';
        $salutation = '';
        $compiledContent = '';

        switch ($option) {
            case '91crpc_79itact':
                $sub = "Notice U/s 91 CrPC & 79(3)(b) of IT Act";
                $salutation = "Team Register name";
                $detailsRequired = "\xE2\x9C\xB1Details Required:\xE2\x9C\xB1
                1. Registration details including:
                        a) Email ID
                        b) Mobile phone numbers
                        c) IP address with Date and Time
                        d) Mode of payment details for registration
                2. Any other subdomains with the above registration email ID or mobile number.
                3. Registration details as mentioned in (1) for domain identified under (2).

                Urgent action and confirmation is solicited by return.";

                // Generate content for each evidence
                $content = "Content :

                A complaint in \xE2\x9C\xB1NO: {$evidence->ack_no}\xE2\x9C\xB1 is reported at National Cyber Crime Reporting Portal (NCRP) for financial fraud in which an Unlawful Website with the URL:{$evidence->url} is involved and it is found that the website is hosted in your registry for propagating cyber fraud. Hence it is directed to provide the details of the below mentioned website by return and also directed to disable the Website within 48 Hrs in order to prevent further Cyber fraud and to ensure the protection of potential victims.

                             	          As an Intermediary if you fails to remove or disable the Unlawful website the protection U/s 79 of IT Act will not be applicable and you will be liable for abetment              

                \xE2\x9C\xB1Alleged Website Details:\xE2\x9C\xB1
                URL:  {$evidence->url}
                Domain Name:  {$evidence->domain}
                Registry Domain ID:  {$evidence->domain}
                ";

                $compiledContent = $content . "\n\n" . $detailsRequired;
                break;
            case '91crpc':
                $sub = "Notice U/s 91 CrPC";
                $salutation = "Team Register name";
                $detailsRequired = "\xE2\x9C\xB1Details Required:\xE2\x9C\xB1
                1. Registration details including:
                        a) Email ID
                        b) Mobile phone numbers
                        c) IP address with Date and Time
                        d) Mode of payment details for registration
                2. Any other subdomains with the above registration email ID or mobile number.
                3. Registration details as mentioned in (1) for domain identified under (2).

                Urgent action and confirmation is solicited by return.";

                // Generate content for each evidence
                $content = "Content :

                A complaint in \xE2\x9C\xB1NO: {$evidence->ack_no}\xE2\x9C\xB1 is reported at National Cyber Crime Reporting Portal (NCRP) for financial fraud in which an Unlawful Website with the URL:{$evidence->url} is involved and it is found that the website is hosted in your registry for propagating cyber fraud. Hence it is directed to provide the details of the below mentioned website by return.

                \xE2\x9C\xB1Alleged Website Details:\xE2\x9C\xB1
                URL:  {$evidence->url}
                Domain Name:  {$evidence->domain}
                Registry Domain ID:  {$evidence->domain}
                ";

                $compiledContent = $content . "\n\n" . $detailsRequired;
                break;
            case '79itact':
                $sub = "Notice U/s 79(3)(b) of IT Act";
                $salutation = "Team Register name";
                $detailsRequired = "Urgent action and confirmation is solicited by return";

                // Generate content for each evidence
                $content = "Content :

                A complaint in \xE2\x9C\xB1NO: {$evidence->ack_no}\xE2\x9C\xB1 is reported at National Cyber Crime Reporting Portal (NCRP) for financial fraud in which an Unlawful Website with the URL:{$evidence->url} is involved and it is found that the website is hosted in your registry for propagating cyber fraud. Hence it is directed to disable the Website within 48 Hrs in order to prevent further Cyber fraud and to ensure the protection of potential victims.

                             	                       As an Intermediary if you fail to remove or disable the Unlawful website the protection U/s 79 of IT Act will not be applicable and you will be liable for abetment              

                \xE2\x9C\xB1Alleged Website Details:\xE2\x9C\xB1
                URL:  {$evidence->url}
                Domain Name:  {$evidence->domain}
                Registry Domain ID:  {$evidence->domain}
                ";

                $compiledContent = $content . "\n\n" . $detailsRequired;
                break;
            default:
                $sub = "Default Subject";
                $content = "Default content";
                break;
        }

        // Determine the view based on $option
        $viewName = 'mailmerge.' . $option . 'Preview';

        // Check if the view exists, otherwise fallback to a default view
        if (view()->exists($viewName)) {
            return view($viewName, compact('sub', 'salutation', 'compiledContent', 'mongo_id'));
        } else {
            // Fallback to a generic preview view or handle as per your application logic
            return view('mailmerge.defaultPreview', compact('sub', 'salutation', 'compiledContent'));
        }
    }


    // public function mailMergePreviewOther($evidence_type, $option, $case_no)
    // {
    //     dd($evidence_type);

    // }

    public function sendEmail(Request $request)
    {
        $mongo_id = $request->input('mongo_id');
        $evidence = Evidence::where('evidence_type_id', $mongo_id)->first();
        $registrarName = $evidence->registrar;
        // dd($registrarName);
        $documents = Registrar::where('registrar', $registrarName)->get();
        // dd($documents);
        $recipientEmails = []; // Initialize an empty array to store email addresses

        foreach ($documents as $document) {
            $emailIds = $document->email_id;

            // Add each email in $emailIds to $demoEmails array
            foreach ($emailIds as $email) {
                $recipientEmails[] = $email;
            }
        }
        // dd($recipientEmails);

        // dd($evidence->registrar);
        // dd($mongo_id);
        // Extract demo email addresses
        // $demoEmails = ['aravind27101994@gmail.com', 'sasaravind2013@gmail.com'];

        // Merge demo emails with any provided emails from the form
        $emails = array_merge($recipientEmails, explode(',', $request->input('emails')));

        // Extract content and subject from the form submission
        $sub = $request->input('sub');
        $salutation = $request->input('salutation');
        $compiledContent = $request->input('compiledContent');
        // dd($sub);

      // Send email to each recipient
      foreach ($emails as $email) {
        // Validate email format before sending (optional but recommended)
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Mail::to($email)->send(new MailMergePreview($sub, $salutation, $compiledContent));
        } else {
            // Handle invalid email address (log or skip)
            continue;
        }
    }
    $evidenceTypes = EvidenceType::where('status', 'active')
    ->whereNull('deleted_at')
    ->get();
    return view('evidence-management.list',compact('evidenceTypes'))->withErrors(['message' => 'Emails sent successfully!']);
    }
}
