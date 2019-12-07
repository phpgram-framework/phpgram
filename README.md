[![](https://gitlab.com/grammm/php-gram/phpgram/raw/master/docs/img/Feather_writing.svg.png)](https://gitlab.com/grammm/php-gram/phpgram)

# phpgram

[![pipeline status](https://gitlab.com/grammm/php-gram/phpgram/badges/master/pipeline.svg)](https://gitlab.com/grammm/php-gram/phpgram/commits/master)
[![Packagist Version](https://img.shields.io/packagist/v/phpgram/phpgram)](https://packagist.org/packages/phpgram/phpgram)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/phpgram/phpgram)](https://gitlab.com/grammm/php-gram/phpgram/blob/master/composer.json)
[![Packagist](https://img.shields.io/packagist/l/phpgram/phpgram)](https://gitlab.com/grammm/php-gram/phpgram/blob/master/LICENSE)

A very fast and lightweight Php Framework for small to enterprise applications.

- Routing based on [nikic/Fastroute](https://github.com/nikic/FastRoute)

- RouteHandler for Functions and Classes

- Request and Response via [Psr-7](https://www.php-fig.org/psr/psr-7/) 

- Middleware via [Psr-15](https://www.php-fig.org/psr/psr-15/) 

- [Psr 11](https://www.php-fig.org/psr/psr-11/) Container Support for Automatic Dependency Injection for Classes (in constructor) and Functions (with ``__get()``)

- Response Creation (via [Psr-17](https://www.php-fig.org/psr/psr-17/) Response Factory)

- Define Output Strategies


## Documentation
- [start](https://gitlab.com/grammm/php-gram/phpgram/blob/master/docs/index.md)

## Install

Via Composer

``` bash
$ composer require phpgram/phpgram
```

## License

phpgram is open source and under [MIT License](https://gitlab.com/grammm/php-gram/phpgram/blob/master/LICENSE)

## Credits
### Router
- Algorithm and Core Implementation: Copyright by Nikita Popov. ([FastRoute](https://github.com/nikic/FastRoute))
- Parser: Copyright by Nikita Popov and Phil Bennett ([thephpleague](https://github.com/thephpleague/route))

### Emitter
- Based on [zend-httphandlerrunner](https://github.com/zendframework/zend-httphandlerrunner). Copyright [Zend Technologies USA, Inc. All rights reserved](https://github.com/zendframework/zend-httphandlerrunner/blob/master/LICENSE.md)
