<?php
declare(strict_types=1);

namespace App\Elasticsearch\Service;

use App\Elasticsearch\ValueObject\Query;
use App\Elasticsearch\ValueObject\Response;
use App\Enum\Index;

interface ApiClientInterface
{
	public function search(Index $index, Query $query): Response;
}
