# Tonic And Friends

A prototype tamagotchi-style server

## Running it

The easiest way is with docker-compose:

```
$ docker-compose up app
```

The application will be available on `localhost:8000`

This runs using the PHP built in server - this is not intended for production.

To run the tests:

```
$ docker-compose run tests
```

## Methods

### Create Pet
```
POST /pet
{
  "name": "My Pet's Name",
  "type": "cat"|"dog|"snake"
}
```

### List all my pets
```
GET /pet
```

### Get a single pet
```
GET /pet/{id}
```

### Feed a pet
```
POST /pet/{id}/feed
```

### Stroke a pet
```
POST /pet/{id}/stroke
```

## Authentication

Authentication is not implemented but you can pass a header of `user-id` to set your user id. For convenience in testing, this defaults to 1 if it is not passed.

## Database

For development purposes, I am using a sqlite database which gets created during the docker build

## Main logic

The main logic is in the `Pet` domain entity in `src/Entity/Pet.php`.

Happiness and Hunger is returned on a 0 to 1 scale, and is never allowed to exceed these bounds.

Feed and Stroke operations are stored as Events on the Pet. When calculating the happiness or hunger of a pet, we calculate it by looping through the events. This is somewhat of an Event Sourcing approach. It has potential implications for performance however, so denormalizing that and storing the last event date and the last calculated hunger/happiness on the main entity might be a worthwhile improvement for the future.

The two functions for calculating the metrics, `getHappiness` and `getHunger` are similar and I considered finding a way to extract out the logic, or move part of the logic into the Event class (potentially having separate event classes for `Stroke` and `Feed`). But I decided that this would make the algorithm harder to understand and hide the details more than it would be helpful.

Different pet types are found in `src/Entity/Pet/*.php`. Each Pet is a subclass of the abstract `Pet` class and has to implement the methods for the different reduce/increase rates.