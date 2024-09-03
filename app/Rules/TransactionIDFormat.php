<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class TransactionIDFormat implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    protected $rowIndex;

    public function __construct($rowindex)
    {
        //
        $this->rowIndex = $rowindex;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //
      
        //  if(is_float($value)){   
        //     return false;
        //  }
        //  else{
        //     return true;
        //  }

        $value = (string)$value;
        if (preg_match('/\+E/i', $value)){
            
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Row ' . $this->rowIndex . ': The Transaction ID format is incorrect.';
    }
}
