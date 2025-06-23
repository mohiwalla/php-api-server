<?php

const MIN_FILE_SIZE = 0;
define('MAX_FILE_SIZE', 2 * 1024 * 1024);
define('MIME_TYPES', ['image/png', 'image/jpeg', 'image/jpg', 'application/pdf']);

/**
 * zod: A flexible schema validation library.
 */
class zod
{
    /**
     * @var array Holds the schema definitions for fields.
     */
    private $schema = [];

    /**
     * Dynamically sets properties on the class instance.
     *
     * @param string $name The name of the property.
     * @param mixed $value The value of the property.
     */
    public function __set(string $name, mixed $value)
    {
        $this->$name = $value;
    }

    /**
     * Defines a validation rule for a field.
     *
     * @param string $name The name of the field.
     * @param callable $validator The validation function for the field.
     * @param string|null $invalidMessage Message to return if validation fails.
     * @param string|null $requiredMessage Message to return if field is required but not present.
     * @return $this
     */
    public function field(string $name, ?callable $validator = null, string|null $invalidMessage = null, string|null $requiredMessage = null)
    {
        if (!$validator) {
            $validator = function () {
                $result = new stdClass();
                $result->ok = true;

                return $result;
            };
        }

        $this->schema[] = [$name, $validator, $invalidMessage, $requiredMessage];
        return $this;
    }

    /**
     * Validates input data against the defined schema.
     *
     * @param array $data The input data to validate.
     * @return stdClass Result object indicating success or failure.
     */
    public function parse(array $data)
    {
        $result = new stdClass();
        $result->ok = true;
        $result->error = null;
        $result->data = new stdClass();

        foreach ($this->schema as $config) {
            [$key, $validator, $invalidMessage, $requiredMessage] = $config;

            $value = $data[$key] ?? null;

            if ($value === null) {
                $result->ok = false;
                $result->error = $requiredMessage ?? $invalidMessage ?? "Please enter a valid $key to proceed further.";
                break;
            }

            $response = $validator($value);

            if (!$response->ok) {
                $result->ok = false;
                $result->error = $invalidMessage ?? "⚠️ Error, $key {$response->text}" ?? "Please enter a valid $key to proceed further.";
                break;
            }

            $result->data->$key = $value;
        }

        return $result;
    }

    /**
     * Returns a validator function that checks if a value is a string.
     *
     * @return callable
     */
    public function string()
    {
        return function ($value) {
            $result = new stdClass();
            $result->ok = is_string($value);
            $result->text = $result->ok ? '' : 'must be a string.';

            return $result;
        };
    }

    /**
     * Returns a validator function that checks if a value is numeric.
     *
     * @return callable
     */
    public function number()
    {
        return function ($value) {
            $result = new stdClass();
            $result->ok = is_numeric($value);
            $result->text = $result->ok ? '' : 'must be a number.';

            return $result;
        };
    }

    /**
     * Returns a validator function that checks if a value is an array and optionally validates each element.
     *
     * @param callable|null $function Optional validator function for each element in the array.
     * @return callable
     */
    public function array(?callable $function = null)
    {
        return function ($value) use ($function) {
            $result = new stdClass();
            $result->ok = is_array($value);
            if (!$result->ok) {
                $result->text = "Invalid input. Expected an array.";
                return $result;
            }

            if ($function) {
                foreach ($value as $item) {
                    $res = $function($item);
                    if (!$res->ok) {
                        $result->ok = false;
                        $result->text = "Invalid array element.";
                        return $result;
                    }
                }
            }

            return $result;
        };
    }

    /**
     * Returns a validator function that checks if a value is a valid email address.
     *
     * @return callable
     */
    public function email()
    {
        return function ($value) {
            $result = new stdClass();
            $result->ok = filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
            $result->text = $result->ok ? '' : 'invalid email address.';

            return $result;
        };
    }

    /**
     * Returns a validator function that checks if a value is a valid URL.
     *
     * @return callable
     */
    public function url()
    {
        return function ($value) {
            $result = new stdClass();
            $result->ok = filter_var($value, FILTER_VALIDATE_URL) !== false;
            $result->text = $result->ok ? '' : 'invalid URL.';

            return $result;
        };
    }

