[![](https://gitlab.com/grammm/php-gram/phpgram/raw/master/docs/img/Feather_writing.svg.png)](https://gitlab.com/grammm/php-gram/phpgram)

# phpgram

A very fast and lightweight Php Framework for small to enterprise applications.

- Routing based on [nikic/Fastroute](https://github.com/nikic/FastRoute)

- RouteHandler for Functions and Classes

- Request and Response via [Psr-7](https://www.php-fig.org/psr/psr-7/) 

- Middleware via [Psr-15](https://www.php-fig.org/psr/psr-15/) 

- [Psr 11](https://www.php-fig.org/psr/psr-11/) Container Support for Automatic Dependency Injection for Classes (in constructor) and Functions (with ``__get()``)

- Response Creation (via [Psr-17](https://www.php-fig.org/psr/psr-17/) Response Factory)

- Define Output Strategies


## Doc
- [start](https://gitlab.com/grammm/php-gram/phpgram/blob/master/docs/index.md)

## Install

Via Composer

``` bash
$ composer require phpgram/phpgram
```

## Credits
### Router
- Algorithm and Core Implementation: Copyright by Nikita Popov. ([FastRoute](https://github.com/nikic/FastRoute))
- Parser: Copyright by Nikita Popov and Phil Bennett ([thephpleague](https://github.com/thephpleague/route))

### Emitter
- Based on [zend-httphandlerrunner](https://github.com/zendframework/zend-httphandlerrunner). Copyright [Zend Technologies USA, Inc. All rights reserved](https://github.com/zendframework/zend-httphandlerrunner/blob/master/LICENSE.md)
