<?php


namespace App\Validators;


use Phalcon\Validation\Message;
use Phalcon\Validation\Validator;

class ArrayValidator extends Validator
{

    /**
     * Executes the validation
     *
     * @param \Phalcon\Validation $validation
     * @param string $attribute
     * @return bool
     */
    public function validate(\Phalcon\Validation $validation, $attribute)
    {

        if( !is_array($validation->getValue($attribute))) {
            $message = new Message($attribute . ' must be array');
            $validation->appendMessage($message);
            return false;

        }

        return true;
    }
}