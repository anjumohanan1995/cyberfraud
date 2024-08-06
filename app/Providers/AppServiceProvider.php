<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
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
        //
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
                }
            }
            
            return false;
        }, 'The :attribute is not in a valid date format.');


        Validator::extend('valid_date_format_entry_date', function ($attribute, $value, $parameters, $validator) {
           
             if (is_numeric($value)) {
                $value =  $this->excelSerialToDate($value);
            }
           
            $value = str_replace(',', '', $value);
         //  dd($value);
           $formats = [
                'd-m-Y',
               'd/m/Y H:i:s',
               'd-m-Y H:i:s',
               'd/m/Y h:i:s A',
               'd-m-Y h:i:s A',
              // 'd-m-Y',
               'd-m-y',
               'd/F/Y',
               'd-F-Y',
               'd/m/Y H:i',
               'd-m-Y H:i',
               'd/m/Y',
               'd-M-Y',
               'd-m-Y, h:i:s A',
               'd/m/y',  
               'd-m-Y H:i:s A',
               'Y-m-d H:i:s',
               'dd-mm-YYYY H:i:s',
               'd/m/YYYY',
              
           ];
   
           foreach ($formats as $format){
 
//             }
               try {
                $date = Carbon::createFromFormat($format, $value);
                //dd($date);
                                   
                 // Adjust year if necessary
                 if (strlen($date->year) == 2) {
                     $date->year = $date->year + ($date->year < 30 ? 2000 : 1900);
                 }
 
                 return $date->format($format) === $value;
               } catch (\Exception $e) {
              
                   // Continue to the next format
               }
           }
           
           return false;
       }, 'The :attribute is not in a valid date format.');
        
    }

    protected function excelSerialToDate($serial)
    {
        // Convert Excel serial date to a Carbon date
        try {
            $baseDate = Carbon::create(1899, 12, 30); // Excel starts from Dec 30, 1899
            $date = $baseDate->addDays((int)$serial);

            return $date->toDateTimeString(); // Return in 'Y-m-d H:i:s' format
        } catch (\Exception $e) {
            return null; // Return null if conversion fails
        }
    }
}
