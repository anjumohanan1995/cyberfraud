<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Session;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Imports\ComplaintImport;
use App\Models\UploadErrors;
use Excel;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel as FacadesExcel;
use Illuminate\Support\Facades\Auth;
use App\Models\JobStatus;


class ImportComplaintsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $sourceType;
    protected $userId;
    protected $uploadId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $filePath, string $sourceType , string $userId , string $uploadId)
    {
        //
       
        $this->filePath = $filePath;
        $this->sourceType = $sourceType;
        $this->userId = $userId;
        $this->uploadId = $uploadId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {   
        JobStatus::updateOrCreate(
            ['job_id' => $this->uploadId],
            ['status' => 'processing']
        );
        // FacadesExcel::import(new ComplaintImport($this->sourceType),storage_path('app/' . $this->filePath));
        UploadErrors::where('upload_id', $this->uploadId)->delete();
        try {
            FacadesExcel::import(new ComplaintImport($this->sourceType), storage_path('app/' . $this->filePath));
            JobStatus::where('job_id', $this->uploadId)->update(['status' => 'completed']);
           
        } catch (\Illuminate\Validation\ValidationException $e){
            
           
            foreach ($e->errors() as $rowIndex => $messages){
                foreach ($messages as $message){
                    UploadErrors::create([
                        'user_id' => $this->userId,
                        'upload_id' => $this->uploadId,
                        'error' => "$message",
                    ]);
                }
            }
            JobStatus::where('job_id', $this->uploadId)->update([
                'status' => 'failed',
                'error_message' => 'Validation errors occurred',
            ]);
          
            throw $e;
        } catch (\Exception $e){
           
            UploadErrors::create([
                'user_id' => $this->userId,
                'upload_id' => $this->uploadId,
                'error' => $e->getMessage(),
            ]);
            JobStatus::where('job_id', $this->uploadId)->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
           
            throw $e;
        }
    }
}
