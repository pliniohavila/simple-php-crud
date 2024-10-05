<?php

namespace App\Exceptions;

class NotFoundException extends \Exception
{
    public static function routeNotFound(string $field): self 
    {
        $message = sprintf("The '%s' route not found", $field);
        return new self($message, 404);
    }

    public static function taskNotFoundById($id): self
    {
        $message = sprintf("Couldn't find a task with the ID: '%s' 🫠", (string)$id);
        return new self($id, 404);
    }
}