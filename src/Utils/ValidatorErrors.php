<?php

namespace App\Utils;

use Symfony\Component\Translation\TranslatorInterface;
use JMS\TranslationBundle\Annotation\Ignore;

class ValidatorErrors
{
    protected $translator;
    protected $errors;

    public function __construct(TranslatorInterface $translator, $errors = [])
    {
        $this->translator = $translator;
        $this->errors = $errors;
    }

    public function __call($name, $arguments)
    {
        return $this->get($name);
    }

    public function get($name)
    {
        $string = '';
        if(isset($this->errors[$name])) {
            $errors = [];
            foreach($this->errors[$name] as $error) {
                /** @Ignore */
                $errors[] = $this->translator->trans($error); // 'validator.error.custom.'.
            }
            $string = implode("\n", $errors);
        }
        return $string;
    }

    public function getAll()
    {
        return $this->errors;
    }

    public function empty()
    {
        return count($this->errors) == 0;
    }

    protected function init($field)
    {
        if(!isset($this->errors[$field])) $this->errors[$field] = [];
    }

    public function append($field, $error)
    {
        $this->errors[$field][] = $error;
    }
}