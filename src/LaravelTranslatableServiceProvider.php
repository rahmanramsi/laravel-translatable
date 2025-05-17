<?php

namespace RahmanRamsi\LaravelTranslatable;

use RahmanRamsi\LaravelTranslatable\Commands\LaravelTranslatableCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelTranslatableServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-translatable')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_translatable_table');
    }
}
