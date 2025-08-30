<?php

use App\Exceptions\RouterException;
use Core\App;
use Core\Request;
use Core\Response;
use Core\Router;

const BASE_PATH = __DIR__ . '/../';

require BASE_PATH . './vendor/autoload.php';

require BASE_PATH . 'core/helpers/functions.php';

require BASE_PATH . 'bootstrap/app.php';

$router = new Router();

$routes = require BASE_PATH . 'routes/api.php';

$request = App::resolve(Request::class);

try {
    $router->route($request->path(), $request->method());
} catch (RouterException|Exception $exception) {
    Response::json(data: $exception->getMessage(), status: $exception->getCode());
}
