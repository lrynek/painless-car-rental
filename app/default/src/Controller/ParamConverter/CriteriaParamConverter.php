<?php
declare(strict_types=1);

namespace App\Controller\ParamConverter;

use App\Elasticsearch\ValueObject\Criteria\ColorsFilter;
use App\Elasticsearch\ValueObject\Criteria\Criteria;
use App\Elasticsearch\ValueObject\Criteria\PhraseFilter;
use App\Elasticsearch\ValueObject\Criteria\ServicesFilter;
use App\Repository\AdditionalServiceRepositoryInterface;
use App\ValueObject\AdditionalServices;
use App\ValueObject\Colors;
use App\ValueObject\Id;
use App\ValueObject\Phrase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

final class CriteriaParamConverter extends AbstractParamConverter
{
	private const PARAM_FILTERS = 'filters';

	private const FILTER_PHRASE = 'phrase';
	private const FILTER_COLORS = 'colors';
	private const FILTER_SERVICES = 'services';

	public function __construct(private AdditionalServiceRepositoryInterface $serviceRepository)
	{
	}

	public function apply(Request $request, ParamConverter $configuration): bool
	{
		$param = $configuration->getName();
		$filters = $request->get(self::PARAM_FILTERS, []);
		$criteria = new Criteria;

		$this->processPhrase($criteria, $filters);
		$this->processColors($criteria, $filters);
		$this->processServices($criteria, $filters);

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

	private function processColors(Criteria $criteria, array $filters): void
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

	private function processServices(Criteria $criteria, mixed $filters): void
	{
		$services = $filters[self::FILTER_SERVICES] ?? [];

		if (empty($services)) {
			return;
		}

		$services = array_values(
			array_map(
				fn(string $id) => $this->serviceRepository->findOneById(new Id((int)$id)),
				$services
			)
		);


		$criteria->addRequired(new ServicesFilter(new AdditionalServices(...$services)));
	}

	private function extractColors(array $colors, string $group): array
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
