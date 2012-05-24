# DarkRedisListBundle [![Build Status](https://secure.travis-ci.org/cursedcoder/DarkRedisListBundle.png?branch=master)](http://travis-ci.org/cursedcoder/DarkRedisListBundle)
Symfony2 bundle which allows you to store part of doctrine entities data in Redis.
For more information please read text below.

### How do we manage lists at MySQL:
- write query, for example 'SELECT a FROM MyTestBundle:Post a ORDER BY a.created_at'
- use doctrine repository

then we can pass it to the paginator, but no need to talk about it now.

### But I need more functionality to those lists:
- combine few entity types at one list\query
- retrieve real-time result
- use permanent cache for each entitiy
- it's should be fast

### Introducing to Redis Lists
Redis List — it's structured data, that is stored in Redis hashes (do not confuse native Redis lists with ours).
Okay, let's learn more about this.

### What Redis List is?
Each list have name, for example — 'View', each element of this hash has unique ID and it contains some value.

Here is example of Redis List:

    Hash  ID   Value
    View — 1 — SonataBadBundle:Post;1
    View — 2 — SonataBadBundle:Post;2
    View — 3 — SonataBadBundle:Post;3
    View — 4 — KnpGoodBundle:Article;1
    View — 5 — MyPromoBundle:Promo;1

So, as you can see that each hash:
- starts from 1 id
- all ids are holistic (count of all elements = last element id)

Okay, but how it's can satisfy my requirements?

    Each element of Redis List contains information about entities — repository name, id.
    I can fetch those entities through repository, and then cache it in Memcached for example.

### Features which it gives to you:
- real-time list result
- can store entities in cache permanently
- if new element passed to list, no need to reload old entities from DB
- it allows to list a few types of entities as one list
- it allows to list random entities from list freely, cuz ids are holistic, and also no need to fetch them from DB

## Installation

Add DarkRedisListBundle in your composer.json

```js
{
    "require": {
        "cursedcoder/dark-redis-list-bundle": "*"
    }
}
```

Register the bundle in your `app/AppKernel.php`:

```php
<?php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Dark\RedisListBundle\DarkRedisListBundle(),
    );
)
```

## Collectors
* Single — fetches entities one at a time (1 request per entity)
* Pieces — fetches entities by pieces, for example for list "Post1, Post2, Answer1, Answer2, Post3, Post4" it will take 3 requests

## Configuration reference

```jinja
# app/config.yml
dark_redis_list:
    collector: single # or pieces
    template: DarkRedisListBundle:Pagination:list.html.twig
    time: 604800 # cache lifetime, 0 by default
```

## Credits
I wrote this bundle for my own needs, so don't angry if anything will going not good :P