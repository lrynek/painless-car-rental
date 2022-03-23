<?php
declare(strict_types=1);

namespace App\ValueObject;

final class PagesTotal
{
	private const MIN_PAGE = 1;

	private int $value;

	public function __construct(PagesTotalAwareInterface $results, ResultsPerPage $resultsPerPage)
	{
		if ($resultsPerPage->value() > $results->total()) {
			$this->value = self::MIN_PAGE;
		} else {
			$this->value = (int)floor($results->total() / $resultsPerPage->value());
		}

		$this->validate();
	}

	public function value(): int
	{
		return $this->value;
	}

	public function isPreviousPageDisabled(int|Page $page): bool
	{
		$page = $page instanceof Page ? $page->value() : $page;

		return self::MIN_PAGE === $page;
	}

	public function isNextPageDisabled(int|Page $page): bool
	{
		$page = $page instanceof Page ? $page->value() : $page;

		return $this->value === $page;
	}

	private function validate(): void
	{
		if (self::MIN_PAGE > $this->value) {
			throw new \InvalidArgumentException;
		}
	}
}
