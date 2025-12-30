<?php

namespace App\Core;

class PermissionVersion
{
    private const DEFAULT_VERSION = 1;
    private const CACHE_FILE = '/storage/cache/permission_version.cache';

    public static function current(): int
    {
        $version = self::readVersion();

        if ($version !== null) {
            return $version;
        }

        return self::writeVersion(self::DEFAULT_VERSION);
    }

    public static function bump(): int
    {
        $next = self::current() + 1;

        return self::writeVersion($next);
    }

    private static function readVersion(): ?int
    {
        $path = self::path();

        if (!file_exists($path)) {
            return null;
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            return null;
        }

        $version = (int) trim($contents);

        return $version > 0 ? $version : null;
    }

    private static function writeVersion(int $version): int
    {
        self::ensureCacheDirectory();
        file_put_contents(self::path(), (string) $version, LOCK_EX);

        return $version;
    }

    private static function ensureCacheDirectory(): void
    {
        $directory = dirname(self::path());

        if (is_dir($directory)) {
            return;
        }

        mkdir($directory, 0777, true);
    }

    private static function path(): string
    {
        return BASE_PATH . self::CACHE_FILE;
    }
}
