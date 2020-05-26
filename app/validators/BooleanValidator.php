<?php


namespace App\Validators;

use Phalcon\Validation\Message;
use Phalcon\Validation\Validator;

class BooleanValidator extends Validator
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
        if( !is_bool($validation->getValue($attribute))) {
            $message = new Message($attribute . ' must be boolean');
            $validation->appendMessage($message);
            return false;

        }

        return true;
    }
}