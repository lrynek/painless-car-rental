<?php
declare(strict_types=1);

namespace App\Controller\ParamConverter;

use App\Repository\Elasticsearch\ValueObject\Criteria\ColorsFilter;
use App\Repository\Elasticsearch\ValueObject\Criteria\Criteria;
use App\Repository\Elasticsearch\ValueObject\Criteria\PhraseFilter;
use App\ValueObject\Colors;
use App\ValueObject\Phrase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

final class CriteriaParamConverter extends AbstractParamConverter
{
	private const PARAM_FILTERS = 'filters';

	private const FILTER_PHRASE = 'phrase';
	private const FILTER_COLORS = 'colors';

	public function apply(Request $request, ParamConverter $configuration): bool
	{
		$param = $configuration->getName();
		$filters = $request->get(self::PARAM_FILTERS, []);
		$criteria = new Criteria;

		$this->processPhrase($criteria, $filters);
		$this->processColors($criteria, $filters);

		$request->attributes->set($param, $criteria);

		return true;
	}

	private function processPhrase(Criteria $criteria, mixed $filters): void
	{
		$phrase = $filters[self::FILTER_PHRASE] ?? '';

		if (empty($phrase)) {
			return;
		}

		$criteria->addRequired(new PhraseFilter(new Phrase($phrase)));
	}

	protected function processColors(Criteria $criteria, array $filters): void
	{
		$colors = $filters[self::FILTER_COLORS] ?? [];

		if (empty($colors)) {
			return;
		}

		$required = $this->extractColors($colors, 'required');
		$additional = $this->extractColors($colors, 'additional');
		$excluded = $this->extractColors($colors, 'excluded');

		$criteria
			->addRequired(new ColorsFilter(Colors::fromArray($required)))
			->addAdditional(new ColorsFilter(Colors::fromArray($additional)))
			->addExcluded(new ColorsFilter(Colors::fromArray($excluded)));
	}

	protected function extractColors(array $colors, string $group): array
	{
		return array_map(
			static fn(string $color): string => str_replace($group, '', $color),
			array_filter(
				$colors,
				static fn(string $color): bool => str_starts_with($color, $group)
			)
		);
	}
}
