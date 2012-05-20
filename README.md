### How we manage lists at MySQL:
- write query, for example 'SELECT a FROM MyTestBundle:Post a ORDER BY a.created_at'
- use doctrine repository

then we can pass it to the paginator, but no need talk about it now.

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
Hash   ID   Value

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
— real-time list result
— can store entities in cache permanently
— if new element passed to list, no need to reload old entities from DB
— it allows to list a few types of entities as one list
— it allows to list random entities from list freely, cuz ids are holistic, and also no need to fetch them from DB