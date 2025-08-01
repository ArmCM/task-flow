<?php

use App\Exceptions\RouterException;
use Core\Router;

const BASE_PATH = __DIR__ . '/../';

require BASE_PATH . './vendor/autoload.php';

require BASE_PATH . 'core/helpers/functions.php';

require BASE_PATH . 'bootstrap/app.php';

$router = new Router();

$routes = require BASE_PATH . 'routes/api.php';

$uri = getUri();

$method = getRequestMethod();
if (in_array($_SERVER['REQUEST_METHOD'], ['PATCH', 'PUT', 'DELETE'])) {
    parse_str(file_get_contents("php://input"), $_POST);
}

try {
    $router->route($uri, $method);
} catch (RouterException $exception) {
    jsonEncode(data: $exception->getMessage(), status: $exception->getCode());
}
