
[![GitHub release](https://img.shields.io/github/release/robinhoo1973/laravel-translation-helper.svg)]()
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/robinhoo1973/laravel-translation-helper/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/robinhoo1973/laravel-translation-helper/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/robinhoo1973/laravel-translation-helper/badges/build.png?b=master)](https://scrutinizer-ci.com/g/robinhoo1973/laravel-translation-helper/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/robinhoo1973/laravel-translation-helper/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)
![Language](https://img.shields.io/badge/language-php-orange.svg)
[![License](https://img.shields.io/packagist/l/topview-digital/laravel-translation-helper.svg)]()
[![Total Downloads](https://img.shields.io/packagist/dt/topview-digital/laravel-translation-helper.svg)](https://packagist.org/packages/topview-digital/laravel-translation-helper)
[![HitCount](http://hits.dwyl.io/robinhoo1973/https://github.com/robinhoo1973/laravel-translation-helper.svg)](http://hits.dwyl.io/robinhoo1973/https://github.com/robinhoo1973/laravel-translation-helper)
# Laravel Translation Helper


#### Localize the terms in your code and store translations in the tables or export to text files.

Implementations of inline translation for your strings required localization and archiving the translations int tables or exporting to text files, while you have google access and queue function enabled for default queue, it will help you to generate the other required languages automatically via google translation.
## Requirements

-   PHP >= 7.0
-   MySQL >= 5.7
-   [Laravel](https://laravel.com/) >= 5.6


## Installation

Require the package via Composer:

```
composer require topview-digital/laravel-translation-helper
```
Laravel will automatically register the [ServiceProvider](https://github.com/robinhoo1973/laravel-translation-helper/blob/master/src/TranslationHelperServiceProvider.php).

### Publish Package
After installation, please publish the assets by below commands
```
php artisan trans-helper:publish
```

### Configure Package
Please config your settings in config/trans-helper.php file, it should looks like below

```
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel-Translation-Helper Database Settings
    |--------------------------------------------------------------------------
    |
    | Here are database settings for Laravel-Translation-Helper builtin tables connction.
    |
    */

    'database' => [
        // Database connection for following tables.
        'connection' => '',

    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel-Translation-Helper Citing Settings
    |--------------------------------------------------------------------------
    |
    | Here are citing settings for Laravel-Translation-Helper.
    |
    */

    'cite' => [
        'enable' => true,
        'async' => true,

    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel-Translation-Helper Translation Settings
    |--------------------------------------------------------------------------
    |
    | Here are translating settings for Laravel-Translation-Helper trigger auto translation.
    |
    */

    // Translation mode setting
    'translation' => [
        'broker' => TopviewDigital\TranslationHelper\Service\GoogleTranslator::class,
        'mode' => 'auto',
    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel-Translation-Helper Exporting Settings
    |--------------------------------------------------------------------------
    |
    | Here are exporting settings for Laravel-Translation-Helper.
    |
    */

    // Exporting data config setting.
    'export' => [
        'path' => realpath(base_path('resources/lang')),
    ],
];

```

Once you confired your settings, you may run  install command to setup the tables for the package.
```
php artisan trans-helper:install
```

### Configure Queue
If you want use the auto translation feature, please also config your queue config file and .env file.
If you have enabled the queue feature for default queue, please skipp below instructions.
config/queue.php[example]
```
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Queue Connection Name
    |--------------------------------------------------------------------------
    |
    | Laravel's queue API supports an assortment of back-ends via a single
    | API, giving you convenient access to each back-end using the same
    | syntax for every one. Here you may define a default connection.
    |
    */

    'default' => env('QUEUE_CONNECTION', 'sync'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection information for each server that
    | is used by your application. A default configuration has been added
    | for each back-end shipped with Laravel. You are free to add more.
    |
    | Drivers: "sync", "database", "beanstalkd", "sqs", "redis", "null"
    |
    */

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver' => 'database',
            'table' => '_jobs',
            'queue' => 'default',
            'retry_after' => 90,
        ],

        'beanstalkd' => [
            'driver' => 'beanstalkd',
            'host' => 'localhost',
            'queue' => 'default',
            'retry_after' => 90,
            'block_for' => 0,
        ],

        'sqs' => [
            'driver' => 'sqs',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'prefix' => env('SQS_PREFIX', 'https://sqs.us-east-1.amazonaws.com/your-account-id'),
            'queue' => env('SQS_QUEUE', 'your-queue-name'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => env('REDIS_QUEUE', 'default'),
            'retry_after' => 90,
            'block_for' => null,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs
    |--------------------------------------------------------------------------
    |
    | These options configure the behavior of failed queue job logging so you
    | can control which database and table are used to store the jobs that
    | have failed. You may change them to any database / table you wish.
    |
    */

    'failed' => [
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => '_failed_jobs',
    ],

];

```
.env[queue section]
```
QUEUE_CONNECTION=database
```
After your configuration done, please ensure the your queue is up and running. Simple way is run 
```
php artisan queue:work --queue=translation &
php artisan queue:work --queue=cite &
```
and make it running all the time in the background.

### Mark the Cited Code Feature

You could follow up on the translation later by setup the web interface to fine-tuning the interpretations. However, sometimes it's difficult to recall where the terms are used. You can turn on the feature of cite, and it will help you to record the place you cited the term in your code.

### Define Your Own Translator

You can define your own translator by reference the code below, and set it in config.

```
<?php

namespace TopviewDigital\TranslationHelper\Service;

use Campo\UserAgent;
use Stichoza\GoogleTranslate\GoogleTranslate;
use TopviewDigital\TranslationHelper\Interfaces\TranslatorInterface;

class GoogleTranslator implements TranslatorInterface
{
    protected $break = 0;
    protected $called = 0;
    protected $word;
    protected $source_locale = null;
    protected $target_locale;

    public function __construct()
    {
        $this->target_locale = config('app.locale');
    }

    public function word(string $word)
    {
        $this->word = $word;
        return $this;
    }

    public function targetLocale(string $target_locale)
    {
        $this->target_locale = $target_locale;
        return $this;
    }

    public function sourceLocale(string $source_locale)
    {
        $this->source_locale = $source_locale;
        return $this;
    }

    public function translate()
    {
        $translated = '';
        $translator = new GoogleTranslate();
        try {
            $translated = is_null($this->source_locale)
                ? $translator
                ->setSource()
                ->setTarget($this->target_locale)
                ->translate($this->word)
                : $translator
                ->setSource($this->source_locale)
                ->setTarget($this->target_locale)
                ->translate($this->word);
        } catch (Exception $e) {
            return null;
        }
        return $translated;
    }
}
```

## Usage

For the following examples

### Translation

You can wrap your strings, NO parameters invovled, in helper function localize()

```
$form->select('mode', localize('项目模式'))->options([localize('1对1单人模式'), localize('团队多人模式')]);
```
And the helper will translate the string into relavent languages accroding to your current locale of laravel user while you have laravel queue function enabled and queue default is running in background.

### Sweep

As the process of development the strings in the code changes a lot, you may manually run command
```
php artisan trans-helper:sweep
```
or call the sweep action in your code by helper function sweep()

And you also can manually trigger the auto translation without/before running your code by calling command
```
php artisan trans-helper:trans
```
or call the translation in your code by calling helper function translate($locales=[]), the inbound parameter is the locale codes you want to translate like ['en','zh-CN','br','de'...], default locales are the config('app.locale'), config('app.fallback_locale'), config('app.faker_locale').


### Export
You can use the translation feature without text lang files, you really need them. You can use export command to get them
```
php artisan trans-helper:export
```
or call it in your code or tinker enviroment by helper command export($path=null,$locales=null), it will help your to export all locales that has translated in the tables or the locales that your assigned. Noticed: if the locale that you assigned not have any tranlsations yet, it will use the translations of locale config('app.locale').

The export language files will named with the helper function localize() called file namespace.

Hope you enjoy it! Thanks!


## License

The MIT License (MIT). Please see [License File](https://github.com/robinhoo1973/laravel-translation-helper/blob/master/LICENSE.md) for more information.
