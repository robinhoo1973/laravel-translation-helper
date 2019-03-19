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
    | Laravel-Translation-Helper Translation Settings
    |--------------------------------------------------------------------------
    |
    | Here are translating settings for Laravel-Translation-Helper trigger auto translation.
    |
    */

    // Vocabulary data tables and model.
    'translation' => [
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

    // Vocabulary data tables and model.
    'export' => [
        'path' => realpath(base_path('resources/lang')),
    ],

];
