<?php
declare(strict_types=1);

namespace App\Repository\Elasticsearch\ValueObject;

use App\Repository\Elasticsearch\ValueObject\Criteria\Criterion;

final class Query
{
	private const DEFAULT_SOURCE = true;
	private const DEFAULT_EXPLAIN = false;
	private const DEFAULT_FROM = 0;
	private const DEFAULT_SIZE = 10;

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
						'should' => [],
						'filter' => [],
					],
				],
				'functions' => [],
				'boost_mode' => 'replace',
				'score_mode' => 'sum',
				'min_score' => 0,
			],
		],
		'sort' => [],
	];

	public function __construct(array $structure = [])
	{
		$this->structure = array_merge($this->structure, $structure);
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

	public function setPagination(int $page, int $documentsPerPage): void
	{
		$this->structure['from'] = ($page - 1) * $documentsPerPage;
		$this->structure['size'] = $documentsPerPage;
	}

	public function appendMust(Criterion $criterion): void
	{
		$this->structure['query']['function_score']['query']['bool']['must'][] = $criterion->definition();
	}

	public function appendShould(Criterion $criterion): void
	{
		$this->structure['query']['function_score']['query']['bool']['should'][] = $criterion->definition();
	}

	public function appendFilter(Criterion $criterion): void
	{
		$this->structure['query']['function_score']['query']['bool']['filter'][] = $criterion->definition();
	}

	public function appendFunction(array $function): void
	{
		if (false === empty($function)) {
			$this->structure['query']['function_score']['functions'][] = $function;
		}
	}

	public function setSort(array $sort): void
	{
		if (false === empty($sort)) {
			$this->structure['sort'] = $sort;
		}
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
}
