# HTTPStatus

[![Latest Version on Packagist](https://img.shields.io/github/release/lukasoppermann/http-status.svg?style=flat-square)](https://github.com/lukasoppermann/http-status/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/lukasoppermann/http-status.svg?style=flat-square)](https://travis-ci.org/lukasoppermann/http-status)
[![Total Downloads](https://img.shields.io/packagist/dt/lukasoppermann/http-status.svg?style=flat-square)](https://packagist.org/packages/lukasoppermann/http-status)

The HTTPStatus package provides an easy and convinent way to retrieve the standard status text (english) for any given HTTP status code. You can also get the HTTP status code for any valid status text. Additionally this package provides all status codes as constants, to use for a better readability of your code (`HTTP_OK` is just much easier to understand than `200`).

## Install

Via Composer

``` bash
$ composer require lukasoppermann/http-status
```

## Usage

``` php
$HTTPStatus = new Lukasoppermann\HTTPStatus\HTTPStatus();
// get status text from code
echo $HTTPStatus->text(301); // Moved Permanently
// get the status code by text
echo $HTTPStatus->code('Method Not Allowed'); // 405
// using constants
echo $HTTPStatus::HTTP_CREATED; // 201
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

If you discover any security related issues, please email oppermann.lukas@gmail.com instead of using the issue tracker.

## Credits

- [Lukas Oppermann][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[link-author]: https://github.com/lukasoppermann
[link-contributors]: ../../contributors
