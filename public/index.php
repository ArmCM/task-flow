<?php

use App\Exceptions\ValidationException;
use Core\Router;

const BASE_PATH = __DIR__ . '/../';

require BASE_PATH . './vendor/autoload.php';

require BASE_PATH . 'core/functions.php';

$router = new Router();

$routes = require BASE_PATH . 'routes/api.php';

$uri = getUri();

$method = getRequestMethod();

try {
    $router->route($uri, $method);
} catch (ValidationException $exception) {

}
