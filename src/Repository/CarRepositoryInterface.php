<?php
declare(strict_types=1);

namespace App\Repository;

use App\ValueObject\Cars;

interface CarRepositoryInterface
{
	public function findAll(): Cars;
}
