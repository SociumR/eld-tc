<?php

namespace App\Components;

use Phalcon\Mvc\User\Component;
use Phalcon\Translate\Adapter\NativeArray;

class Locale extends Component
{

    public static $locale;
    /**
     * @return NativeArray
     */
    public function getTranslator()
    {
        // Ask browser what is the best language
        $language = $this->request->getBestLanguage();

        if($this->request->getHeader('Content-language')) {
            $language = $this->request->getHeader('Content-language');
        } else {
            $language = 'uk-UA';
        }

        /**
         * We are using JSON based files for storing translations.
         * You will need to check if the file exists!
         */

        $messages = [];
        $file = file_exists(__DIR__ . '/../messages/' . $language. '.php') ? __DIR__ . '/../messages/' . $language. '.php' : __DIR__ . '/../messages/' . 'uk-UA' . '.php';
        require_once($file);
        // Return a translation object $messages comes from the require
        // statement above
        return new NativeArray(
            [
                'content' => $messages,
            ]
        );

    }

    public static function t()
    {
        if(self::$locale == null) {
            self::$locale = (new Locale())->getTranslator();
        }

        return self::$locale;
    }
}