    /**
     * Returns a validator function that checks if a value matches a given regular expression.
     *
     * @param string $regex The regular expression to match against.
     * @return callable
     */
    public function regex(string $regex)
    {
        return function ($value) use ($regex) {
            $result = new stdClass();
            $result->ok = preg_match($regex, $value);
            $result->text = $result->ok ? '' : 'invalid format.';

            return $result;
        };
    }

    /**
     * Returns a validator function that checks if a value is a valid base64 encoded string.
     *
     * @return callable
     */
    public function base64()
    {
        return function ($value) {
            $result = new stdClass();
            $result->ok = base64_decode($value, true) !== false;
            $result->text = $result->ok ? '' : 'invalid base64 string.';

            return $result;
        };
    }

    /**
     * Returns a validator function that checks if a value is a valid JSON string.
     *
     * @return callable
     */
    public function json()
    {
        return function ($value) {
            $result = new stdClass();
            $result->ok = json_decode($value) !== null;
            $result->text = $result->ok ? '' : 'invalid JSON string.';

            return $result;
        };
    }

    /**
     * Returns a validator function that checks if a value is a valid IP address.
     *
     * @return callable
     */
    public function ip()
    {
        return function ($value) {
            $result = new stdClass();
            $result->ok = filter_var($value, FILTER_VALIDATE_IP) !== false;
            $result->text = $result->ok ? '' : 'invalid IP address.';

            return $result;
        };
    }

    /**
     * Returns a validator function that checks if a value is a valid UUID.
     *
     * @return callable
     */
    public function uuid()
    {
        return function ($value) {
            $result = new stdClass();
            $result->ok = preg_match('/^[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12}$/i', $value);
            $result->text = $result->ok ? '' : 'invalid UUID.';

            return $result;
        };
    }

    /**
     * Returns a validator function that checks if a value is a valid date string.
     *
     * @return callable
     */
    public function date()
    {
        return function ($value) {
            $result = new stdClass();
            $result->ok = strtotime($value) !== false;
            $result->text = $result->ok ? '' : 'invalid date.';

            return $result;
        };
    }

    /**
     * Returns a validator function that checks if a value is a valid datetime string.
     *
     * @return callable
     */
    public function datetime()
    {
        return function ($value) {
            $result = new stdClass();
            $result->ok = strtotime($value) !== false;
            $result->text = $result->ok ? '' : 'invalid datetime.';

            return $result;
        };
    }

    /**
     * Returns a validator function that checks if a value is a valid time string.
     *
     * @return callable
     */
    public function time()
    {
        return function ($value) {
            $result = new stdClass();
            $result->ok = strtotime($value) !== false;
            $result->text = $result->ok ? '' : 'invalid time.';

            return $result;
        };
    }

    /**
     * Returns a validator function that checks if a value is a boolean.
     *
     * @return callable
     */
    public function boolean()
    {
        return function ($value) {
            $result = new stdClass();
            $result->ok = is_bool($value);
            $result->text = $result->ok ? '' : 'must be a boolean.';

            return $result;
        };
    }

    /**
     * Returns a validator function that checks if a string includes a given substring.
     *
     * @param string $includes The substring that should be included.
     * @return callable
     */
    public function includes(string $includes)
    {
        return function ($value) use ($includes) {
            $result = new stdClass();
            $result->ok = strpos($value, $includes) !== false;
            $result->text = $result->ok ? '' : 'must include "' . $includes . '".';

            return $result;
        };
    }

    /**
     * Returns a validator function that checks if a string starts with a given substring.
     *
     * @param string $startsWith The substring that should be at the start.
     * @return callable
     */
    public function startsWith(string $startsWith)
    {
        return function ($value) use ($startsWith) {
            $result = new stdClass();
            $result->ok = strpos($value, $startsWith) === 0;
            $result->text = $result->ok ? '' : 'must start with "' . $startsWith . '".';

            return $result;
        };
    }

