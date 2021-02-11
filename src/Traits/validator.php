<?php

namespace Core\Http\Traits;

/**
 * Request Validator
 *
 * TODO:
 * 1. use SESSION not error()
 * 2. improve variable/function names
 * 3. refactor
 *
 *
 * !NOTE: change all error() to session and redirect()->back()
 */
trait Validator
{

    protected $input;

    public function validate(array $data)
    {
        $return = [];
        foreach ($data as $key => $rules) {
            $this->input = $key;
            $value = request($key);
            $total_rules = count($rules);

            for ($i = 0; $i < $total_rules; $i++) {
                if (!str_contains($rules[$i], ':')) {
                    $rules[$i] = "{$rules[$i]}:";
                }

                [$rule, $parameter] = explode(':', $rules[$i]);
                if (!$this->$rule($value, $parameter)) {

                }
            }
            array_push($return, [$key => request($key)]);
        }

        return $return;
    }

    protected function required($request)
    {
        if (empty($request)) {
            error("{$this->input} is required.");
        }
    }

    protected function string($request)
    {
        if (!is_string($request)) {
            error("{$this->input} is not a string.");
        }
    }

    protected function min($request, $value)
    {
        if (strlen($request) < $value) {
            error("{$this->input} is too short.");
        }
    }

    protected function max($request, $value)
    {
        if (strlen($request) > $value) {
            error("{$this->input} is too long.");
        }
    }

    protected function email($request)
    {
        if (!filter_var($request, FILTER_VALIDATE_EMAIL)
            || !preg_match('/^[a-zA-Z0-9@.]*$/', $request)) {
            error("invalid email format");
        }
    }
}
