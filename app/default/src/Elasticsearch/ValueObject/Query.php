<?php
declare(strict_types=1);

namespace App\Elasticsearch\ValueObject;

use App\Elasticsearch\ValueObject\Criteria\ColorsFilter;
use App\Elasticsearch\ValueObject\Criteria\Criterion;
use App\Elasticsearch\ValueObject\Factor\FactorInterface;
use App\Elasticsearch\ValueObject\Sorter\DefaultSorter;
use App\Elasticsearch\ValueObject\Sorter\FactorSorterInterface;
use App\Elasticsearch\ValueObject\Sorter\SorterInterface;
use App\ValueObject\CriteriaInterface;
use App\ValueObject\Pagination;

final class Query
{
	private const DEFAULT_SOURCE = true;
	private const DEFAULT_EXPLAIN = false;
	private const DEFAULT_FROM = 0;
	private const DEFAULT_SIZE = 10;
	private const DEFAULT_BOOST_MODE = 'replace';
	private const DEFAULT_SCORE_MODE = 'sum';
	private const DEFAULT_MIN_SCORE = 0;

	private array $structure = [
		'_source' => self::DEFAULT_SOURCE,
		'explain' => self::DEFAULT_EXPLAIN,
		'from' => self::DEFAULT_FROM,
		'size' => self::DEFAULT_SIZE,
		'query' => [
			'function_score' => [
				'query' => [
					'bool' => [
						'must' => [],
						'must_not' => [],
						'should' => [],
						'filter' => [],
					],
				],
				'functions' => [],
				'boost_mode' => self::DEFAULT_BOOST_MODE,
				'score_mode' => self::DEFAULT_SCORE_MODE,
				'min_score' => self::DEFAULT_MIN_SCORE,
			],
		],
		'sort' => [],
	];
	private CriteriaInterface $criteria;

	public function __construct(Pagination $pagination, CriteriaInterface $criteria)
	{
		$this
			->setPagination($pagination)
			->applyCriteria($criteria)
			->setSorter(new DefaultSorter);
	}

	public function setSource(bool|array $source): self
	{
		$this->structure['_source'] = $source;

		return $this;
	}

	public function setExplain(bool $explain): self
	{
		$this->structure['explain'] = $explain;

		return $this;
	}

	private function setPagination(Pagination $pagination): self
	{
		$page = $pagination->page()->value();
		$size = $pagination->resultsPerPage()->value();
		$from = ($page - 1) * $size;

		$this->structure['from'] = $from;
		$this->structure['size'] = $size;

		return $this;
	}

	public function setSorter(SorterInterface $sorter): self
	{
		$definition = $sorter->definition();

		if (false === empty($definition)) {
			$this->structure['sort'] = $definition;
		}

		if ($sorter instanceof FactorSorterInterface) {
			/** @var FactorInterface $factor */
			foreach ($sorter->factors() as $factor) {
				$this->appendFunction($factor);
			}
		}

		return $this;
	}

	public function appendFunction(FactorInterface $factor): self
	{
		$function = $factor->definition($this);

		if (false === empty($function)) {
			$this->structure['query']['function_score']['functions'][] = $function;
		}

		return $this;
	}

	public function setMaxBoost(int $maxBoost): self
	{
		$this->structure['query']['function_score']['max_boost'] = $maxBoost;

		return $this;
	}

	public function setBoostMode(string $boostMode): self
	{
		$this->structure['query']['function_score']['boost_mode'] = $boostMode;

		return $this;
	}

	public function setScoreMode(string $scoreMode): self
	{
		$this->structure['query']['function_score']['score_mode'] = $scoreMode;

		return $this;
	}

	public function setMinScore(int $minScore): self
	{
		$this->structure['query']['function_score']['min_score'] = $minScore;

		return $this;
	}

	public function toArray(): array
	{
		return $this->structure;
	}

	public function requiredColors(): array
	{
		$requiredColors = [];
		foreach ($this->criteria->required() as $requiredCriterion) {
			if ($requiredCriterion instanceof ColorsFilter) {
				$requiredColors = $requiredCriterion->colors()->toArray();
			}
		}

		return $requiredColors;
	}

	private function applyCriteria(CriteriaInterface $criteria): self
	{
		$this->criteria = $criteria;

		foreach ($criteria->required() as $requiredCriterion) {
			$this->appendMust($requiredCriterion);
		}

		foreach ($criteria->additional() as $additionalCriterion) {
			$this->appendShould($additionalCriterion);
		}

		foreach ($criteria->excluded() as $excludedCriterion) {
			$this->appendMustNot($excludedCriterion);
		}

		return $this;
	}

	private function appendMust(Criterion $criterion): self
	{
		$definition = $criterion->definition();

		if (isset($definition['terms'])) {
			$termsQuery = $definition['terms'];

			$definition = ['bool' => ['must' => []]];

			foreach ($termsQuery as $field => $terms) {
				foreach ($terms as $term) {
					$definition['bool']['must'][] = [
						'term' => [
							$field => $term,
						],
					];
				}
			}
		}

		$this->structure['query']['function_score']['query']['bool']['must'][] = $definition;

		return $this;
	}

	private function appendMustNot(Criterion $criterion): self
	{
		$this->structure['query']['function_score']['query']['bool']['must_not'][] = $criterion->definition();

		return $this;
	}

	private function appendShould(Criterion $criterion): self
	{
		$this->structure['query']['function_score']['query']['bool']['should'][] = $criterion->definition();

		return $this;
	}

	private function appendFilter(Criterion $criterion): self
	{
		$this->structure['query']['function_score']['query']['bool']['filter'][] = $criterion->definition();

		return $this;
	}
}
