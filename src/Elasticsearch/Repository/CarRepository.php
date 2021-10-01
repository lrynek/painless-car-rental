<?php
declare(strict_types=1);

namespace App\Elasticsearch\Repository;

use App\Elasticsearch\Service\ApiClientInterface;
use App\Elasticsearch\ValueObject\Criteria\Criteria;
use App\Elasticsearch\ValueObject\Query;
use App\Elasticsearch\ValueObject\Sorter\RecommendedSorter;
use App\Enum\Index;
use App\Repository\CarRepositoryInterface;
use App\ValueObject\Cars;
use App\ValueObject\CriteriaInterface;
use App\ValueObject\Pagination;
use JetBrains\PhpStorm\Pure;

final class CarRepository implements CarRepositoryInterface
{
	private Index $index;

	public function __construct(private ApiClientInterface $client)
	{
		$this->index = Index::CARS();
	}

	#[Pure]
	public function find(Pagination $pagination, ?CriteriaInterface $criteria = null): Cars
	{
		$query = (new Query)
			->setPagination($pagination)
			->setSorter(new RecommendedSorter)
			->applyCriteria($criteria ?? new Criteria);

		$response = $this->client->search($this->index, $query);

		return Cars::fromResponse($response);
	}
}
