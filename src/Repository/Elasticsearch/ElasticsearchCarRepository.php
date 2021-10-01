<?php
declare(strict_types=1);

namespace App\Repository\Elasticsearch;

use App\Repository\CarRepositoryInterface;
use App\Repository\Elasticsearch\ValueObject\Criteria\Criteria;
use App\Repository\Elasticsearch\ValueObject\Query;
use App\Repository\Elasticsearch\ValueObject\Sorter\RecommendedSorter;
use App\Service\ElasticsearchClient;
use App\ValueObject\Cars;
use App\ValueObject\CriteriaInterface;
use App\ValueObject\Pagination;
use JetBrains\PhpStorm\Pure;

final class ElasticsearchCarRepository implements CarRepositoryInterface
{
	public function __construct(private ElasticsearchClient $client)
	{
	}

	#[Pure]
	public function find(Pagination $pagination, ?CriteriaInterface $criteria = null): Cars
	{
		$query = (new Query)
			->setPagination($pagination)
			->setSorter(new RecommendedSorter)
			->applyCriteria($criteria ?? new Criteria);

		$response = $this->client->search($query);

		return Cars::fromResponse($response);
	}
}
