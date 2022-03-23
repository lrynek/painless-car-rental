<?php
declare(strict_types=1);

namespace App\Elasticsearch\Repository;

use App\Elasticsearch\Hydrator\CarsHydratorInterface;
use App\Elasticsearch\Service\ApiClientInterface;
use App\Elasticsearch\ValueObject\Criteria\Criteria;
use App\Elasticsearch\ValueObject\Query;
use App\Elasticsearch\ValueObject\Sorter\RecommendedSorter;
use App\Enum\Index;
use App\Repository\CarRepositoryInterface;
use App\ValueObject\Cars;
use App\ValueObject\CriteriaInterface;
use App\ValueObject\Pagination;

final class CarRepository implements CarRepositoryInterface
{
	private Index $index;

	public function __construct(
		private ApiClientInterface    $client,
		private CarsHydratorInterface $hydrator
	)
	{
		$this->index = Index::CARS();
	}

	public function find(Pagination $pagination, ?CriteriaInterface $criteria = null): Cars
	{
		$query = new Query($pagination, $criteria ?? new Criteria);
		$query->setSorter(new RecommendedSorter);

		$response = $this->client->search($this->index, $query);

		return $this->hydrator->hydrate($response);
	}
}
