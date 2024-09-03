<?php

namespace App\Http\Controllers;

use App\Models\JobStatus;
use App\Models\UploadErrors;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SSEController extends Controller
{
    //
    public function stream($uploadId)
    {
        $response = new StreamedResponse(function() use ($uploadId) {
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');

            while (true) {
                $jobStatus = JobStatus::where('job_id', $uploadId)->first();
                $errors = UploadErrors::where('upload_id', $uploadId)->get();

                if ($jobStatus && in_array($jobStatus->status, ['completed', 'failed'])) {
                
                    echo "data: " . json_encode([
                        'status' => $jobStatus->status,
                        'errors' => $errors->pluck('error'),
                    ]) . "\n\n";

                    ob_flush();
                    flush();
                    break;
                }

                // Send the current status and errors
                echo "data: " . json_encode([
                    'status' => $jobStatus ? $jobStatus->status : 'unknown',
                    'errors' => $errors->pluck('error'),
                ]) . "\n\n";

                ob_flush();
                flush();

                sleep(5); 
            }
        });

        return $response;
    }
}
