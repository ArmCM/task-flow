<?php

namespace Core;

class ValidationRules
{
    private array $data;
    private array $rules;
    private array $errors = [];

    public function __construct(array $data, array $rules)
    {
        $this->data  = $data;
        $this->rules = $rules;
    }

    public function verify(): bool
    {
        foreach ($this->rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                if ($rule === 'required' && empty($value)) {
                    $this->errors[$field][] = "The field $field is required";
                }

                if ($rule === 'string' && !is_string($value)) {
                    $this->errors[$field][] = "The field $field must be a string";
                }

                if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = "The field $field must be a valid email";
                }

                if (str_starts_with($rule, 'min:')) {
                    $min = (int) explode(':', $rule)[1];
                    if (strlen($value) < $min) {
                        $this->errors[$field][] = "The field $field must be at least $min characters";
                    }
                }

                if (str_starts_with($rule, 'max:')) {
                    $max = (int) explode(':', $rule)[1];
                    if (strlen($value) > $max) {
                        $this->errors[$field][] = "The field $field must be max $max characters";
                    }
                }

                if (str_starts_with($rule, 'in:')) {
                    $options = explode(',', explode(':', $rule)[1]);

                    if (!in_array($value, $options)) {
                        $this->errors[$field][] = "The field $field must be one of: " . implode(', ', $options);
                    }
                }

                if ($rule === 'date' && !strtotime($value)) {
                    $this->errors[$field][] = "The field $field must be a valid date";
                }
            }
        }

        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function validated(): array
    {
        return $this->data;
    }
}
