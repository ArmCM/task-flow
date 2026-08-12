<?php

use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

const BASE_PATH = __DIR__ . '/../';

require BASE_PATH . 'core/helpers/functions.php';

Dotenv::createImmutable(BASE_PATH)->safeLoad();

// The suite spawns `php -S` as a child process, which reads APP_ENV from the
// real environment. phpunit.xml's <env> only reaches this process, so export it.
putenv('APP_ENV=testing');
