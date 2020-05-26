<?php

namespace App\Helpers;


use Exception;
use Phalcon\Validation;


class AttributeValidator extends Validation
{

    const CHILD_SEPARATOR = '*';
    protected $data;

    public function getValue($field) {
        return $field != null ? $this->parseAttribute($field) : null;
    }


    private function parseAttribute($attribute)
    {
        $attributes = explode('.', $attribute);

        $params = $this->_data;
        $previous = "";
/*
        if(array_pop($attributes) == self::CHILD_SEPARATOR) {
            $this->createChildValidator(substr($attribute, 0, -1), $params);
            return $params;
        }*/

        foreach ($attributes as $attr) {
           /* if ($attr == self::CHILD_SEPARATOR) {
                $this->createChildValidator(substr($previous, 1), $params);
                continue;
            }*/

            if (gettype($params->$attr) == 'array') {
                $this->createValidatorForArray($attr, $params->$attr);
            }

            $previous .= '.' . $attr;

            if (property_exists($params, $attr)) {
                if(gettype($params->$attr) == 'array') {
                    $params = sizeof($params->$attr) > 0 ? $params->$attr : null;
                } else {
                    $params = $params->$attr !== null ? $params->$attr : null;
                }
            } else {
                throw new Exception('property ' . $attribute . ' does not exist');
            }
        }


        return $params;
    }

    private function createChildValidator($attribute, $data, $v = null)
    {

        foreach ($data as $key => $content) {
            $contentType = gettype($content);
            if ($contentType == 'array' || $contentType == 'object') {
                $validator = $this->getAttributeValidator($attribute);
                $this->createChildValidator($attribute . '.' . $key, $content, $validator);
            } else {
                $validator = $v ? $v : $this->getAttributeValidator($attribute);
                if ($validator) {
                    $validator->validate($this, $attribute . '.' . $key);
                }
            }
        }
    }

    private function createValidatorForArray($attribute, $data, $v = null)
    {
        foreach ($data as $key => $content) {
            $contentType = gettype($content);
            if ($contentType == 'array' || $contentType == 'object') {
                $validator = $this->getAttributeValidator($attribute);
                $this->createChildValidator($attribute . '.' . $key, $content, $validator);
            } else {
                $validator = $v ? $v : $this->getAttributeValidator($attribute);
                if ($validator) {
                    $validator->validate($this, $attribute . '.' . $key);
                }
            }
        }
    }


    private function getAttributeValidator($attribute)
    {

        foreach ($this->_validators as $validator) {
            if(substr($validator[0],0 , -2) == $attribute) {
                $v = get_class($validator[1]);
                return new $v();
            }
        }

    }

}