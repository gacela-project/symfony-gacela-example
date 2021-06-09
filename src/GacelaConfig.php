<?php

declare(strict_types=1);

namespace App;

use RuntimeException;

final class GacelaConfig
{
    /**
     * This file config/local.php could be ignore in your project, and it will be read the last one
     * so it will override every possible value.
     */
    private const CONFIG_LOCAL_FILENAME = 'local.php';

    private static $applicationRootDir = '';
    /** @var array */
    private static $config = [];
    /** @var $this|null */
    private static $instance = null;

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function setApplicationRootDir(string $dir): void
    {
        self::$applicationRootDir = $dir;
    }

    /**
     * @param mixed|null $default
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        if (empty(self::$config)) {
            self::init();
        }

        if ($default !== null && !self::hasValue($key)) {
            return $default;
        }

        if (!self::hasValue($key)) {
            throw new RuntimeException(sprintf(
                'Could not find config key "%s" in "%s"',
                $key,
                self::class
            ));
        }

        return self::$config[$key];
    }

    public static function hasValue(string $key): bool
    {
        return isset(self::$config[$key]);
    }

    public static function init(): void
    {
        self::$config = self::readConfigFromFile();
    }

    private static function readConfigFromFile(): array
    {
        $fileName = self::getApplicationRootDir() . '/config/' . self::CONFIG_LOCAL_FILENAME;

        if (file_exists($fileName)) {
            return include $fileName;
        }

        return [];
    }

    public static function getApplicationRootDir(): string
    {
        if (empty(self::$applicationRootDir)) {
            self::$applicationRootDir = getcwd() ?: '';
        }

        return self::$applicationRootDir;
    }
}
