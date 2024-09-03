<?php

namespace App\Jobs;

use App\Imports\BankImports;
use App\Models\UploadErrors;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel as FacadesExcel;

class ImportBankActionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $filePath;
    protected $tempFile;
    protected $userId;
    protected $uploadId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $tempFile , string $userId , string $uploadId)
    {
        //
       
        $this->tempFile = $tempFile;
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
        //
      
        UploadErrors::where('upload_id', $this->uploadId)->delete();
       
        try{
           
            // Excel::import(new BankImports, $tempFile, null, \Maatwebsite\Excel\Excel::CSV);
            Excel::import(new BankImports, $this->tempFile);
        } catch (\Illuminate\Validation\ValidationException $e){
            Log::error('Job failed: ' . $e->getMessage());
            foreach ($e->errors() as $rowIndex => $messages){
                foreach ($messages as $message){
                    UploadErrors::create([
                        'user_id' => $this->userId,
                        'upload_id' => $this->uploadId,
                        'error' => "$message",
                    ]);
                }
            }
            throw $e;
        } catch (\Exception $e){
            Log::error('Job failed: ' . $e->getMessage());
            UploadErrors::create([
                'user_id' => Auth::user()->_id,
                'upload_id' => $this->uploadId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
