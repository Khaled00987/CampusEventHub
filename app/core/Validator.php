<?php
/**
 * Validator.php — Server-side form validation.
 *
 * Collects field errors in a friendly array for display in views.
 * Used on every POST action alongside client-side validation in validation.js.
 */

declare(strict_types=1);

class Validator
{
    /** @var array<string, string> */
    private array $errors = [];

    /** @var array<string, mixed> */
    private array $data;

    /**
     * @param array<string, mixed> $data Typically $_POST
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array<string, string>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function fails(): bool
    {
        return $this->errors !== [];
    }

    /**
     * @return bool
     */
    public function passes(): bool
    {
        return !$this->fails();
    }

    /**
     * @param string $field
     * @return string
     */
    public function value(string $field): string
    {
        return trim((string) ($this->data[$field] ?? ''));
    }

    /**
     * @param string $field
     * @param string $label
     */
    public function required(string $field, string $label): void
    {
        if ($this->value($field) === '') {
            $this->errors[$field] = "{$label} is required.";
        }
    }

    /**
     * @param string $field
     * @param string $label
     * @param int $min
     * @param int $max
     */
    public function length(string $field, string $label, int $min, int $max): void
    {
        $len = strlen($this->value($field));
        if ($len > 0 && ($len < $min || $len > $max)) {
            $this->errors[$field] = "{$label} must be between {$min} and {$max} characters.";
        }
    }

    /**
     * @param string $field
     * @param string $label
     */
    public function email(string $field, string $label): void
    {
        $val = $this->value($field);
        if ($val !== '' && !filter_var($val, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "{$label} must be a valid email address.";
        }
    }

    /**
     * @param string $field
     * @param string $label
     */
    public function passwordStrength(string $field, string $label): void
    {
        $val = $this->value($field);
        if ($val === '') {
            return;
        }
        if (strlen($val) < 8) {
            $this->errors[$field] = "{$label} must be at least 8 characters.";
            return;
        }
        if (!preg_match('/[A-Z]/', $val)) {
            $this->errors[$field] = "{$label} must include an uppercase letter.";
            return;
        }
        if (!preg_match('/[a-z]/', $val)) {
            $this->errors[$field] = "{$label} must include a lowercase letter.";
            return;
        }
        if (!preg_match('/[0-9]/', $val)) {
            $this->errors[$field] = "{$label} must include a number.";
        }
    }

    /**
     * @param string $field
     * @param string $confirmField
     */
    public function confirmed(string $field, string $confirmField = 'password_confirmation'): void
    {
        if ($this->value($field) !== $this->value($confirmField)) {
            $this->errors[$confirmField] = 'Password confirmation does not match.';
        }
    }

    /**
     * @param string $field
     * @param string $label
     * @param int $min
     * @param int $max
     */
    public function integerRange(string $field, string $label, int $min, int $max): void
    {
        $val = $this->value($field);
        if ($val === '') {
            return;
        }
        if (!ctype_digit($val) && !preg_match('/^-?\d+$/', $val)) {
            $this->errors[$field] = "{$label} must be a whole number.";
            return;
        }
        $num = (int) $val;
        if ($num < $min || $num > $max) {
            $this->errors[$field] = "{$label} must be between {$min} and {$max}.";
        }
    }

    /**
     * @param string $field
     * @param string $label
     * @param array<int, string> $allowed
     */
    public function inList(string $field, string $label, array $allowed): void
    {
        $val = $this->value($field);
        if ($val !== '' && !in_array($val, $allowed, true)) {
            $this->errors[$field] = "{$label} is invalid.";
        }
    }

    /**
     * Validate end_time is after start_time.
     *
     * @param string $startField
     * @param string $endField
     */
    public function timeAfter(string $startField, string $endField): void
    {
        $start = $this->value($startField);
        $end = $this->value($endField);
        if ($start !== '' && $end !== '' && $end <= $start) {
            $this->errors[$endField] = 'End time must be after start time.';
        }
    }
}
