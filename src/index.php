<?php declare(strict_types = 1);

header("Access-Control-Allow-Origin: *"); // Permitir todas as origens (ou especifique a origem desejada)
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT, PATCH"); // Métodos permitidos
header("Access-Control-Allow-Headers: Content-Type, Authorization"); 
date_default_timezone_set("America/Sao_Paulo");

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Route;

$requestURI = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$requestMethod = $_SERVER['REQUEST_METHOD'];

$route = new Route();
require_once __DIR__ . '/Routes/routes.php';

$route->call($requestURI, $requestMethod);
