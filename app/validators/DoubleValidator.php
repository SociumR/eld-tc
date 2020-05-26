<?php


namespace App\Validators;

use Phalcon\Validation;
use Phalcon\Validation\Message;
use Phalcon\Validation\Validator;

class DoubleValidator extends Validator
{

    /**
     * Executes the validation
     *
     * @param Validation $validation
     * @param string $attribute
     * @return bool
     */
    public function validate(Validation $validation, $attribute)
    {
        if( !is_float($validation->getValue($attribute))) {
            $message = new Message($attribute . ' must be float');
            $validation->appendMessage($message);
            return false;

        }

        return true;
    }
}