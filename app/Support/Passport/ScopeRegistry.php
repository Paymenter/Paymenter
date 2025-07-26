<?php

namespace App\Support\Passport;

class ScopeRegistry
{
    protected static array $scopes = [
        'profile' => 'View your profile',
    ];

    public static function add(string $scope, string $description): void
    {
        if (!array_key_exists($scope, static::$scopes)) {
            static::$scopes[$scope] = $description;
        }
    }

    public static function addMany(array $scopes): void
    {
        foreach ($scopes as $key => $desc) {
            static::add($key, $desc);
        }
    }

    public static function getAll(): array
    {
        return static::$scopes;
    }
}
