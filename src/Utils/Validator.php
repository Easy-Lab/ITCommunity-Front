<?php

namespace App\Utils;

use App\Entity\Contact;
use App\Entity\ContactUs;
use App\Entity\Flag;
use App\Entity\User;
use App\Service\UserService;
use Doctrine\Common\Util\Inflector;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;
use JMS\TranslationBundle\Annotation\Ignore;

class Validator
{
    protected $errors;
    protected $values;
    protected $post_values;
    protected $fields;
    protected $default_entity;
    protected $requestStack;
    protected $translator;
    protected $container;
    protected $failed;
    protected $userService;

    public function __construct(ContainerInterface $container, RequestStack $requestStack, TranslatorInterface $translator, UserService $userService, $get_from_session = false)
    {
        $errors = [];
        $values = [];

        if($get_from_session) {
            $session = $requestStack->getCurrentRequest()->getSession()->getFlashBag()->get("validator");
            if(is_array($session) && isset($session[0])) {
                if(isset($session[0]['errors']) && is_array($session[0]['errors'])) $errors = $session[0]['errors'];
                if(isset($session[0]['values']) && is_array($session[0]['values'])) $values = $session[0]['values'];
            }
        }

        $this->container = $container;
        $this->translator = $translator;
        $this->errors = new ValidatorErrors($translator, $errors);
        $this->values = $values;
        $this->requestStack = $requestStack;
        $this->userService = $userService;
        $this->post_values = $_POST;
        $this->fields = [];
        $this->failed = false;
    }

    public function __call($name, $arguments)
    {
        if($name == 'error') return $this->errors;
        return false;
    }

    protected function getEntityAttribute($entity, $attr)
    {
        $getter = 'get'.ucfirst(Inflector::camelize($attr));
        if(method_exists($entity, $getter)) {
            if(strpos(get_class($entity), User::class) !== false && in_array($attr, ['firstname', 'lastname', 'phone', 'address', 'address2', 'username', 'birthdate'])) {
                $value = $this->userService->getUncrypted($entity, $attr);
            }
            elseif(strpos(get_class($entity), Contact::class) !== false && in_array($attr, ['firstname', 'lastname', 'phone', 'email'])) {
                $value = $this->userService->getUncrypted($entity, $attr);
            }
            elseif(strpos(get_class($entity), ContactUs::class) !== false && in_array($attr, ['firstname', 'lastname', 'phone', 'email'])) {
                $value = $this->userService->getUncrypted($entity, $attr);
            }
            elseif(strpos(get_class($entity), Flag::class) !== false && in_array($attr, ['firstname', 'lastname', 'phone', 'email'])) {
                $value = $this->userService->getUncrypted($entity, $attr);
            }
            else {
                $value = $entity->{$getter}();
                if($value instanceof \DateTime) $value = $value->format('d/m/Y');
            }
            return $value;
        }
        return '';
    }

    public function post()
    {
        return $this->requestStack->getCurrentRequest()->getMethod() == 'POST';
    }

    public function error($name = null, $error = null, $generic = false)
    {
        if(is_null($error) && is_null($name)) {
            return $this->errors;
        }
        elseif(is_null($error)) {
            return $this->errors->get($name);
        }
        else {
            $this->init($name);
            $this->errors->append($name, $generic ? 'validator.error.generic.'.$error : 'validator.error.custom.'.$error);
            return $this;
        }
    }

    public function pass($name) {
        return empty($this->errors->get($name));
    }

    public function errors()
    {
        return $this->errors->getAll();
    }

    public function keep()
    {
        $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add("_validator", [
            'errors' => $this->errors(),
            'values' => $this->post_values,
        ]);
        return $this;
    }

    public function success($traduction = null, $flashSubDomain = null)
    {
        if(is_null($traduction)) $traduction = 'validator.message.success';
        else $traduction = 'validator.message.custom.'.$traduction;
        /** @Ignore */
        $message = $this->translator->trans($traduction);
        $flashDomain = 'success';
        if(!empty($flashSubDomain))
            $flashDomain = $flashSubDomain . '/' . $flashDomain;
        $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add($flashDomain, $message);
        return $this;
    }

    public function successWithoutCustom($traduction = null, $flashSubDomain = null)
    {
        if(is_null($traduction)) $traduction = 'validator.message.success';
        else $traduction = $traduction;
        /** @Ignore */
        $message = $this->translator->trans($traduction);
        $flashDomain = 'success';
        if(!empty($flashSubDomain))
            $flashDomain = $flashSubDomain . '/' . $flashDomain;
        $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add($flashDomain, $message);
        return $this;
    }

    public function fail($traduction = null)
    {
        if(is_null($traduction)) $traduction = 'validator.message.fail';
        else $traduction = 'validator.message.custom.'.$traduction;
        /** @Ignore */
        $message = $this->translator->trans($traduction);
        $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add("danger", $message);
        $this->failed = true;
        return $this;
    }

