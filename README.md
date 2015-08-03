# HTTPStatus

[![Latest Version on Packagist](https://img.shields.io/github/release/lukasoppermann/http-status.svg?style=flat-square)](https://github.com/lukasoppermann/http-status/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads](https://img.shields.io/packagist/dt/lukasoppermann/http-status.svg?style=flat-square)](https://packagist.org/packages/lukasoppermann/http-status)

The HTTPStatus package provides an easy and convinent way to retrieve the standard status text (english) for any given HTTP status code. You can also get the HTTP status code for any valid status text. Additionally this package provides all status codes as constants, to use for a better readability of your code (`HTTP_OK` is just much easier to understand than `200`).

## Install

Via Composer

``` bash
$ composer require lukasoppermann/HTTPStatus
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

[ico-version]: https://img.shields.io/packagist/v/league/HTTPStatus.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/thephpleague/HTTPStatus/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/thephpleague/HTTPStatus.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/thephpleague/HTTPStatus.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/league/HTTPStatus.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/league/HTTPStatus
[link-travis]: https://travis-ci.org/thephpleague/HTTPStatus
[link-scrutinizer]: https://scrutinizer-ci.com/g/thephpleague/HTTPStatus/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/thephpleague/HTTPStatus
[link-downloads]: https://packagist.org/packages/league/HTTPStatus
[link-author]: https://github.com/lukasoppermann
[link-contributors]: ../../contributors
