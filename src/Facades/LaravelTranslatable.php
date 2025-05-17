<?php

namespace RahmanRamsi\LaravelTranslatable\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \RahmanRamsi\LaravelTranslatable\LaravelTranslatable
 */
class LaravelTranslatable extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \RahmanRamsi\LaravelTranslatable\LaravelTranslatable::class;
    }
}
