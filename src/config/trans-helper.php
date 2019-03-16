<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel-Translation-Helper Database Settings
    |--------------------------------------------------------------------------
    |
    | Here are database settings for Laravel-Translation-Helper builtin model & tables.
    |
    */
    'database' => [
        // Database connection for following tables.
        'connection' => '',

    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel-Translation-Helper Translation Settings
    |--------------------------------------------------------------------------
    |
    | Here are database settings for Laravel-Translation-Helper builtin model & tables.
    |
    */

    // Vocabulary data tables and model.
    'translation' => [
        'mode' => 'auto', //auto,manual,off
    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel-Translation-Helper Exporting Settings
    |--------------------------------------------------------------------------
    |
    | Here are database settings for Laravel-Translation-Helper builtin model & tables.
    |
    */

    // Vocabulary data tables and model.
    'export' => [
        'path' => realpath(base_path('resources/lang')),
    ],

];
