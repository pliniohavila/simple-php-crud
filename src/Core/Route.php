<?php declare(strict_types = 1);

namespace App\Core;

use Exception;
use App\Traits\HttpResponse;

if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool {
        return strlen($needle) === 0 || strpos($haystack, $needle) === 0;
    }
}

class Route 
{
    use HttpResponse;
    
    public $routes = [
        'GET' => [], 
        'POST' => [], 
        'PUT' => [], 
        'PATCH' => [], 
        'DELETE' => []
    ];
    public $params = "";

    public static function load(string $file): Route {
        $router = new static;
        require $file;
        return $router;
    }

    public function get(string $uri, array $controller) 
    {
        // Negative programming 
        if (str_starts_with($uri, '/')) {
            echo "Endpoints should not start with '/'";
            die;
        }
        $this->routes['GET'][$uri] = $controller;
    }

    public function post(string $uri, array $controller) 
    {
        $this->routes['POST'][$uri] = $controller;
    }

    public function put(string $uri, array $controller) 
    {
        $this->routes['PUT'][$uri] = $controller;
    }

    public function patch(string $uri, array $controller) 
    {
        $this->routes['PATCH'][$uri] = $controller;
    }

    public function delete(string $uri, array $controller) 
    {
        $this->routes['DELETE'][$uri] = $controller;
    }

    public function call(string $uri, string $requestType) 
    {

        if (!isset($this->routes[$requestType])) {
            echo "Método de request não encontrado: $requestType" . PHP_EOL;
            die;
        }

        foreach($this->routes[$requestType] as $key => $value) {
  
            if ($uri == $key || $this->compareStrings($uri, $key)) {
                if (is_array($this->routes[$requestType][$key])) {
                    return $this->callAction(
                        // Study about the functions bellow
                        reset($this->routes[$requestType][$key]), 
                        end($this->routes[$requestType][$key])
                    );
                } else {
                    echo "Formato inválido para a rota: $key" . PHP_EOL;
                    die;
                }
            }
        }
        $this->errorResponse(404, 'Route Not Found 😢', 'Route Not Found.');
    }

    protected function compareStrings(string $url, string $uri) 
    {
        $urlPattern = $uri;
        $regexPattern = '/^' . str_replace('\{id\}', '(.+)', preg_quote($urlPattern, '/'))  . '$/';

        if (!preg_match($regexPattern, $url, $matches))
            return false;
        $this->params = $matches[1];

        return true;
    }

    protected function callAction(string $controller, string $action) 
    {
        $controller = "App\Controllers\\{$controller}";
        $controller = new $controller;
        
        if (!method_exists($controller, $action)) {
            throw new Exception(
                "{$controller} does not have the {$action} method."
            );
        }

        if ($this->params == "")
            return $controller->$action();

        return $controller->$action($this->params);
    }
}