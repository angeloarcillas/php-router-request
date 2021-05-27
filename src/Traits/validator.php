<?php

namespace SimpleRouter\Traits;

use \Exception;

/**
 * Request Validator
 *
 * !FIXME: There's no difference between GET and POST request 
 *   do I need to identify it?
 * 
 * TODO:
 * 1. add more
 * 2. improve variable/function names
 * 3. refactor
 */


trait Validator
{
    /**
     * input name (<input name="" />)
     */
    protected $input;

    // TODO: Check if rule doesn't exists then thrown an error
    // public function __call($name, $arguments)
    // {
    //     $_SESSION['errors'] = [];
    //     $_SESSION['errors']['method']  = "Method or Rule {$name} is not supported";
    //     return redirect()->back();
    // }

    /**
     * Validate input fields with rules
     * 
     * @param array $fields
     * @return void|array validated fields
     */
    public function validate(array $fields): ?array
    {
        // validated fields placeholder
        $validated = [];

        // set session errors
        $_SESSION['errors'] = [];

        // loop through fields
        foreach ($fields as $key => $rules) {
            // check if the user didnt define an input name or a rule
            if (!is_string($key)) {
                throw new Exception("Request \"{$rules}\" is missing a field or a rule");
            }

            // field value
            $value = $_REQUEST[$key] ?? null;

            // <input name="$this->input">
            $this->input = $key;

            // check if the current request exists
            if (!isset($value)) {
                throw new Exception("request field \"{$this->input}\" doesn't exists.");
            }

            // if "required|min:6|max:255"  (string with `|`)
            // convert to ["required", "min:6", "max:255"]
            $rules = (!is_string($rules)) ? $rules : explode("|", $rules);

            // total rules to validate
            $total_rules = count($rules);

            // loop throught rules
            for ($i = 0; $i < $total_rules; $i++) {
                // get rule and parameter
                [$rule, $parameter] = [...explode(':', $rules[$i]), null];

                // check if rule doesn't exists
                if (!method_exists($this::class, $rule)) {
                    throw new Exception("Rule \"{$rule}\" doesnt exists");
                }

                // execute rule
                $this->$rule($value, $parameter);
            }

            // append validated rules
            $validated[$key] = $value;
        }

        // check if field has error
        $hasError = count($_SESSION['errors']) > 0;

        // if has error redirect back
        // else return all validated fields
        return ($hasError) ? redirect()->back() : $validated;
    }


    /**
     * Check if request has is set and has a value
     * 
     * @param string $request
     */
    protected function required(string $request): void
    {
        if (!isset($request) || empty($request)) {
            $this->error("{$this->input} is required.");
        }
    }

    /**
     * Check if Request is a string
     * 
     * @param string $request
     */
    protected function string(string $request): void
    {
        if (!is_string($request)) {
            $this->error("{$this->input} is not a string.");
        }
    }

    /**
     * Check if rule minimum value meet
     * 
     * @param string $request
     * @param string $value
     */
    protected function min(string  $request, string $value)
    {
        // check if value has non numeric character
        if ($this->isNumeric($value)) {
            throw new Exception("Rule \"min\" must not contain a non numerical value.");
        }

        // check if input value is less than min rule value
        if (strlen($request) < (int) $value) {
            $this->error("{$this->input} is too short. min of {$value}");
        }
    }


    /**
     * Check if rule maximum value didn't exceed
     * 
     * @param string $request
     * @param string $value
     */
    protected function max(string $request, string $value)
    {
        // check if value has non numeric character
        if ($this->isNumeric($value)) {
            throw new Exception("Rule \"max\" must not contain a non numerical value.");
        }

        // check if input value is greater than max rule value
        if (strlen($request) > (int) $value) {
            $this->error("{$this->input} is too long. max of {$value}");
        }
    }

    /**
     * Check if request is a valid email
     * 
     * @param string @request
     */
    protected function email($request)
    {
        // dd(!$this->isEmail($request));
        // validate if valid email syntax
        if (!$this->isEmail($request)) {
            $this->error("invalid email format");
        }
    }

    /**
     * Check if 2 input is a match   
     * 
     * @param string $request
     * @param string $key
     */
    protected function same(string $request, string $key)
    {
        if ($_REQUEST[$key] !== $request) {
            $this->error("$this->input and $key must match");
        }
    }

    /**
     * Check if input and confirm input is a match
     * 
     * @param string $request
     */
    protected function confirm(string $request)
    {
        // ex. password_confirmation / foo_confirmation
        $confirm = request("{$this->input}_confirmation");

        // check if 2 value matched
        if ($request !== $confirm) {
            $this->error("{$this->input} and confirm {$this->input} didn't match");
        }
    }

    /**
     * Check if string $value only contains numeric value
     * 
     * @param string $value
     * @return bool
     */
    public function isNumeric(string $value): bool
    {
        return preg_match('/[^0-9]/', $value);
    }

    /**
     * Check if $value is an email
     * 
     * @param string $email
     * @return bool
     */
    public function isEmail(string $email): bool
    {
        // sanitize email
        $sanitizedEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
        // validate email
        $isFilterValidEmail = filter_var($sanitizedEmail, FILTER_VALIDATE_EMAIL);
        // 3-50 char/. @ 2-12 char . 2-8 char | example.123@mail.com
        $isRegexValidEmail = preg_match('/^[\w\.]{3,50}@\w{2,12}\.\w{2,8}$/', $sanitizedEmail);
        // validate if valid email syntax
        return ($isFilterValidEmail && $isRegexValidEmail);
    }

    /**
     * Set session errors
     * 
     * @param string $message
     */
    protected function error(string $message)
    {
        $_SESSION['errors'][$this->input] = $message;
    }
}
