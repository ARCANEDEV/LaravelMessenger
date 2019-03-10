# 1. Installation

## Table of contents

  1. [Installation and Setup](1-Installation-and-Setup.md)
  2. [Configuration](2-Configuration.md)
  3. [Usage](3-Usage.md)

## Version Compatibility

| LaravelMessenger                                | Laravel                                                                                |
|:------------------------------------------------|:---------------------------------------------------------------------------------------|
| ![LaravelMessenger v1.x][laravel_messenger_1_x] | ![Laravel v5.1][laravel_5_1] ![Laravel v5.2][laravel_5_2] ![Laravel v5.3][laravel_5_3] |
| ![LaravelMessenger v2.x][laravel_messenger_2_x] | ![Laravel v5.4][laravel_5_4]                                                           |
| ![LaravelMessenger v3.x][laravel_messenger_3_x] | ![Laravel v5.5][laravel_5_5]                                                           |
| ![LaravelMessenger v4.x][laravel_messenger_4_x] | ![Laravel v5.6][laravel_5_6]                                                           |
| ![LaravelMessenger v5.x][laravel_messenger_5_x] | ![Laravel v5.7][laravel_5_7]                                                           |
| ![LaravelMessenger v6.x][laravel_messenger_6_x] | ![Laravel v5.8][laravel_5_8]                                                           |

[laravel_5_1]:  https://img.shields.io/badge/v5.1-supported-brightgreen.svg?style=flat-square "Laravel v5.1"
[laravel_5_2]:  https://img.shields.io/badge/v5.2-supported-brightgreen.svg?style=flat-square "Laravel v5.2"
[laravel_5_3]:  https://img.shields.io/badge/v5.3-supported-brightgreen.svg?style=flat-square "Laravel v5.3"
[laravel_5_4]:  https://img.shields.io/badge/v5.4-supported-brightgreen.svg?style=flat-square "Laravel v5.4"
[laravel_5_5]:  https://img.shields.io/badge/v5.5-supported-brightgreen.svg?style=flat-square "Laravel v5.5"
[laravel_5_6]:  https://img.shields.io/badge/v5.6-supported-brightgreen.svg?style=flat-square "Laravel v5.6"
[laravel_5_7]:  https://img.shields.io/badge/v5.7-supported-brightgreen.svg?style=flat-square "Laravel v5.7"
[laravel_5_8]:  https://img.shields.io/badge/v5.8-supported-brightgreen.svg?style=flat-square "Laravel v5.8"

[laravel_messenger_1_x]: https://img.shields.io/badge/version-1.*-blue.svg?style=flat-square "LaravelMessenger v1.*"
[laravel_messenger_2_x]: https://img.shields.io/badge/version-2.*-blue.svg?style=flat-square "LaravelMessenger v2.*"
[laravel_messenger_3_x]: https://img.shields.io/badge/version-3.*-blue.svg?style=flat-square "LaravelMessenger v3.*"
[laravel_messenger_4_x]: https://img.shields.io/badge/version-4.*-blue.svg?style=flat-square "LaravelMessenger v4.*"
[laravel_messenger_5_x]: https://img.shields.io/badge/version-5.*-blue.svg?style=flat-square "LaravelMessenger v5.*"
[laravel_messenger_6_x]: https://img.shields.io/badge/version-6.*-blue.svg?style=flat-square "LaravelMessenger v6.*"

## Composer

You can install this package via [Composer](http://getcomposer.org/) by running this command: `composer require arcanedev/laravel-messenger`.

## Laravel

### Setup

> **NOTE :** The package will automatically register itself if you're using Laravel `>= v5.5`, so you can skip this section.

Once the package is installed, you can register the service provider in `config/app.php` in the `providers` array:

```php
// config/app.php

'providers' => [
    ...
    Arcanedev\LaravelMessenger\LaravelMessengerServiceProvider::class,
],
```

### Artisan commands

To publish the config file, run this command:

```bash
php artisan vendor:publish --provider="Arcanedev\LaravelMessenger\LaravelMessengerServiceProvider"
```
