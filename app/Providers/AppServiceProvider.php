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
            //dd($value);
            $formats = [
                'd/m/Y H:i',
                'd/m/Y H:i:s A',
                'd-m-Y H:i:s A',
                'd/m/Y h:i:s A',
                'd-m-Y h:i:s A',
                'd/m/Y',
                'dd-mm-YYYY',
                'd/F/Y',
                'd-F-Y',
                'd-m-Y H:i',
                'd/M/Y',
                'd-M-Y',
                'Y-m-d H:i:s A',
            ];

            foreach ($formats as $format){
                //dd($value);
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
    }
}
