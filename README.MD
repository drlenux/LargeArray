# Large Array

[![Latest Version](https://img.shields.io/packagist/v/drlenux/large-array.svg?style=flat-square)](https://packagist.org/packages/vendor/package-name)
[![Total Downloads](https://img.shields.io/packagist/dt/drlenux/large-array.svg?style=flat-square)](https://packagist.org/packages/vendor/package-name)

## PHP Array Implementation with SQLite3

Designed for storing large datasets as an array.

Please note: If you have a very large array, consider reevaluating your architecture rather than using this code. Use this library only in exceptional cases.


## Installation

You can install this package via Composer:

```bash
composer require drlenux/large-array
```

## Usage

```php

use Drlenux\LargeArray\LargeArray;

$array = new LargeArray(/** optional salt **/);
$array = new LargeArray('for_report'); // don't use space

for ($i = 0; $i < 1_000_000; $i++) $array[] = $i;
$array['test'] = 'test value';
$array['sub']['array'] = true;

$array->in(5000); // result: true [instead of in_array]
$array[5000] // result: 5000
$array['test'] // result: test value

```

*LargeArray* use interface ArrayAccess, Countable, Iterator

## Security
If you discover any security-related issues, please email [oleksii@bielokudrenko.family](mailto:oleksii@bielokudrenko.family) instead of using the issue tracker.

## License
This package is open-source software licensed under the MIT license.
