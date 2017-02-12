# Aalberts Laravel PXL CMS Adapter

Nothing to see here for now. See the PXLCMS adapter package for more information.


## Install

Via Composer

``` bash
$ composer require czim/aalberts-pxlcms
```

Add this line of code to the providers array located in your `config/app.php` file:

``` php
    Aalberts\AalbertsCmsServiceProvider::class,
```

And add the facade aliases to the same file:

``` php
    'Aalberts'  => Aalberts\Facades\AalbertsFacade::class,
    'Translate' => Aalberts\Facades\TranslateFacade::class,
```

Publish the configuration:

``` bash
$ php artisan vendor:publish
```

Set up event listening for missing translation phrases. Add the following pair to your `EventServiceProvider` class:

``` php
    \Aalberts\Events\DetectedMissingTranslationPhrase::class => [
        \Aalberts\Listeners\MissingTranslationPhraseListener::class
    ], 
    \Aalberts\Events\SearchPerformed::class => [ 
        \Aalberts\Listeners\SearchListener::class
    ],
```

## Cache

This package requires that a cache driver be used that supports tagging (such as Redis).


## Configuration

### .env

Set the following keys for your application in the `.env` file like so (or adjust `confing/aalberts.php` directly).

``` bash
AALBERTS_ORGANIZATION=2
AALBERTS_ORGANIZATION_KEY=VSH
```

### Scheduling

Translations, CMS content and Compano content are all cached for common lookups.
All translations should be loaded in cache at all times; other content may be cached on demand.

A typical schedule for this in your `Console/Kernel.php` is:

```
        $schedule->command('aalberts:cache:translations')->everyFiveMinutes();
        $schedule->command('aalberts:cache:cms:check')->everyTenMinutes();
        $schedule->command('aalberts:cache:cmp:check')->dailyAt('06:00');
```


### Translations

#### Labels

It is much more efficient to keep all translations cached, to prevent lookups for individual `atrans()` calls.
Translations are not automatically cached, at least not in a batch. 

To make sure the cache stays up to date, schedule the `artisan:cache:translations` command to run periodically.
It is recommended to keep the interval at least 5 minutes or to prevent overlap. 
The command will check whether a cache is required by comparing the latest `modifiedts` date for all the organization's translations.
If no updates since the last cache time are detected, the cache will not be renewed. 

If no cache has been set at all, this command will always fill the cache.

#### Local translations

Add `aalberts` translations files for all locales that your application uses and set content like the following:

``` php
<?php

return [

    'months' => [
        '01' => 'jan',
        '02' => 'feb',
        '03' => 'mrt',
        '04' => 'apr',
        '05' => 'mei',
        '06' => 'jun',
        '07' => 'jul',
        '08' => 'aug',
        '09' => 'sep',
        '10' => 'okt',
        '11' => 'nov',
        '12' => 'dec',
    ],
    
];
```

This will be used by the `StandardDateFormatter`.


## Usage

Run the generator to make the models and repositories:

``` bash
$ php artisan aalberts:generate
```

This command works exactly like the `czim/laravel-pxlcms` package `pxlcms:generate` command.


### Presenters

Some models have presenters from the [Laracasts presenter package](https://github.com/laracasts/Presenter).

ToDo: add support for this to the package
- How to override and/or set your own presenter classes.
- Provide default presenters
- Set up date formatting (with 'special' standard class)

### Translation

Note that the `Translator` expects the generated Phrase and Translation classes to be in `App\Models\Aalberts`.
Anywhere else will break the built-in `Translator` class.

### Filters

#### Adding a Filter

Things to update:

- Add the filter slugs & validation rules to the `Aalberts\Filters\ProductFilterData` (don't forget the `$defaults` key!)
- Add the filter slugs to the `Aalberts\Filters\ProductFilter` properties
- Add an `Aalberts\Filters\ParameterCounters\..` Counter class
- Add an `Aalberts\Filters\ParameterFilters\..` Parameter class
- If it doesn't exist yet, add a repository for the Compano filter target model (such as for `cmp_applications`)
- Update the `Aalberts\Factories\FilterStrategyFactory`: set the repository for the slug


## Credits

- [Coen Zimmerman][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/czim/laravel-pxlcms.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/czim/laravel-pxlcms.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/czim/aalberts-pxlcms
[link-downloads]: https://packagist.org/packages/czim/aalberts-pxlcms
[link-author]: https://github.com/czim
[link-contributors]: ../../contributors
