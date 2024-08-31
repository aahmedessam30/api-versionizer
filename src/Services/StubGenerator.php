<?php

namespace Ahmedessam\ApiVersionizer\Services;

class StubGenerator
{
    public static function getStub($stub, $path = null): false|string
    {
        $stub = str($stub)->when(
            str($stub)->endsWith('.stub'),
            fn($stub) => str($stub)->before('.stub')
        )->value();

        $path = str($path)->when(
            str($path)->startsWith(base_path()),
            fn($path) => $path,
            fn($path) => str(base_path("$path/stubs"))
        )->value();

        if (!file_exists("$path/$stub.stub")) {
            $stub = self::defaultStub();
        }

        return file_get_contents("$path/$stub.stub");
    }

    public static function replaceStub($stub, $keys, $values): array|string
    {
        if (!is_array($keys)) {
            $keys = [$keys];
        }

        if (!is_array($values)) {
            $values = [$values];
        }

        return str_replace($keys, $values, $stub);
    }

    public static function saveStub($path, $stub): void
    {
        file_put_contents($path, $stub);
    }

    public static function generate($stub, $keys, $values, $path): void
    {
        $stub = self::getStub($stub);
        $stub = self::replaceStub($stub, $keys, $values);
        self::saveStub($path, $stub);
    }

    public static function isDefaultStub($stub): bool
    {
        return $stub === self::defaultStub() || $stub === file_get_contents(__DIR__ . '/../stubs/' . self::defaultStub() . '.stub');
    }

    private static function defaultStub(): string
    {
        return 'api-versionizer';
    }
}
