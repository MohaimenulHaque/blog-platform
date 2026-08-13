<?php

namespace Tests\Feature;

use Tests\TestCase;

class DebugEnvTest extends TestCase
{
    public function test_debug_environment(): void
    {
        fwrite(STDERR, PHP_EOL . 'APP_ENV getenv: ' . var_export(getenv('APP_ENV'), true) . PHP_EOL);
        fwrite(STDERR, 'env $_ENV: ' . var_export($_ENV['APP_ENV'] ?? null, true) . PHP_EOL);
        fwrite(STDERR, 'env $_SERVER: ' . var_export($_SERVER['APP_ENV'] ?? null, true) . PHP_EOL);
        fwrite(STDERR, 'app->environment(): ' . var_export(app()->environment(), true) . PHP_EOL);
        fwrite(STDERR, 'container env: ' . var_export(app()['env'], true) . PHP_EOL);
        fwrite(STDERR, 'runningUnitTests: ' . var_export(app()->runningUnitTests(), true) . PHP_EOL);
        fwrite(STDERR, 'runningInConsole: ' . var_export(app()->runningInConsole(), true) . PHP_EOL);

        $this->assertTrue(true);
    }
}
