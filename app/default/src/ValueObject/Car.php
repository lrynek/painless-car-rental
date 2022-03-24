<?php
declare(strict_types=1);

namespace App\ValueObject;

final class Car
{
	public function __construct(
		private Producer           $producer,
		private Model              $model,
		private Picture            $picture,
		private ProductionYear     $productionYear,
		private Colors             $colors,
		private AdditionalServices $services,
	)
	{
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

	public function services(): AdditionalServices
	{
		return $this->services;
	}
}