    public function check()
    {
        foreach($this->fields as $name => $field) {
            foreach($field['rules'] as $rule) {
                $this->checkRule($name, $rule);
            }
        }

        return !$this->failed && $this->errors->empty();
    }

    public function getInputValue($name)
    {
        $value = $this->requestStack->getCurrentRequest()->get($name, null);
        if(is_null($value) && isset($this->values[$name])) {
            $value = $this->values[$name];
        }
        return $value;
    }

    public function get($name)
    {
        return $this->getInputValue($name);
    }

    public function checked($name, $option = null)
    {
        if(!is_null($option)) {
            return $this->value($name) == $option ? 1 : 0;
        }

        return !is_null($this->value($name)) && !empty($this->value($name)) ? 1 : 0;
    }

    public function isChecked($name)
    {
        return !is_null($this->getInputValue($name)) && !empty($this->getInputValue($name));
    }

    public function value($name, $default = null)
    {
        $value = $this->getInputValue($name);

        if(empty($value)) {
            if(isset($this->fields[$name])) {
                if(isset($this->fields[$name]['entity'])) {
                    $value = $this->getEntityAttribute($this->fields[$name]['entity'], $name);
                }
                else {
                    $value = $this->getEntityAttribute($this->default_entity, $name);
                }
            }
            else {
                $value = $this->getEntityAttribute($this->default_entity, $name);
            }
        }
        return (empty($value) && $default != null) ? $default : $value;
    }

    public function setValue($name, $value)
    {
        $this->values[$name] = $value;
        $this->post_values[$name] = $value;
    }

    public function inject($fields = null)
    {
        foreach($this->fields as $name => $field)
        {
            if(func_num_args() > 0 && !in_array($name, func_get_args())) continue;

            if(isset($field['entity'])) {
                $entity = &$field['entity'];
            }
            else {
                $entity = &$this->default_entity;
            }

            $entity->{'set'.ucfirst(Inflector::camelize($name))}($this->getInputValue($name));
        }
        return $this;
    }

    protected function init($name)
    {
        if(!isset($this->fields[$name])) $this->fields[$name] = ['rules' => []];
    }

    /**
     * @param $entity
     * @return $this
     */
    public function entity(&$entity, array $fields = null)
    {
        if(!is_null($fields)) {
            foreach($fields as $name) {
                $this->init($name);
                $this->fields[$name]['entity'] = &$entity;
            }
        }
        else $this->default_entity = $entity;

        return $this;
    }

    public function fields($fields, $type)
    {
        foreach($fields as $name)
        {
            $this->init($name);
            $this->fields[$name]['rules'][] = $type;
        }
        return $this;
    }

    public function required()
    {
        $this->fields(func_get_args(), 'required');
        return $this;
    }

    public function email()
    {
        $this->fields(func_get_args(), 'email');
        return $this;
    }

    public function phone()
    {
        $this->fields(func_get_args(), 'phone');
        return $this;
    }

    public function zipcode()
    {
        $this->fields(func_get_args(), 'zipcode');
        return $this;
    }

    public function departement()
    {
        $this->fields(func_get_args(), 'departement');
        return $this;
    }

    public function alphanumeric()
    {
        $this->fields(func_get_args(), 'alphanumeric');
        return $this;
    }

    public function integer()
    {
        $this->fields(func_get_args(), 'integer');
        return $this;
    }

    public function float()
    {
        $this->fields(func_get_args(), 'float');
        return $this;
    }

    public function url()
    {
        $this->fields(func_get_args(), 'url');
        return $this;
    }

    public function dni(){
        $this->fields(func_get_args(), 'dni');
        return $this;
    }

    public function max($name, $length)
    {
        $this->init($name);
        $this->fields([$name], 'max_length');
        $this->fields[$name]['rule_max_length'] = $length;
        return $this;
    }

    public function min($name, $length)
    {
        $this->init($name);
        $this->fields([$name], 'min_length');
        $this->fields[$name]['rule_min_length'] = $length;
        return $this;
    }

    public function inArray($name, array $array)
    {
        $this->init($name);
        $this->fields([$name], 'in_array');
        $this->fields[$name]['rule_in_array'] = $array;
        return $this;
    }

    public function identical($origin, $name)
    {
        $this->init($name);
        $this->fields([$name], 'identical');
        $this->fields[$name]['rule_identical'] = $origin;
        return $this;
    }

    public function zones()
    {
        $this->fields(func_get_args(), 'zones');
        return $this;
    }

    public function date()
    {
        $this->fields(func_get_args(), 'date');
        return $this;
    }

    //

