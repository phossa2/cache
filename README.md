# phossa2/cache [ABANDONED]
Please use [phoole/cache](https://github.com/phoole/cache) instead.

[![Build Status](https://travis-ci.org/phossa2/cache.svg?branch=master)](https://travis-ci.org/phossa2/cache)
[![Code Quality](https://scrutinizer-ci.com/g/phossa2/cache/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phossa2/cache/)
[![Code Climate](https://codeclimate.com/github/phossa2/cache/badges/gpa.svg)](https://codeclimate.com/github/phossa2/cache)
[![PHP 7 ready](http://php7ready.timesplinter.ch/phossa2/cache/master/badge.svg)](https://travis-ci.org/phossa2/cache)
[![HHVM](https://img.shields.io/hhvm/phossa2/cache.svg?style=flat)](http://hhvm.h4cc.de/package/phossa2/cache)
[![Latest Stable Version](https://img.shields.io/packagist/vpre/phossa2/cache.svg?style=flat)](https://packagist.org/packages/phossa2/cache)
[![License](https://img.shields.io/:license-mit-blue.svg)](http://mit-license.org/)

**phossa2/cache** is a PSR-6 compliant caching library for PHP. It supports
various drivers and useful features like bypass, encrypt, stampede protection
etc.

It requires PHP 5.4, supports PHP 7.0+ and HHVM. It is compliant with [PSR-1][PSR-1],
[PSR-2][PSR-2], [PSR-3][PSR-3], [PSR-4][PSR-4], [PSR-6][PSR-6] and the proposed
[PSR-5][PSR-5].

[PSR-1]: http://www.php-fig.org/psr/psr-1/ "PSR-1: Basic Coding Standard"
[PSR-2]: http://www.php-fig.org/psr/psr-2/ "PSR-2: Coding Style Guide"
[PSR-3]: http://www.php-fig.org/psr/psr-3/ "PSR-3: Logger Interface"
[PSR-4]: http://www.php-fig.org/psr/psr-4/ "PSR-4: Autoloader"
[PSR-5]: https://github.com/phpDocumentor/fig-standards/blob/master/proposed/phpdoc.md "PSR-5: PHPDoc"
[PSR-6]: http://www.php-fig.org/psr/psr-6/ "PSR-6: Caching Interface"

Installation
---
Install via the `composer` utility.

```bash
composer require "phossa2/cache"
```

or add the following lines to your `composer.json`

```json
{
    "require": {
       "phossa2/cache": "2.*"
    }
}
```

Features
---

- Fully [PSR-6][PSR-6] compliant.

- Support all serializable PHP datatypes.

- **Extensions included**:

  - **Bypass**: If sees a trigger in URL (e.g. '?nocache=true'), bypass the
    cache.

  - **Stampede Protection**: Whenever cached object's lifetime is less than a
    configurable time, by a configurable percentage, the cache will return false
    on 'isHit()' which will trigger re-generation of the object.

  - **Encrypt**: A simple extension to encrypt the serialized content.

  - **DistributedExpiration**: Even out the spikes of item misses by alter
    expiration time a little bit.

- **Drivers**

  - **StorageDriver**

    The storage driver uses [phossa2/storage](https://github.com/phossa2/storage)
    local or cloud storage.

  - **NullDriver**

    The blackhole driver, can be used as fallback driver for all other drivers.

Usage
--

- Simple usage

  ```php
  /*
   * cache dir default to local sys_get_temp_dir() . '/cache'
   */
  $cache = new CachePool();

  $item = $cache->getItem('/data.cache');
  if (!$item->isHit()) {
      $value = calcuate_data();
      $item->set($value);
      $cache->save($item);
  }
  $data = $item->get();
  ```

- Specify the driver

  ```php
  use Phossa2\Cache\Driver\StorageDriver;
  use Phossa2\Storage\Storage;
  use Phossa2\Storage\Filesystem;
  use Phossa2\Storage\Driver\LocalDriver;

  $driver = new StorageDriver(
      new Storage('/', new Filesystem(new LocalDriver(sys_get_temp_dir()))),
      '/cache'
  );

  $cache = new CachePool($driver);
  ```

- Use extensions

  ```php
  /*
   * DistributedExpiration extension
   */
  use Phossa2\Cache\CachePool;
  use Phossa2\Cache\Extension\DistributedExpiration;

  $cache = new CachePool();
  $cache->addExtension(new DistributedExpiration());
  ```

Change log
---

Please see [CHANGELOG](CHANGELOG.md) from more information.

Testing
---

```bash
$ composer test
```

Contributing
---

Please see [CONTRIBUTE](CONTRIBUTE.md) for more information.

Dependencies
---

- PHP >= 5.4.0

- [phossa2/event](https://github.com/phossa2/event) >= 2.1.4

- [phossa2/storage](https://github.com/phossa2/storage) >= 2.0.0

License
---

[MIT License](http://mit-license.org/)
