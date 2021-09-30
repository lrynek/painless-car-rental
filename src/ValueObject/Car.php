<?php
declare(strict_types=1);

namespace App\ValueObject;

use App\Repository\Elasticsearch\ValueObject\Result;

final class Car
{
	public static function fromResult(Result $result): self
	{
		$values = $result->source();

		return new self(
			new Producer($values['producer'] ?? ''),
			new Model($values['model'] ?? ''),
			new Picture($values['picture'] ?? ''),
			new ProductionYear($values['production_year'] ?? 0),
			Colors::fromArray($values['colors'] ?? []),
		);
	}

	public function producer(): Producer
	{
		return $this->producer;
	}

	public function model(): Model
	{
		return $this->model;
	}

	public function fullName(): string
	{
		return \sprintf('%s %s', $this->producer, $this->model);
	}

	public function picture(): Picture
	{
		return $this->picture;
	}

	public function productionYear(): ProductionYear
	{
		return $this->productionYear;
	}

	public function colors(): Colors
	{
		return $this->colors;
	}

	private function __construct(
		private Producer       $producer,
		private Model          $model,
		private Picture        $picture,
		private ProductionYear $productionYear,
		private Colors         $colors,
	)
	{
	}
}
