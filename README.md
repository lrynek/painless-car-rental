# Painless Car Rental
### Painless / Painful? Own ranking system in PHP/Elasticsearch
![image](https://user-images.githubusercontent.com/36886649/145262691-59852b28-141a-4d1a-ac9c-73bd1e5b00bf.png)

## Purpose and constraints
This is the simple educational project prepared to support my recent presentation during **PHPers Summit 2021** conference and to allow participants to play with Elasticsearch scoring. It is not intended to expose any architectural patterns of the code itself, so please don't stick to the directory structure or the overall code architecture too much 😉.

| [Docplanner Tech](https://docplanner.tech) | [PHPers Summit 2021](https://summit.phpers.pl/pl/) |
| :---:         |     :---:      |
| ![image](https://user-images.githubusercontent.com/36886649/135843518-9d4b2ec1-32dc-4226-a63c-b173d9b0706e.png) | ![image](https://user-images.githubusercontent.com/36886649/135534953-338af09d-d2c6-43ee-9407-137253cc4e13.png) |

## Requirements
- PHP 8.0.9
- Elasticsearch 7.16.0 running on `localhost:9200`

If you need to change the Elasticsearch host the application uses, [it's defined in the `ApiClient` class as a constant](https://github.com/lrynek/phpers-2021/blob/b4a8431ffd73c7417b00d6428ef491c91b45960f/src/Elasticsearch/Service/ApiClient.php#L14) (normally worth passing it from `.env` params file 😉 )

In order to run the project, it is advisable to install an instance of latest stable version of Elasticsearch (it's 7.16.0 version at the moment of the presentation https://www.elastic.co/guide/en/elasticsearch/reference/7.16/index.html)

## Setup
1. Create `cars` index in Elasticsearch ([`Index/Create` HTTP request](https://github.com/lrynek/phpers-2021/blob/main/.elasticsearch-http-requests/Index/Create.http)*)
2. Populate the index with sample cars data ([`Index/Bulk` HTTP request](https://github.com/lrynek/phpers-2021/blob/main/.elasticsearch-http-requests/Index/Bulk.http)*)
3. Go to project's root directory in the terminal
4. `cp .env.dist .env`
5. `symfony server:start`
6. Go to http://127.0.0.1:8000/

>(*) - all HTTP requests can be executed either:
>- from within [PhpStorm's built-in REST HTTP client](https://www.jetbrains.com/help/phpstorm/http-client-in-product-code-editor.html) (samples in [.elasticsearch-http-requests directory](https://github.com/lrynek/phpers-2021/blob/main/.elasticsearch-http-requests))
>- in [Insomnia REST HTTP client](https://insomnia.rest/) (import [insomnia.json file](https://github.com/lrynek/phpers-2021/blob/main/insomnia.json) with all the samples)

## How to play with it?
All Elasticsearch implementation related code is placed in `src/Elasticsearch` directory.

The core ranking logic is built [from specific `Factors` classes](https://github.com/lrynek/phpers-2021/tree/main/src/Elasticsearch/ValueObject/Factor):
- [`RawScoreFactor`](https://github.com/lrynek/phpers-2021/blob/main/src/Elasticsearch/ValueObject/Factor/RawScoreFactor.php) that propagates the originally calculated document score to the overall scoring (as it is being overwritten / replaced by all custom functions) in order to weight it along with other custom factors provided by the developer
- [`DodgePromoFactor`](https://github.com/lrynek/phpers-2021/blob/main/src/Elasticsearch/ValueObject/Factor/DodgePromoFactor.php) that promotes all documents that has `producer` field equal to `Dodge` (you can switch to any other)
- [`ColorRelevanceFactor`](https://github.com/lrynek/phpers-2021/blob/main/src/Elasticsearch/ValueObject/Factor/ColorRelevanceFactor.php) that ranks higher these documents / cars which has more intensive or exclusive color to the ones that are being filtered out on every app's request

Then the `RecommendedSorter` that includes all those ranking factors is [set up in `CarRepository`](https://github.com/lrynek/phpers-2021/blob/b4a8431ffd73c7417b00d6428ef491c91b45960f/src/Elasticsearch/Repository/CarRepository.php#L32) to guarantee it applies to every search request:

```php
<?php
// ...

final class RecommendedSorter implements FactorSorterInterface
{
  // ...

	public function __construct(private ?Factors $factors = null)
	{
		$this->factors ??= new Factors(
			new RawScoreFactor(new Weight(1)),
			new DodgePromoFactor(new Weight(100)),
			new ColorRelevanceFactor(new Weight(50)),
		);
	}

  // ...
}
```

💡 You can add any other factor you want on base of those existing ones.

💡 You can also play with all those factors' weights as well in the `RecommendedSorter` constructor and see the influence on the overall ranking.

💡 In order to get rid of customly ranked results on the listing you can switch to `DefaultSorter` that sorts all results ascending by their `id`.

## Copyrights
Apart from [the project's LICENSE](https://github.com/lrynek/phpers-2021/blob/main/LICENSE), [all car photo samples](https://github.com/lrynek/phpers-2021/tree/main/public/images/cars) used in the project are taken from Google search results and all copyrights applies to their respective authors and shouldn't be used further than private/educational use without their explicit consent.
