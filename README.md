Level3 RESTful API builder [![Build Status](https://travis-ci.org/yunait/level3.png?branch=master)](https://travis-ci.org/yunait/level3)
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
* nocarrier/hal
* symfony/http-foundation
* psr/log: ~1.0
* symfony/yaml

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
    
Documentation
-------------
### Overview
Level3 only provides the handling of already parsed requests. These requests extend `Symfony\Component\HttpFoundation\Request` and add some extra functionality. You have to to create these requests from whatever delivery mechanism/framework you are using (see [level3-silex](https://github.com/yunait/level3-silex) for an example of how to do this using [Silex](http://silex.sensiolabs.org/)).

These requests travel along a series of `RequestProcessor` instances that can authenticate, authorize and modify them. The last `RequestProcessor` is the `AccessorWrapper`. It knows how to translate a request into a call for the `Accessor`, and interpret the return of that call to turn it into a `Response` (which extends `Symfony\Component\HttpFoundation\Response`) object.

The `Accessor` is in charge of asking the `RepositoryHub` for the `Repository` in charge of handling the fetching of the requested resource and querying it.

The `RepositoryHub` knows what `Repository` is responsible of fetching each kind of resource based on the `key`.

The `RepositoryMapper` knows about `Repositories` and `URLs`.

`Repositories` have to be extended in order to implement the logic in charge of handling resources from the storage system.

![Classes overview](https://raw.github.com/yunait/level3/master/doc/overview.png)

### Request and Response
Are the messages passed through a chain of `RequestProcessor`s. They encapsulate all the business logic.

On the one hand the Controller handling the HTTP request has to create a `Request` object, but on the other hand, Level3 `Response` objects, in the case of using a (Symfony)[http://symfony.com/] based framework can be returned directly since they extend its Response implementation.

### RequestProcessor
All subclasses are intended to handle the request, pass it to the next processor, get their response, handle it, and reuturn it. They can be chained in order to implement any kind of functionality. Some default are already provided as an example, since they can also be useful (you can read more about `RequestProcessor` (here)[https://raw.github.com/yunait/level3/master/doc/RequestProcessor.md]):

#### AcessorWrapper
Is the only mandatory `RequestProcessor`. It translates the request into a `RepositoryFriendly` format and generates a response from its response. If chained to others, this has to be the last one in the chain.

#### ExceptionHandler
Captures thrown exceptions and prints them in a convenient format, depending on the request headers. It also logs to a [PSR3 Logger](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md).

#### RequestLogger
Logs all requests to a [PSR3 Logger](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md).

#### AuthenticationProcessor
This class authenticates the request and sets *proper* credentials in order to, later, authorize or not the request. The way the authentication is handled is done using implementations of `Level3\Security\Authentication\Method`. By default, `HMAC` is used.

Read more about this [here](https://raw.github.com/yunait/level3/master/doc/AuthenticationProcessor.md)

#### AuthorizationProcessor
Authorizes the request based on it's `getCredentials()` method. Several authorizators are provided:
* Role based authorizator
* ACL based authorizator

By default, all request have anonymous credentials, so if no `AuthenticationProcessor` is chained before, this is what you can expect.

You can read more about `AurizationProcesor` [here](https://raw.github.com/yunait/level3/master/doc/AuthorizationProcessor.md)

### Repository
Is the one in charge of retrieving the data from the underlying storage and returning a `Level3\Hal\Resource` with some help from `Level3\Hal\ResourceBuilder`


License
-------

MIT, see [LICENSE](LICENSE)
