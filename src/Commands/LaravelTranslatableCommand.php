<?php

namespace RahmanRamsi\LaravelTranslatable\Commands;

use Illuminate\Console\Command;

class LaravelTranslatableCommand extends Command
{
    public $signature = 'laravel-translatable';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
