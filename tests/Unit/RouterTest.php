<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\RouterException;
use Core\App;
use Core\Container;
use Core\Router;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Router::dispatch() calls App::resolve() and only falls back to
        // `new $class()` when the container throws, so a container must exist.
        App::setContainer(new Container());
    }

    #[Test]
    public function itDispatchesToTheMatchingController(): void
    {
        $router = new Router();
        $router->get('/tasks', [RouterTestController::class, 'index']);

        $this->assertSame('index', $router->route('/tasks', 'GET'));
    }

    #[Test]
    public function itPassesUriSegmentsAsPositionalArguments(): void
    {
        $router = new Router();
        $router->get('/tasks/{id}', [RouterTestController::class, 'show']);

        $this->assertSame('show:42', $router->route('/tasks/42', 'GET'));
    }

    #[Test]
    public function itMatchesOnMethodAsWellAsUri(): void
    {
        $router = new Router();
        $router->post('/tasks', [RouterTestController::class, 'index']);

        $this->expectException(RouterException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('Route not found for [GET] /tasks');

        $router->route('/tasks', 'GET');
    }

    #[Test]
    public function itThrowsA404ForAnUnknownUri(): void
    {
        $router = new Router();
        $router->get('/tasks', [RouterTestController::class, 'index']);

        $this->expectException(RouterException::class);
        $this->expectExceptionCode(404);

        $router->route('/unknown', 'GET');
    }

    #[Test]
    public function itThrowsA500WhenTheControllerMethodIsMissing(): void
    {
        $router = new Router();
        $router->get('/tasks', [RouterTestController::class, 'missingMethod']);

        $this->expectException(RouterException::class);
        $this->expectExceptionCode(500);
        $this->expectExceptionMessage('Controller or method not found');

        $router->route('/tasks', 'GET');
    }

    #[Test]
    public function aPlaceholderDoesNotMatchAcrossSegments(): void
    {
        $router = new Router();
        $router->get('/tasks/{id}', [RouterTestController::class, 'show']);

        $this->expectException(RouterException::class);

        $router->route('/tasks/42/comments', 'GET');
    }
}

final class RouterTestController
{
    public function index(): string
    {
        return 'index';
    }

    public function show(string $id): string
    {
        return "show:$id";
    }
}
