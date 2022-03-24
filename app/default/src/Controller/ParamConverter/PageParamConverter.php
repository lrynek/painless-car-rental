<?php
declare(strict_types=1);

namespace App\Controller\ParamConverter;

use App\ValueObject\Page;
use App\ValueObject\Pagination;
use App\ValueObject\ResultsPerPage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

final class PageParamConverter extends AbstractParamConverter
{
	private const PARAM_PAGE = 'page';
	private const PAGE_DEFAULT = 1;
	private const RESULTS_PER_PAGE = 3;

	public function apply(Request $request, ParamConverter $configuration): bool
	{
		$param = $configuration->getName();

		$page = new Page((int)$request->get(self::PARAM_PAGE, self::PAGE_DEFAULT));
		$resultsPerPage = new ResultsPerPage(self::RESULTS_PER_PAGE);
		$pagination = new Pagination($page, $resultsPerPage);

		$request->attributes->set($param, $pagination);

		return true;
	}
}