    /**
     * Returns a validator function that checks if a string ends with a given substring.
     *
     * @param string $endsWith The substring that should be at the end.
     * @return callable
     */
    public function endsWith(string $endsWith)
    {
        return function ($value) use ($endsWith) {
            $result = new stdClass();
            $result->ok = substr($value, -strlen($endsWith)) === $endsWith;
            $result->text = $result->ok ? '' : 'must end with "' . $endsWith . '".';

            return $result;
        };
    }

    /**
     * Returns a validator function that checks if the length of a string is greater than or equal to a minimum length.
     *
     * @param int $minlength The minimum length.
     * @return callable
     */
    public function minlength(int $minlength)
    {
        return function ($value) use ($minlength) {
            $result = new stdClass();
            $result->ok = strlen($value) >= $minlength;
            $result->text = $result->ok ? '' : 'must be at least ' . $minlength . ' characters long.';

            return $result;
        };
    }

    /**
     * Returns a validator function that checks if the length of a string is less than or equal to a maximum length.
     *
     * @param int $maxlength The maximum length.
     * @return callable
     */
    public function maxlength(int $maxlength)
    {
        return function ($value) use ($maxlength) {
            $result = new stdClass();
            $result->ok = strlen($value) <= $maxlength;
            $result->text = $result->ok ? '' : 'must be at most ' . $maxlength . ' characters long.';

            return $result;
        };
    }

    /**
     * Returns a validator function that checks if a number is greater than or equal to a minimum value.
     *
     * @param int $min The minimum value.
     * @return callable
     */
    public function min(int $min)
    {
        return function ($value) use ($min) {
            $result = new stdClass();
            $result->ok = $value >= $min;
            $result->text = $result->ok ? '' : "Must be at least $min.";

            return $result;
        };
    }

    /**
     * Returns a validator function that checks if a number is less than or equal to a maximum value.
     *
     * @param int $max The maximum value.
     * @return callable
     */
    public function max(int $max)
    {
        return function ($value) use ($max) {
            $result = new stdClass();
            $result->ok = $value <= $max;
            $result->text = $result->ok ? '' : "Must be at most $max.";

            return $result;
        };
    }

    public function enum(...$values)
    {
        return function ($value) use ($values) {
            $result = new stdClass();
            $result->ok = in_array($value, $values);
            $result->text = "Please choose either " . implode(', ', $values) . '.';

            return $result;
        };
    }

    public function phone()
    {
        return function ($value) {
            $result = new stdClass();
            $result->ok = preg_match('/^\+?[0-9\s\-()]{7,15}$/', $value);
            $result->text = $result->ok ? '' : 'invalid phone number. Must contain 7-15 digits, optionally with spaces, dashes, or parentheses.';
            return $result;
        };
    }

    /**
     * Returns a validator function that checks if a file is valid based on size and MIME type.
     *
     * @param int $minSize The minimum file size in bytes.
     * @param int $maxSize The maximum file size in bytes.
     * @param array $mimeTypes The allowed MIME types.
     * @return callable
     */
    public function file(int $minSize = MIN_FILE_SIZE, int $maxSize = MAX_FILE_SIZE, array $mimeTypes = MIME_TYPES)
    {
        return function ($value) use ($minSize, $maxSize, $mimeTypes) {
            $result = new stdClass();

            if (!isset($value['error']) || $value['error'] > 0) {
                $result->ok = false;
                $result->text = 'The file is either corrupted or not uploaded properly.';
                return $result;
            }

            if ($value['size'] < $minSize) {
                $result->ok = false;
                $result->text = 'File must be more than ' . ($minSize / 1024 / 1024) . 'MB.';
                return $result;
            }

            if ($value['size'] > $maxSize) {
                $result->ok = false;
                $result->text = 'File must be under ' . ($maxSize / 1024 / 1024) . 'MB.';
                return $result;
            }

            if (!in_array($value['type'], $mimeTypes)) {
                $validFileTypes = implode(', ', array_map(function ($mimeType) {
                    return explode('/', $mimeType)[1];
                }, $mimeTypes));

                $result->ok = false;
                $result->text = "File must be one of the following types: $validFileTypes.";
                return $result;
            }

            $result->ok = true;
            return $result;
        };
    }
}
