# Laravel-support

[![Latest Version on Packagist](https://img.shields.io/packagist/v/padosoft/laravel-support.svg?style=flat-square)](https://packagist.org/packages/padosoft/laravel-support)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/padosoft/laravel-support/master.svg?style=flat-square)](https://travis-ci.org/padosoft/laravel-support)
[![Quality Score](https://img.shields.io/scrutinizer/g/padosoft/laravel-support.svg?style=flat-square)](https://scrutinizer-ci.com/g/padosoft/laravel-support)
[![Total Downloads](https://img.shields.io/packagist/dt/padosoft/laravel-support.svg?style=flat-square)](https://packagist.org/packages/padosoft/laravel-support)

Laravel-support package is a collection of helpers and tools for Laravel projects.

##Requires
  
- php: >=7.0.0
- illuminate/database: ^5.0
- illuminate/support: ^5.0
- illuminate/auth: ^5.0
- illuminate/contracts: ^5.0
  
## Installation

You can install the package via composer:
``` bash
$ composer require padosoft/laravel-support
```
  
## List of functions

- validate
- locale
- userIsLogged
- query_interpolate
- queries
- query_table
  
## Usage

Here is some support method:

Using helper for laravel validator:
``` php
echo validate('192.168.0.1', 'ip'); //true
echo validate('dfdsfdsfs', 'ip'); //false
echo validate('20150230', 'date'); //false
echo validate('20150227', 'date'); //true
```

Using locale() helper:
``` php
echo locale();  //'en'
this->app->setLocale('it');
echo locale(); 'it'
```

Using query_interpolate() helper:
``` php
$query = 'update "users" set "remember_token" = ? where "id" = ?';
$bindings = [
    0 => "dfsf234wdfsafsdfsdf",
    1 => "1"
];
$result = query_interpolate($query, $bindings);
echo $result; //update "users" set "remember_token" = 'dfsf234wdfsafsdfsdf' where "id" = 1
```

Using queries() helper:
``` php
User::first();
User::first();
User::first();
$queries = queries();
var_dump($result);
foreach ($queries as $query) {
    echo 'e($query['look']) . "\t" . e($query['time']) . PHP_EOL;
}
```

Using query_table() helper:
``` php
User::first();
User::first();
User::first();
echo query_table();//print html table with queries
```

Using current_user() helper:
``` php
echo current_user(); //false
$user = User::first();
Auth::login($user);
var_dump(current_user()); //sump current logged user
echo current_user('id'); //1
echo current_user('email'); //test@user.com
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email instead of using the issue tracker.

## Credits
- [Lorenzo Padovani](https://github.com/lopadova)
- [All Contributors](../../contributors)

## About Padosoft
Padosoft (https://www.padosoft.com) is a software house based in Florence, Italy. Specialized in E-commerce and web sites.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
