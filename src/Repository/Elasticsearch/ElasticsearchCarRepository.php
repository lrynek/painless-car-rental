<?php
declare(strict_types=1);

namespace App\Repository\Elasticsearch;

use App\Repository\CarRepositoryInterface;
use App\Repository\Elasticsearch\ValueObject\Criteria\MatchAll;
use App\Repository\Elasticsearch\ValueObject\Query;
use App\Service\ElasticsearchClient;
use App\ValueObject\Cars;
use JetBrains\PhpStorm\Pure;

final class ElasticsearchCarRepository implements CarRepositoryInterface
{
	public function __construct(private ElasticsearchClient $client)
	{
	}

	#[Pure]
	public function findAll(): Cars
	{
		$query = new Query;
		$query->setPagination(1, 1000);
		$query->appendMust(new MatchAll);

		$response = $this->client->search($query);

		return Cars::fromResults($response->results());
	}
}
