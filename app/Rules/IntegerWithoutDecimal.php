<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class IntegerWithoutDecimal implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    protected $rowIndex;
    protected $attribute;

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
       
        $this->attribute = $attribute;
        if(is_float($value)){
           
            return false;
        }
        else{
            return true;
        }
          
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
           $attr_value = "" ;
        if($this->attribute == 'acknowledgement_no'){
            $attr_value = 'Acknowledgement number';
        }
        if($this->attribute == 'account_id'){
            $attr_value = 'Account number';
        }
        return 'Row ' . $this->rowIndex . ': The '.$attr_value.' format is incorrect.';
    }
}
