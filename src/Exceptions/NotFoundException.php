<?php

namespace App\Exceptions;

class NotFoundException extends \Exception
{
    public static function routeNotFound(string $field): self 
    {
        return new self($field, 404);
    }

    public static function taskNotFoundById($id): self
    {
        return new self($id, 404);
    }
}