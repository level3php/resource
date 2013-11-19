Level3 Resource [![Build Status](https://travis-ci.org/level3php/resource.png?branch=master)](https://travis-ci.org/level3php/resource)
==============================

Level3 Resource is a library for representing resources in different [hypermedia](http://en.wikipedia.org/wiki/Hypermedia) 
formats. A resource in a [HATEOAS API] (http://en.wikipedia.org/wiki/HATEOAS) must describe their own capabilities 
and interconnections, this is the third level of [Three Levels of the REST Maturity Model](http://www.infoq.com/news/2010/03/RESTLevels)

### Why Hypermedia?

As you can read on prologue of [Designing Hypermedia APIs](http://www.designinghypermediaapis.com/) book:

>Hypermedia APIs embrace the principles that make the web great: flexibility, standardization, and loose coupling 
to any given service. They take into account the principles of systems design [enumerated by Roy Fielding in his thesis](http://www.ics.uci.edu/~fielding/pubs/dissertation/top.htm), 
but with a little less sytems theory jargon.

>Hypermedia designs scale better, are more easily changed and promote decoupling and encapsulation, with all the 
benefits those things bring. On the downside, it is not necessarily the most latency-tolerant design, and caches 
can get stale if you're not careful. It may not be as efficient on an individual request level as other designs.

>-- [Steve Klabnik](http://www.steveklabnik.com/)

#### Which Hypermedia specification i should use?

Hypermedia is being defined in this days, just the better APIs implements Hypermedia, currently not exists a de 
facto standard, so you must choose between the differents specifications.

Level3 Resource currenly implements or is planed to implement this specifications:
* [HAL](http://stateless.co/hal_specification.html): This is the most common and active, has a JSON and a XML version. If you dont need use actions is your better option.
* [Siren](https://github.com/kevinswiber/siren): Currently being defined. Implements some usefull things like actions, classes, etc. Is a good choice if you want make CRUD
* [Collection+JSON](http://amundsen.com/media-types/collection/): This is fully oriented to make a API CRUD oriented. 


Requirements
------------

* PHP 5.4.x

Installation
------------

The recommended way to install Level3 Resource is [through composer](http://getcomposer.org).
You can see [the package information on Packagist.](https://packagist.org/packages/level3/resource)

```JSON
{
    "require": {
        "level3/resource": "dev-master"
    }
}
```


Examples
--------

### Basic Resource with Link as ```application/hal+json```

```php
use Level3\Resource\Link;
use Level3\Resource\Resource;
use Level3\Resource\Formatter\HAL;

$resource = new Resource();
$resource->setURI('/foo');
$resource->setLink('foo', new Link('/bar'));
$resource->setData([
    'foo' => 'bar',
    'baz' => 'qux'
]);

$resource->setFormatter(new HAL\JsonFormatter(true));

echo $resource;
```

```js
{
    "foo": "bar",
    "baz": "qux",
    "_links": {
        "self": {
            "href": "/foo"
        },
        "foo": {
            "href": "/bar"
        }
    }
}
```

### Resource with embedded resources as ```aapplication/vnd.siren+json```

```php
use Level3\Resource\Link;
use Level3\Resource\Resource;
use Level3\Resource\Formatter\Siren;

$resource = new Resource();
$resource->setRepositoryKey('index');
$resource->setURI('/index?page=2');
$resource->setLink('prev', new Link('/index?page=1'));
$resource->setLink('next', new Link('/index?page=3'));
$resource->addData('count', 5);

$subresource = [];
foreach (range(1, 5) as $value) {
    $subresource = new Resource();
    $subresource->addData('value', $value);

    $subresources[] = $subresource;
}

$resource->addResources('subresources', $subresources);

$resource->setFormatter(new Siren\JsonFormatter(true));

echo $resource;
```

```json
{
    "class": [
        "index"
    ],
    "properties": {
        "count": 5
    },
    "entities": [
        {
            "rel": "subresources",
            "class": [
                "index",
                "subresources"
            ],
            "properties": {
                "value": 1
            }
        },
        ...
        {
            "rel": "subresources",
            "class": [
                "index",
                "subresources"
            ],
            "properties": {
                "value": 5
            }
        }
    ],
    "links": [
        {
            "rel": "self",
            "href": "/index?page=2"
        }
    ]
}
```

### Resource with linked resource as ```application/hal+xml```

```php
use Level3\Resource\Link;
use Level3\Resource\Resource;
use Level3\Resource\Formatter\HAL;

$author = new Resource();
$author->setURI('/john-doe');
$author->setTitle('John Doe');

$article = new Resource();
$article->setURI('/lorem-ipsum');
$article->addData('description', 'Lorem ipsum dolor sit amet ...');
$article->linkResource('author', $author);

$article->setFormatter(new HAL\XMLFormatter(true));

echo $article;
```

```xml
<?xml version="1.0"?>
<resource href="/lorem-ipsum">
  <description>Lorem ipsum dolor sit amet ...</description>
  <link rel="author" href="/john-doe" title="John Doe"/>
</resource>
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
