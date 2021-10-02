<?php
declare(strict_types=1);

namespace App\Repository;

use App\ValueObject\AdditionalService;
use App\ValueObject\AdditionalServices;
use App\ValueObject\Id;

interface AdditionalServiceRepositoryInterface
{
	public function findOneById(Id $id): AdditionalService;

	public function findAll(): AdditionalServices;
}
