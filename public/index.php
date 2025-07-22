<?php

use Core\ValidationException;

const BASE_PATH = __DIR__ . '/../';

require BASE_PATH . './vendor/autoload.php';

require BASE_PATH . 'core/functions.php';

$router = new \Core\Router();

$routes = require BASE_PATH . 'routes/api.php';

$uri = getUri();

$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

try {
    $router->route($uri, $method);
} catch (ValidationException $exception) {

}
