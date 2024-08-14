<?php

namespace App\Providers;

use App\Models\Complaint;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{

    protected $acknowledgementNos = [];
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        // Load acknowledgement numbers into the provider

        $this->acknowledgementNos = Complaint::pluck('acknowledgement_no')->toArray();
        $this->acknowledgementNos = array_unique($this->acknowledgementNos);

        Validator::extend('exists_in_acknowledgement_nos', function ($attribute, $value, $parameters, $validator) {
            
            return in_array($value,  $this->acknowledgementNos);

        }, 'The :attribute is not a valid acknowledgement number.');



        Validator::extend('valid_date_format', function ($attribute, $value, $parameters, $validator) {

             $value = str_replace(',', '', $value);
             if (is_numeric($value)) {
                $value =  $this->excelSerialToDate($value);

            }

            $formats = [
                'd/m/Y H:i:s',
                'd-m-Y H:i:s',
                'd/m/Y h:i:s A',
                'd-m-Y h:i:s A',
                'd-m-Y',
                'd/F/Y',
                'd-F-Y',
                'd/m/Y H:i',
                'd-m-Y H:i',
                'd/M/Y',
                'd-M-Y',
                'd-m-Y, h:i:s A',
                'd/m/Y',
                
                'd-m-Y H:i:s A',
                'Y-m-d H:i:s',
                'd/m/Y G:i:s'
            ];



            foreach ($formats as $format){
              
                try {
                    $date = Carbon::createFromFormat($format, $value);


                    // Adjust year if necessary
                    if (strlen($date->year) == 2) {
                        $date->year = $date->year + ($date->year < 30 ? 2000 : 1900);
                    }

                    return $date->format($format) === $value;
                } catch (\Exception $e) {

                    // Continue to the next format
                    continue;
                }
            }
       

            return false;
        }, 'The :attribute is not in a valid date format.');


        Validator::extend('valid_date_format_entry_date', function ($attribute, $value, $parameters, $validator) {
            
             if (is_numeric($value)) {
                return false;
            }
          
            $value = str_replace(',', '', $value);
            // if (strpos($value, '/') !== false) {
            //  $value = str_replace('/', '-', $value);
            
            // }              
           
           $formats = [
             
               'd/m/Y H:i:s',
                'd-m-Y H:i:s',
                'd/m/Y h:i:s A',
                'd-m-Y h:i:s A',
                'd-m-Y',
                'd/F/Y',
                'd-F-Y',
                'd/m/Y H:i',
                'd-m-Y H:i',
                'd/M/Y',
                'd-M-Y',
                'd-m-Y, h:i:s A',
                'd/m/Y',
                'd-m-Y H:i:s A',
                'Y-m-d H:i:s',
                'd/m/Y G:i:s'
                     
              
           ];
     

   
           foreach ($formats as $format){

            
            try {
             // $date = Carbon::createFromFormat($format, $value);
             $date = Carbon::createFromFormat($format, $value);
            

              // Adjust year if necessary
              if (strlen($date->year) == 2) {

                  $date->year = $date->year + ($date->year < 30 ? 2000 : 1900);

              }
            
             return $date->format($format) == $value;
            } catch (\Exception $e) {

                continue;
            }
        }
       
        return false;

    }, 'The :attribute is not in a valid date format.');



 }

    protected function excelSerialToDate($serial , $targetFormat = 'd-m-Y H:i:s')
    {
        // Convert Excel serial date to a Carbon date
        try {

            $baseDate = Carbon::create(1900, 1, 1);
            if ($serial > 60) {
                $serial--;
            }
            $date = $baseDate->addDays($serial);
            return $date->format($targetFormat);
        }
         catch (\Exception $e) {
            return false; // Return null if conversion fails
        }
    }
}
