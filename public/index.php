<?php

use App\Http\Middlewares\AuthMiddleware;
use Core\App;
use Core\Middleware;
use Core\Request;
use Core\Response;
use Core\Router;
use Dotenv\Dotenv;

const BASE_PATH = __DIR__ . '/../';

require BASE_PATH . './vendor/autoload.php';

require BASE_PATH . 'core/helpers/functions.php';

require BASE_PATH . 'bootstrap/app.php';

$dotenv = Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

$router = new Router();

$routes = require BASE_PATH . 'routes/api.php';

$request = App::resolve(Request::class);

$middleware = new Middleware();
$middleware->add(new AuthMiddleware());

$finalResponse = function (Request $request) use ($router): Response {
    return $router->route($request->path(), $request->method());
};

try {
    $middleware->process($request, $finalResponse);
} catch (Exception $exception) {
    Response::json(data: $exception->getMessage(), status: $exception->getCode());
}