    protected function checkRule($name, $rule)
    {
        $value = $this->getInputValue($name);
        $error = null;
        switch($rule) {
            case 'required':
                if(empty($value)) $error = 'required';
                break;
            case 'in_array':
                if(!in_array($value, $this->fields[$name]['rule_in_array'])) $error = 'in_array';
                break;
            case 'identical':
                $_value = $this->getInputValue($this->fields[$name]['rule_identical']);
                if($_value !== $value) $error = 'identical';
                break;
            case 'min_length':
                if(strlen($value) < $this->fields[$name]['rule_min_length']) $error = 'min_length';
                break;
            case 'max_length':
                if(mb_strlen($value, 'UTF-8') > $this->fields[$name]['rule_max_length']) $error = 'max_length';
                break;
            case 'email' :
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) $error = 'email';
                break;
            case 'phone' :
                switch($this->container->getParameter('locale')) {
                    // TODO:
                     case 'es':
                         $_pattern = "/^\+?\d{7,15}$/";
                     if (!(strlen($value)==9 && ctype_digit($value))
                        && !preg_match($_pattern, $value)){ $error = 'phone';
                     }
                     break;

                    case 'it':
                        $_pattern = "/^(03)\d{9}$/";
                        if (!(strlen($value)==11 && ctype_digit($value))
                            && !preg_match($_pattern, $value)){ $error = 'phone';
                }
                        break;

                    case 'de':
                        $_pattern = "/^\+?\d{7,15}$/";
                        if (!(strlen($value)<=12 && ctype_digit($value))
                            && !preg_match($_pattern, $value)){ $error = 'phone';
                        }
                        break;

                    default: // fr & co
                        $_pattern = "/^\+?\d{7,15}$/";
                        if (!(strlen($value) == 10 && ctype_digit($value))
                            && (!preg_match($_pattern, $value))){ $error = 'phone';
                        }
                        break;
                }
                break;
            case 'zipcode' :
                switch($this->container->getParameter('locale')) {
                    // TODO: add other countries rules
                    // case 'es':
                    // ...
                    //  break;
                    default: // fr & co
                        if (!(strlen($value) == 5 && ctype_digit($value))) $error = 'zipcode';
                        break;
                }
                break;
            case 'dni':
                switch($this->container->getParameter('locale')) {
                    case 'es':
                        $_pattern1 = "/^[0-9]{8}[[:alpha:]]{1}$/";
                        $_pattern2 = "/^[[:alpha:]]{1}[0-9]{7}[[:alpha:]]{1}$/";
                        $_pattern3 = "/^[0-9]{2}[[:alpha:]]{2}[0-9]{5}$/";

                        if (!(strlen($value) == 9)) {
                            if (!preg_match($_pattern1, $value) or !preg_match($_pattern2, $value) or !preg_match($_pattern3, $value)) {
                                $error = 'dni';
                            }
                        }
                        break;
                    case 'it':
                        if (!(strlen($value) == 16)) {
                            $_pattern1 = "/^.{16}$/";
                            if (!preg_match($_pattern1, $value)) {
                                $error = 'dni';
                            }
                        }
                        break;
                }
                break;
            case 'departement' :
                if (!(strlen($value) == 2 && ctype_digit($value))) $error = 'departement';
                break;
            case 'alphanumeric' :
                if (!ctype_alnum($value)) $error = 'alphanumeric';
                break;
            case 'integer' :
                if (!filter_var($value, FILTER_VALIDATE_INT)) $error = 'integer';
                break;
            case 'float' :
                if (!filter_var($value, FILTER_VALIDATE_FLOAT)) $error = 'float';
                break;
            case 'url':
                $_pattern = "%^((?:(?:https?|ftp)://))?(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]-*)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]-*)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,}))\.?)(?::\d{2,5})?(?:[/?#]\S*)?$%iuS";
                if (!preg_match($_pattern, $value)) $error = 'url';
                break;
            case 'zones':
                $value = str_replace(' ', '', $value);
                $_pattern = "/^((\d{2}|\d{5})(\,|$))*$/";
                if (!preg_match($_pattern, $value)) $error = 'zones';
                break;
            case 'date':
                if(!preg_match('/^([0-9]{2}\/[0-9]{2}\/[0-9]{4})|([0-9]{4}-[0-9]{2}-[0-9]{2})$/', $value)) $error = 'date';
                break;
        }
        if(!is_null($error)) $this->errors->append($name, 'validator.error.generic.'.$error);
    }

    public function DatacheckSpam(String $string){
        $testHTML = mb_strpos($string,"<");
        if ($testHTML){
            return "html";
        }
        $testHTML2 = mb_strpos($string,">");
        if ($testHTML2){
            return "html";
        }
        $testTag = mb_strpos($string,"[");
        if ($testTag){
            return "html";
        }
        $testTag2 = mb_strpos($string,"]");
        if ($testTag2){
            return "html";
        }
        $testURL = strstr($string, "http://");
        if ($testURL){
            return "url";
        }
        $testURLs = strstr($string, "https://");
        if ($testURLs){
            return "url";
        }
        $testURLs = strstr($string, "www.");
        if ($testURLs){
            return "url";
        }
        return null;
    }
}
