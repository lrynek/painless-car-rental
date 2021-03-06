<?php
declare(strict_types=1);

namespace App\Repository;

use App\ValueObject\AdditionalService;
use App\ValueObject\AdditionalServices;
use App\ValueObject\Id;
use App\ValueObject\Name;

final class InMemoryAdditionalServiceRepository implements AdditionalServiceRepositoryInterface
{
	private array $services;

	public function __construct()
	{
		$this->services = [
			1 => new AdditionalService(new Id(1), new Name('extreme driving + drift')),
			2 => new AdditionalService(new Id(2), new Name('neck massage')),
			3 => new AdditionalService(new Id(3), new Name('instructor')),
			4 => new AdditionalService(new Id(4), new Name('sport driver')),
			5 => new AdditionalService(new Id(5), new Name('cup of coffee')),
			6 => new AdditionalService(new Id(6), new Name('historical briefing')),
			7 => new AdditionalService(new Id(7), new Name('mechanical inspection')),
			8 => new AdditionalService(new Id(8), new Name('car tuning lesson')),
		];
	}

		public function findOneById(Id $id): AdditionalService
	{
		return $this->services[$id->value()] ?? throw new \OutOfRangeException;
	}

		public function findAll(): AdditionalServices
	{
		return new AdditionalServices(...$this->services);
	}
}
