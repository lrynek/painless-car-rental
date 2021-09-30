<?php
declare(strict_types=1);

namespace App\Repository\Elasticsearch;

use App\Repository\CarRepositoryInterface;
use App\Repository\Elasticsearch\ValueObject\Criteria\MatchAll;
use App\Repository\Elasticsearch\ValueObject\Query;
use App\Service\ElasticsearchClient;
use App\ValueObject\Cars;
use App\ValueObject\Pagination;
use JetBrains\PhpStorm\Pure;

final class ElasticsearchCarRepository implements CarRepositoryInterface
{
	public function __construct(private ElasticsearchClient $client)
	{
	}

	#[Pure]
	public function find(Pagination $pagination): Cars
	{
		$query = new Query;
		$query->setPagination($pagination);
		$query->appendMust(new MatchAll);

		$response = $this->client->search($query);

		return Cars::fromResponse($response);
	}
}
