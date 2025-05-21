<?php

namespace RahmanRamsi\LaravelTranslatable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use RahmanRamsi\LaravelTranslatable\Exceptions\AttributeIsNotTranslatable;

trait HasTranslations
{
    /**
     * @var Collection|Translate[]
     */
    private $indexedTranslateCollection;

    protected ?string $translationLocale = null;

    public function initializeHasTranslations(): void {}

    /**
     * Relationship to the `Translate` model.
     */
    public function translate(): MorphMany
    {
        return $this->morphMany($this->getTranslateClassName(), 'translatable');
    }

    /**
     * Retrieve the FQCN of the class to use for Meta models.
     *
     * @return class-string<Meta>
     */
    protected function getTranslateClassName(): string
    {
        return config('translatable.model', Translate::class);
    }

    public static function usingLocale(string $locale): self
    {
        return (new self)->setLocale($locale);
    }

    public function getLocale(): string
    {
        return $this->translationLocale ?: app()->getLocale();
    }

    public function setLocale(string $locale): self
    {
        $this->translationLocale = $locale;

        return $this;
    }

    public function useFallbackLocale(): bool
    {
        if (property_exists($this, 'useFallbackLocale')) {
            return $this->useFallbackLocale;
        }

        return true;
    }

    protected function normalizeLocale(string $key, string $locale, bool $useFallbackLocale): string
    {
        $translatedLocales = $this->getTranslatedLocales($key);

        if (in_array($locale, $translatedLocales)) {
            return $locale;
        }

        if (! $useFallbackLocale) {
            return $locale;
        }

        if (method_exists($this, 'getFallbackLocale')) {
            $fallbackLocale = $this->getFallbackLocale();
        }

        $fallbackLocale ??= config('app.fallback_locale');

        if (! is_null($fallbackLocale) && in_array($fallbackLocale, $translatedLocales)) {
            return $fallbackLocale;
        }

        if (! empty($translatedLocales)) {
            return $translatedLocales[0];
        }

        return $locale;
    }

    public function getTranslation(string $key, string $locale, bool $useFallbackLocale = true)
    {
        $normalizedLocale = $this->normalizeLocale($key, $locale, $useFallbackLocale);

        $translation = null;

        if ($this->hasTranslation($key, $normalizedLocale)) {
            $translation = $this->getTranslateRecord($key, $normalizedLocale)->getAttribute('value');
        }

        // TODO fallback locale
        if (! $translation && $useFallbackLocale) {
        }

        return $translation;
    }

    public function getTranslations(?string $key = null)
    {
        if ($key !== null) {
            $this->guardAgainstNonTranslatableAttribute($key);

            return $this->getTranslateCollection()->filter(fn ($item) => $item->key == $key)->mapWithKeys(fn ($item) => [$item->locale => $item->value])->toArray();
        }

        return $this->translations;
    }

    public function translations(): Attribute
    {
        return Attribute::get(function () {
            return collect($this->getTranslatableAttributes())
                ->mapWithKeys(fn (string $key) => [$key => $this->getTranslations($key)])
                ->toArray();
        });
    }

    public function getTranslatedLocales(string $key): array
    {
        return array_keys($this->getTranslations($key));
    }

    /**
     * Retrieve the `Translate` model instance attached to a given key.
     */
    public function getTranslateRecord(string $key, string $locale): ?Translate
    {
        return $this->getTranslateCollection()->get($this->indexKey($key, $locale));
    }

    public function hasTranslation(string $key, string $locale)
    {
        return $this->getTranslateCollection()->has($this->indexKey($key, $locale));
    }

    public function forgetTranslation(string $key, string $locale): self
    {
        if ($this->hasTranslation($key, $locale)) {
            $this->getTranslateCollection()->pull($this->indexKey($key, $locale))->delete();
        }

        return $this;
    }

    public function indexKey(string $key, string $locale): string
    {
        return $key.'_'.$locale;
    }

    public function setTranslation(string $key, string $locale, ?string $value): self
    {
        $this->guardAgainstNonTranslatableAttribute($key);

        $this->translate()->updateOrCreate(
            ['key' => $key, 'locale' => $locale],
            ['value' => $value],
        );

        return $this;
    }

    public function setTranslations(string $key, array $translations)
    {
        $this->guardAgainstNonTranslatableAttribute($key);

        if (! empty($translations)) {
            foreach ($translations as $locale => $translation) {
                $this->setTranslation($key, $locale, $translation);
            }
        }

        return $this;
    }

    protected function guardAgainstNonTranslatableAttribute(string $key): void
    {
        if (! $this->isTranslatableAttribute($key)) {
            throw AttributeIsNotTranslatable::make($key, $this);
        }
    }

    public function isTranslatableAttribute(string $key): bool
    {
        return in_array($key, $this->getTranslatableAttributes());
    }

    public function getTranslatableAttributes(): array
    {
        return is_array($this->translatable)
            ? $this->translatable
            : [];
    }

    /**
     * fetch all translate for the model, if necessary.
     *
     * @return mixed
     */
    private function getTranslateCollection()
    {
        // load meta relation if not loaded.
        if (! $this->relationLoaded('translate')) {
            $this->setRelation('translate', $this->translate()->get());
        }

        // reindex by key for quicker lookups if necessary.
        if ($this->indexedTranslateCollection === null) {
            $this->indexedTranslateCollection = $this->translate->keyBy(fn (Translate $item, int $key) => $item->key.'_'.$item->locale);
        }

        return $this->indexedTranslateCollection;
    }

    /**
     * Translatable Attributes
     */
    public function getAttribute($key): mixed
    {
        if (! $this->isTranslatableAttribute($key)) {
            return parent::getAttribute($key);
        }

        return $this->getTranslation($key, $this->getLocale(), $this->useFallbackLocale());
    }

    public function setAttribute($key, $value)
    {
        if (! $this->isTranslatableAttribute($key)) {
            parent::setAttribute($key, $value);

            return;
        }

        $this->setTranslation($key, $this->getLocale(), $value);
    }

    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            if ($this->isTranslatableAttribute($key)) {
                $this->setTranslation($key, $this->getLocale(), $value);
                unset($attributes[$key]);
            }
        }

        parent::fill($attributes);
    }

    public function scopeWhereLocale(Builder $query, string $key, $operator, ?string $locale = null): void
    {
        // Shift arguments if no operator is present.
        if (! isset($locale)) {
            $locale = $operator;
            $operator = '=';
        }

        $query->whereHas('translate', function (Builder $q) use ($key, $operator, $locale) {
            $q->where('key', $key);
            $q->where('locale', $operator, $locale);
        });
    }
}
