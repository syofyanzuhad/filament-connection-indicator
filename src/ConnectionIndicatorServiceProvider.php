<?php

namespace Syofyanzuhad\ConnectionIndicator;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ConnectionIndicatorServiceProvider extends PackageServiceProvider
{
    public static string $name = 'connection-indicator';

    public static string $viewNamespace = 'connection-indicator';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasViews(static::$viewNamespace);
    }
}
