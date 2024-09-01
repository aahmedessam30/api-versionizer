<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Api Versionizer
    |--------------------------------------------------------------------------
    |
    | This file is for storing the api versionizer configuration.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Api Debug
    |--------------------------------------------------------------------------
    |
    | This value is the debug mode of the api.
    */
    'debug' => env('API_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Current Version
    |--------------------------------------------------------------------------
    |
    | This value is the current version of the api.
    |
    */
    'current_version' => env('API_VERSION', 'v1'),

    /*
    |--------------------------------------------------------------------------
    | FallBack Version
    |--------------------------------------------------------------------------
    |
    | This value is the fallback version of the api.
    |
    */
    'fallback_version' => env('API_FALLBACK_VERSION', 'v1'),

    /*
    |--------------------------------------------------------------------------
    | Strategy
    |--------------------------------------------------------------------------
    |
    | This value is the strategy of the versioning.
    | Available options are: 'uri', 'header', 'query'.
    |
    */
    'strategy' => 'uri',

    /*
    |--------------------------------------------------------------------------
    | Versioning Key
    |--------------------------------------------------------------------------
    |
    | This value is the key of the versioning.
    | It will be used in the strategy of 'header' and 'query'.
    |
    */
    'versioning_key' => [
        'header' => 'Accept-Version',
        'query'  => 'version',
    ],

    /*
    |--------------------------------------------------------------------------
    | Prefix
    |--------------------------------------------------------------------------
    |
    | This value is the prefix of the versioning.
    | It will be used in the strategy of 'uri'.
    |
    */
    'prefix' => 'v',

    /*
    |--------------------------------------------------------------------------
    | Default Version
    |--------------------------------------------------------------------------
    |
    | This value is the default version of the api.
    |
    */
    'default_version' => 'v1',

    /*
    |--------------------------------------------------------------------------
    | Default Directory
    |--------------------------------------------------------------------------
    |
    | This value is the default directory of the versioned api.
    | It will be used to put the versioned api files in it.
    |
    */
    'default_directory' => 'Api',

    /*
    |--------------------------------------------------------------------------
    | Versioned Folders
    |--------------------------------------------------------------------------
    |
    | This value is the versioned folders of the versioned api.
    | Available options are: 'controller', 'request', 'resource', etc.
    | Add your custom folders here,
    | and the package will create the versioned files in these folders.
    | package will put unknown folders in the app directory.
    |
    */
    'versioned_folders' => ['routes', 'controllers', 'requests', 'resources'],

    /*
    |--------------------------------------------------------------------------
    | Middlewares
    |--------------------------------------------------------------------------
    |
    | This value is the middlewares of the versioned api.
    |
    */
    'middlewares' => ['api', 'localization'],

    /*
    |--------------------------------------------------------------------------
    | Default Files
    |--------------------------------------------------------------------------
    |
    | This value is the default files of the versioned api.
    |
    */
    'default_files' => [
        [
            'name'        => 'auth',
            'as'          => 'auth',
            'prefix'      => 'auth',
            'namespace'   => 'Auth',
            'middlewares' => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Versions
    |--------------------------------------------------------------------------
    |
    | Here you may specify the API versions that you want to use.
    | You can add as many versions as you want.
    | Each version should have a name, description, status, deprecated_at.
    | if you want to use the default files, just leave the files array empty.
    | if you want to use the default middlewares, just leave the middlewares array empty.
    |
    */
    'versions' => [
        'v1' => [
            'name'          => 'v1',
            'description'   => 'First version of the API',
            'status'        => 'active', // active, inactive, deprecated
            'deprecated_at' => null, // if status is deprecated
            'files'         => [
                // if empty, the package will use the default files
            ],
            'middlewares'   => [
                // if empty, the package will use the default middlewares
            ],
        ],
    ],
];
