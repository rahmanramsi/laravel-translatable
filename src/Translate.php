<?php

namespace RahmanRamsi\LaravelTranslatable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Translate extends Model
{
    public $timestamps = false;

    protected $table = 'translate';

    protected $fillable = ['key', 'value', 'locale'];

    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }
}
