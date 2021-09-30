<?php
declare(strict_types=1);

namespace App\Repository;

use App\ValueObject\Cars;
use App\ValueObject\Pagination;

interface CarRepositoryInterface
{
	public function find(Pagination $pagination): Cars;
}
