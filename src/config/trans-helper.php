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
        'async'  => true,

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
        'mode'   => 'auto',
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
