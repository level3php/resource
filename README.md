Level3 RESTful API builder [![Build Status](https://travis-ci.org/yunait/level3.png?branch=master)](https://travis-ci.org/mongator/laravel)
==============================

A RESTful API builder based on 3-level model (URI, HTTP and Hypermedia) 

Read about *3-level model of restful maturity* at:
* http://www.infoq.com/news/2010/03/RESTLevels
* http://www.crummy.com/writing/speaking/2008-QCon/act3.html

> Under heavy development

Requirements
------------

* PHP 5.3.x
* shrikeh/teapot
* pimple/pimple
* zircote/hal

Installation
------------

Add `yunait/level3` to your composer requirements, you can see [the package information on Packagist.](https://packagist.org/packages/yunait/level3):

```JSON
{
    "require": {
        "yunait/level3": "dev-master"
    }
}
```

Tests
-----

Tests are in the `tests` folder.
To run them, you need PHPUnit.
Example:

    $ phpunit --configuration phpunit.xml.dist


License
-------

MIT, see [LICENSE](LICENSE)
