# Laravel-Translatable

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rahmanramsi/laravel-translatable.svg?style=flat-square)](https://packagist.org/packages/rahmanramsi/laravel-translatable)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/rahmanramsi/laravel-translatable/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/rahmanramsi/laravel-translatable/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/rahmanramsi/laravel-translatable/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/rahmanramsi/laravel-translatable/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/rahmanramsi/laravel-translatable.svg?style=flat-square)](https://packagist.org/packages/rahmanramsi/laravel-translatable)

Laravel Translatable is a package for easily attaching arbitrary translation data to Eloquent Models.

## Features
- One-to-many polymorphic relationship allows attaching translated data to Eloquent models without needing to adjust the database schema.

<!-- ## Example Usage
Attach some translation to an eloquent model

```php
$post = Post::create($this->request->input());
$post->setTranslation('title', 'en', 'Post Title');
```

Query the model by its translation

```php
$post = Post::whereTranslation('title', 'en', 'Post Title');
```

Retrieve the translation from a model
```php
$title = $post->getTranslation('title', 'en'
or 

$title = $post->title;
``` -->

## Installation & Setup

You can install the package via composer:

```bash
composer require rahmanramsi/laravel-translatable
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="translatable-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="translatable-config"
```

This is the contents of the published config file:

```php
return [
    'model' => Translate::class,
];
```

### Making a model translatable
The required steps to make a model translatable are:

- First, you need to add the `RahmanRamsi\LaravelTranslatable\HasTranslations` trait.
- Next, you should create a public property $translatable which holds an array with all the names of attributes you wish to make translatable.

Here's an example of a prepared model:

```php
use Illuminate\Database\Eloquent\Model;
use RahmanRamsi\LaravelTranslatable\HasTranslations;

class Post extends Model
{
    use HasTranslations;

    public array $translatable = ['title'];
}
```

## Basic Usage

### Getting and setting translations
First, you must prepare your model as instructed in the [installation instructions](#making-a-model-translatable).

#### Setting a translation

Here's an example, given that name is a translatable attribute:
```php
$post->title = 'hello';
```
Changes automatically saved

To set a translation for a specific locale you can use this method:

```php
public function setTranslation(string $key, string $locale, string $value)
```
You can set translations for multiple languages with
```php
$translations = ['en' => 'hello', 'es' => 'hola'];

$post->setTranslations('title', $translations);
```

#### Getting a translation
The easiest way to get a translation for the current locale is to just get the property for the translated attribute. For example (given that name is a translatable attribute):

```php
$post->title;
```
You can also use this method:
```php
public function getTranslation(string $key, string $locale, bool $useFallbackLocale = true)
```
##### Getting all translations
You can get all translations by calling getTranslations() without an argument:

```php
$post->getTranslations();
```
Or you can use the accessor:

```php
$post->translations
```
The methods above will give you back an array that holds all translations, for example:
```php
$post->getTranslations('title'); 
// returns ['en' => 'hello', 'es' => 'hola']
```

The method above returns, all locales. If you only want specific locales, pass that to the second argument of `getTranslations`.

```php
public function getTranslations(string $attributeName)
```
Here's an example:
```php
$translations = [
    'en' => 'Hello',
    'fr' => 'Bonjour',
    'de' => 'Hallo',
];

$newsItem->setTranslations('hello', $translations);
$newsItem->getTranslations('hello', ['en', 'fr']); // returns ['en' => 'Hello', 'fr' => 'Bonjour']
```
#### Get locales that a model has
You can get all locales that a model has by calling locales() without an argument:

```php
$translations = ['en' => 'hello', 'es' => 'hola'];
$post->name = $translations;

$post->locales(); // returns ['en', 'es']
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Rahman Ramsi](https://github.com/rahmanramsi)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
