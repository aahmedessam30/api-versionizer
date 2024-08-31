# API Versionizer

**API Versionizer** is a versatile package for managing API versioning in Laravel applications. It supports flexible routing, automatic versioning, and deprecation notices, ensuring smooth transitions between API versions while maintaining backward compatibility.

## Installation

You can install the package via Composer:

```bash
composer require ahmedessam/api-versionizer
```

## Usage

### Step 1: Publish Configuration File

Before using the API Versionizer package, you must publish the configuration file using Laravel's built-in command:

```bash
php artisan vendor:publish --tag=apiversionizer-config
```

This will create a configuration file named `apiversionizer.php` in your `config` directory. The configuration file contains the following options:

- **current_version**: The current version of the API.
- **fallback_version**: The fallback version to use if the requested version is not found.
- **strategy**: The versioning strategy to use (e.g., `uri`, `header`, `query`).
- **versioning_key**: The key used for versioning (e.g., `v`, `version`).
- **prefix**: The prefix to use for versioned routes.
- **default_version**: The default version to use if no version is specified.
- **default_directory**: The default directory to use for versioned files.
- **versioned_folders**: An array of directories containing versioned files.
- **middlewares**: An array of middleware classes to apply to versioned routes.
- **default_files**: An array of default files to use for versioned routes.
- **versions**: An array of supported API versions.

### Step 2: Configure API Versions

Once the configuration file is published, you can configure the API versions by editing the `apiversionizer.php` file. You can specify the current version, fallback version, versioning strategy, versioning key, prefix, default version, default directory, versioned folders, middlewares, default files, and supported versions.

Here is an example configuration for API versioning:

```php
'versions' => [
    'v1' => [
        'name'        => 'v1',
        'description' => 'First version of the API',
        'status'      => 'active',
        'files' => [
            [
                'name'        => 'users',
                'as'          => 'users',
                'prefix'      => 'users',
                'namespace'   => 'Users',
                'middlewares' => ['auth:api'],
            ],
        ],
    ],
    'v2' => [
        'name'        => 'v2',
        'description' => 'Second version of the API',
        'status'      => 'active',
        'files' => [
            [
                'name'        => 'users',
                'as'          => 'users',
                'prefix'      => 'users',
                'namespace'   => 'Users',
                'middlewares' => ['auth:api'],
            ],
            [
                'name'        => 'posts',
                'as'          => 'posts',
                'prefix'      => 'posts',
                'namespace'   => 'Posts',
                'middlewares' => ['auth:api'],
            ],
        ],
    ],
],
```

In this example, we define a version `v1` with a description and status. We also specify versioned files with their names, aliases, prefixes, namespaces, and middlewares.

**Note:** You can add as many versions and files as needed to support your API versioning requirements, status in the vesions array can be `active` or `inactive` or `deprecated` to indicate the status of the version.

### Step 3: Run Versionizer Command

After configuring the API versions, you can run the Versionizer command to generate versioned routes and files automatically:

```bash
php artisan api:versionize --versions=v1,v2
```

This command will create versioned routes and files based on the configuration specified in the `apiversionizer.php` file. It will generate routes for each versioned file with the appropriate version prefix and middleware.

### Step 4: Access Versioned Routes

Once the versioned routes are generated, you can access them using the version prefix in the URL. For example, to access the `users` route in version `v1`, you can use the following URL:

```
http://example.com/api/v1/users/users
```

This URL has the version prefix `v1` and the route prefix `users` specified in the configuration file.

## Handle Deprecation Notices

If you need to deprecate a version of the API, you can update the version status to `deprecated` in the configuration file and set `deprecated_at` to the date when the version will be deprecated. The Versionizer package will automatically handle deprecation notices and will return error when accessing a deprecated version.


## Copy Version to New Version

If you need to copy a version to a new version, you can run the following command:

```bash
php artisan api:versionize --copy=v1 --to=v2
```

This command will copy the version `v1` to version `v2` and will generate the versioned routes and files for the new version based on the existing version, and namespace will be updated to the new version.


## Delete Version 

If you need to delete a version, you can run the following command:

```bash
php artisan api:versionize --delete=v1
```

This command will delete the version `v1` and will remove the versioned routes and files for the specified version.

# Features

- **Flexible Routing**: Define versioned routes with custom prefixes, namespaces, and middlewares.
- **Automatic Versioning**: Generate versioned routes and files automatically based on the configuration.
- **Deprecation Notices**: Handle deprecation notices for API versions and return errors when accessing deprecated versions.
- **Version Copying**: Copy existing versions to new versions and generate versioned routes and files for the new version.
- **Version Deletion**: Delete existing versions and remove versioned routes and files for the specified version.

# Requirements
- PHP 8.2 or higher
- Laravel 10.0 or higher
- Composer

# License
The API Versionizer package is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Author

- **Ahmed Essam**
    - [GitHub Profile](https://github.com/aahmedessam30)
    - [Packagist](https://packagist.org/packages/ahmedessam/api-versionizer)
    - [LinkedIn](https://www.linkedin.com/in/aahmedessam30)
    - [Email](mailto:aahmedessam30@gmail.com)

## Contributing
Contributions are welcome! Please feel free to submit a Pull Request.

## Issues
If you find any issues with the package or have any questions, please feel free to open an issue on the GitHub repository.

Enjoy building your multilingual applications with Laravel AutoTranslate!
