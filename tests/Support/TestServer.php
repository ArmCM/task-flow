<?php

declare(strict_types=1);

namespace Tests\Support;

use RuntimeException;

/**
 * Boots `php -S` once for the whole suite and keeps it alive.
 *
 * APP_ENV is passed through the child's real environment (not $_ENV), which is
 * what makes config/database.php resolve the test database inside the server
 * process. Errors are sent to a log file instead of the response body so a PHP
 * warning never corrupts the JSON a test is parsing.
 */
final class TestServer
{
    private const HOST = '127.0.0.1';

    private static ?self $instance = null;

    /** @var resource */
    private $process;

    private string $errorLog;

    private int $logOffset = 0;

    private function __construct(private int $port)
    {
        $this->errorLog = tempnam(sys_get_temp_dir(), 'taskflow-server-') ?: '/dev/null';

        $command = [
            PHP_BINARY,
            '-d', 'display_errors=0',
            '-d', 'log_errors=1',
            '-d', 'error_log=' . $this->errorLog,
            '-S', self::HOST . ':' . $this->port,
            '-t', 'public',
        ];

        $process = proc_open(
            $command,
            [1 => ['file', '/dev/null', 'w'], 2 => ['file', $this->errorLog, 'a']],
            $pipes,
            BASE_PATH,
            ['APP_ENV' => 'testing'] + getenv()
        );

        if (!is_resource($process)) {
            throw new RuntimeException('Unable to start the test server.');
        }

        $this->process = $process;

        $this->waitUntilReady();
    }

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self(self::freePort());

            register_shutdown_function(static fn () => self::$instance?->stop());
        }

        return self::$instance;
    }

    public function baseUri(): string
    {
        return 'http://' . self::HOST . ':' . $this->port;
    }

    /** Anything the server logged since the last forget() call. */
    public function newErrorOutput(): string
    {
        clearstatcache(true, $this->errorLog);

        $contents = (string) @file_get_contents($this->errorLog);

        return trim(substr($contents, $this->logOffset));
    }

    public function forgetErrorOutput(): void
    {
        clearstatcache(true, $this->errorLog);

        $this->logOffset = (int) @filesize($this->errorLog);
    }

    public function stop(): void
    {
        if (is_resource($this->process)) {
            proc_terminate($this->process);
            proc_close($this->process);
        }

        @unlink($this->errorLog);

        self::$instance = null;
    }

    private function waitUntilReady(): void
    {
        $deadline = microtime(true) + 10;

        while (microtime(true) < $deadline) {
            $socket = @fsockopen(self::HOST, $this->port, $errno, $errstr, 0.2);

            if (is_resource($socket)) {
                fclose($socket);

                return;
            }

            usleep(100_000);
        }

        throw new RuntimeException(
            "The test server never came up on port {$this->port}.\n" . $this->newErrorOutput()
        );
    }

    private static function freePort(): int
    {
        $socket = @stream_socket_server('tcp://' . self::HOST . ':0', $errno, $errstr);

        if (!is_resource($socket)) {
            throw new RuntimeException("Unable to find a free port: $errstr");
        }

        $address = stream_socket_get_name($socket, false);

        fclose($socket);

        return (int) substr((string) $address, (int) strrpos((string) $address, ':') + 1);
    }
}
