<?php
declare(strict_types=1);

namespace App\Elasticsearch\Hydrator;

use App\Elasticsearch\ValueObject\Result;
use App\Repository\AdditionalServiceRepositoryInterface;
use App\ValueObject\AdditionalServices;
use App\ValueObject\Car;
use App\ValueObject\Colors;
use App\ValueObject\Id;
use App\ValueObject\Model;
use App\ValueObject\Picture;
use App\ValueObject\Producer;
use App\ValueObject\ProductionYear;

final class CarHydrator implements CarHydratorInterface
{
	public function __construct(private AdditionalServiceRepositoryInterface $serviceRepository)
	{
	}

	public function hydrate(Result $result): Car
	{
		$values = $result->source();

		$services = [];
		foreach ($values['service_ids'] ?? [] as $id) {
			$services[] = $this->serviceRepository->findOneById(new Id($id));
		}

		return new Car(
			new Producer($values['producer'] ?? ''),
			new Model($values['model'] ?? ''),
			new Picture($values['picture'] ?? ''),
			new ProductionYear($values['production_year'] ?? 0),
			Colors::fromArray($values['colors'] ?? []),
			new AdditionalServices(...$services)
		);
	}
}
