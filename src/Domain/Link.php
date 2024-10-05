<?php declare(strict_types = 1);

namespace App\Domain;

class Link {
    public string $href;
    public string $method;
    public string $description;

    public function __construct(string $href, string $method = 'GET', string $description = 'See the documentation') {
        $this->href = $href;
        $this->method = $method;
        $this->description = $description;
    }
}