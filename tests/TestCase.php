<?php

namespace Tests;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }
}
