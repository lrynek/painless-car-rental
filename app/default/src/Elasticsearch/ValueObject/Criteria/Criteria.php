<?php
declare(strict_types=1);

namespace App\Elasticsearch\ValueObject\Criteria;

use App\ValueObject\CriteriaInterface;

final class Criteria implements CriteriaInterface
{
	private array $required = [];
	private array $additional = [];
	private array $excluded = [];

	public function addRequired(Criterion $criterion): self
	{
		$this->required[$criterion::class] = $criterion;

		return $this;
	}

	public function addAdditional(Criterion $criterion): self
	{
		$this->additional[$criterion::class] = $criterion;

		return $this;
	}

	public function addExcluded(Criterion $criterion): self
	{
		$this->excluded[$criterion::class] = $criterion;

		return $this;
	}

	public function required(): array
	{
		return $this->required;
	}

	public function additional(): array
	{
		return $this->additional;
	}

	public function excluded(): array
	{
		return $this->excluded;
	}
}
