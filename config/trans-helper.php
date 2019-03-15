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
        // Vocabulary data tables and model.
        'table' => [
            'term' =>  '_vocab_terms',
            'cite' => '_vocab_cites',
            'link' => '_voca_links',
        ]
    ],
    /*
    |--------------------------------------------------------------------------
    | Laravel-Translation-Helper Model Settings
    |--------------------------------------------------------------------------
    |
    | Here are database settings for Laravel-Translation-Helper builtin model & tables.
    |
    */

    // Vocabulary data tables and model.
    'model' => [
        'term' => TopviewDigital\TranslationHelper\Model\VocabTerm::class,
        'cite' => TopviewDigital\TranslationHelper\Model\VocabCite::class,
    ]
];
