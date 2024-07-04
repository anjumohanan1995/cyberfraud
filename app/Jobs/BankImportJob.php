<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use App\Imports\BankImports;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class BankImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $file;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($file)
    {
        //
       
        $this->file = $file;
       // dd($this->file);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {  
        \Log::info('Attempting to process job.');

    try {
        // Your job logic here
        Excel::import(new BankImports, $this->file);

        \Log::info('Job processed successfully.');
    } catch (\Exception $e) {
        \Log::error('Error processing job: ' . $e->getMessage());
        // Handle or rethrow the exception as needed
    }
    }
}
