<?php

namespace App\Tests\Behat\Persistence;

class BehatVariablesDatabase
{
    /**
     * @var array
     */
    protected static $storage = [];

    public static function reset(): void
    {
        self::$storage = [];
    }

    public static function set(string $key, mixed $data): void
    {
        self::$storage[$key] = $data;
    }

    /**
     * @return mixed
     */
    public static function get(string $key)
    {
        return self::$storage[$key] ?? null;
    }
}
