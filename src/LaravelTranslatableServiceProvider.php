<?php

namespace RahmanRamsi\LaravelTranslatable;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use RahmanRamsi\LaravelTranslatable\Commands\LaravelTranslatableCommand;

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
            ->hasMigration('create_laravel_translatable_table')
            ->hasCommand(LaravelTranslatableCommand::class);
    }
}
