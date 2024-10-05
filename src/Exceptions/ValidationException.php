<?php declare(strict_types = 1);

namespace App\Exceptions;

class ValidationException extends \Exception
{
    public static function missingField(string $field): self {
        $message = sprintf("The '%s' is required and must be a non-empty string.", $field);
        return new self($message, 400);
    }

    public static function missingFieldToUpdate(): self {
        $message = sprintf("To update, you need to send a valid 'title' ou 'description' or both.");
        return new self($message, 400);
    }
}