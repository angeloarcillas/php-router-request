<?php

namespace Core\Http\Traits;

/**
 * Request Validator
 *
 * TODO:
 * 1. add more
 * 2. improve variable/function names
 * 3. refactor
 */
trait Validator
{

    protected $input;

    public function validate(array $fields)
    {
        // validated placeholder
        $validated = [];

        $_SESSION['errors'] = [];

        // loop through fields
        foreach ($fields as $key => $rules) {
            $this->input = $key; // <input name="$this->input">
            $value = $_REQUEST[$key]; // request value
            $total_rules = count($rules); // total fields to validate

            // loop throught rules
            for ($i = 0; $i < $total_rules; $i++) {
                // get rule and parameter
                [$rule, $parameter] = [...explode(':', $rules[$i]), null];
                // execute rule
                $this->$rule($value, $parameter);
            }
            // append validated rules
            array_push($validated, [$key => $value]);
        }

        // check if field has error
        $hasError = count($_SESSION['errors'] > 0);

        // if has error redirect back
        // else return all validated fields
        return !$hasError ? $validated : redirect()->back();
    }

    protected function required($request)
    {
        if (empty($request)) {
            $this->error("{$this->input} is required.");
        }
    }

    protected function string($request)
    {
        if (!is_string($request)) {
            $this->error("{$this->input} is not a string.");
        }
    }

    protected function min($request, $value)
    {
        if (strlen($request) < $value) {
            $this->error("{$this->input} is too short. min of {$value}");
        }
    }

    protected function max($request, $value)
    {
        if (strlen($request) > $value) {
            $this->error("{$this->input} is too long. max of {$value}");
        }
    }

    protected function email($request)
    {
        $isFilterEmail = filter_var($request, FILTER_VALIDATE_EMAIL);
        $isRegexEmail = preg_match('/^[a-zA-Z0-9@.]*$/', $request);

        if (!$isFilterEmail || !$isRegexEmail) {
            $this->error("invalid email format");
        }
    }

    /**
     * set session errors
     */
    protected function error(string $message)
    {
        $_SESSION['errors'][$this->input] = $message;
    }
}
