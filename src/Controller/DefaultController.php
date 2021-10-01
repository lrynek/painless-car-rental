<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\ParamConverter\CriteriaParamConverter;
use App\Controller\ParamConverter\PageParamConverter;
use App\Enum\Color;
use App\Repository\CarRepositoryInterface;
use App\Repository\Elasticsearch\ValueObject\Criteria\Criteria;
use App\ValueObject\PagesTotal;
use App\ValueObject\Pagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DefaultController extends AbstractController
{
	private const PAGE_DEFAULT = 1;

	#[Route('/{page}', requirements: ['page' => '\d+'], defaults: ['page' => self::PAGE_DEFAULT])]
	#[ParamConverter('pagination', class: PageParamConverter::class)]
	#[ParamConverter('criteria', class: CriteriaParamConverter::class)]
	public function list(CarRepositoryInterface $carRepository, Pagination $pagination, Criteria $criteria): Response
	{
		$cars = $carRepository->find($pagination, $criteria);
		$pagesTotal = new PagesTotal($cars, $pagination->resultsPerPage());

		return $this->render('search/list.html.twig', [
			'pagesTotal' => $pagesTotal,
			'pagination' => $pagination,
			'cars' => $cars,
			'colors' => Color::all(),
		]);
	}
}
