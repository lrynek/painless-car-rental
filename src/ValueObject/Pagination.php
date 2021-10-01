<?php
declare(strict_types=1);

namespace App\ValueObject;

final class Pagination
{
	private const MAX_TOTAL = 9999;

	public function __construct(
		private Page $page,
		private ResultsPerPage $resultsPerPage,
	)
	{
		$this->validate();
	}

	public function page(): Page
	{
		return $this->page;
	}

	public function resultsPerPage(): ResultsPerPage
	{
		return $this->resultsPerPage;
	}

	private function validate(): void
	{
		$total = ($this->page->value() - 1) * $this->resultsPerPage->value();
		if (self::MAX_TOTAL < $total) {
			throw new \InvalidArgumentException;
		}
	}
}
