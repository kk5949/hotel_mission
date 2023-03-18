<?php

namespace App\Exceptions;

use Exception;

class ReportableException extends Exception
{
    public array $additionalPrams;

    public function __construct($message = 'Something wrong.', $code = 400, $additionalPrams = [])
    {
        parent::__construct();
        $this->message = $message;
        $this->code = $code;
        $this->additionalPrams = $additionalPrams;
    }

    public function getAdditionalParams(): array
    {
        return $this->additionalPrams;
    }
}
