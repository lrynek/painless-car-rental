<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\ParamConverter\CriteriaParamConverter;
use App\Controller\ParamConverter\PageParamConverter;
use App\Elasticsearch\ValueObject\Criteria\Criteria;
use App\Enum\Color;
use App\Repository\AdditionalServiceRepositoryInterface;
use App\Repository\CarRepositoryInterface;
use App\ValueObject\PagesTotal;
use App\ValueObject\Pagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

final class DefaultController extends AbstractController
{
	private const PAGE_DEFAULT = 1;

	public function __construct(LoggerInterface $logger = null)
	{
		
	}

	#[Route('/{page}', name: 'app_search', requirements: ['page' => '\d+'], defaults: ['page' => self::PAGE_DEFAULT])]
	#[ParamConverter('pagination', class: PageParamConverter::class)]
	#[ParamConverter('criteria', class: CriteriaParamConverter::class)]
	public function list(
		CarRepositoryInterface               $carRepository,
		AdditionalServiceRepositoryInterface $serviceRepository,
		Pagination                           $pagination,
		Criteria                             $criteria
		
	): Response
	{
		// $this->logger = $logger;

		$cars = $carRepository->find($pagination, $criteria);

		$filtersData = [
			'colors' => Color::all(),
			'services' => $serviceRepository->findAll(),
		];

		if ($cars->empty()) {
			return $this->render('search/emptyList.html.twig', $filtersData);
		}

		$pagesTotal = new PagesTotal($cars, $pagination->resultsPerPage());

		return $this->render('search/list.html.twig', [
				'pagesTotal' => $pagesTotal,
				'pagination' => $pagination,
				'cars' => $cars,
			] + $filtersData
		);
	}
}
