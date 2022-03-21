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
use Psr\Log\LoggerInterface;

final class CarRepository implements CarRepositoryInterface
{
	private Index $index;

	public function __construct(
		private ApiClientInterface    $client,
		private CarsHydratorInterface $hydrator,
		LoggerInterface $logger = null
	)
	{
		$this->index = Index::CARS();
		$this->logger = $logger;
	}

	public function find(Pagination $pagination, ?CriteriaInterface $criteria = null): Cars
	{
		$query = new Query($pagination, $criteria ?? new Criteria);
		$query->setSorter(new RecommendedSorter);

		$response = $this->client->search($this->index, $query);

		$this->logger->info('Found: [' . count($response->results()) . ']' . ' for query: ' . json_encode($query->toArray()));

		$hydra = $this->hydrator->hydrate($response);

		// var_dump($hydra); die;
		return $hydra;
	}
}
