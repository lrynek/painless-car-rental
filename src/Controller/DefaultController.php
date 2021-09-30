<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\CarRepositoryInterface;
use App\ValueObject\Page;
use App\ValueObject\PagesTotal;
use App\ValueObject\Pagination;
use App\ValueObject\ResultsPerPage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DefaultController extends AbstractController
{
	private const PARAM_PAGE = 'page';
	private const PAGE_DEFAULT = 1;
	private const RESULTS_PER_PAGE = 4;

	#[Route('/{page}', requirements: ['page' => '\d+'])]
	public function list(Request $request, CarRepositoryInterface $carRepository): Response
	{
		$pagination = $this->pagination($request);
		$cars = $carRepository->find($pagination);
		$pagesTotal = new PagesTotal($cars, $pagination->resultsPerPage());

		return $this->render('search/list.html.twig', [
			'pagesTotal' => $pagesTotal,
			'pagination' => $pagination,
			'cars' => $cars,
		]);
	}

	protected function pagination(Request $request): Pagination
	{
		$page = new Page((int)$request->get(self::PARAM_PAGE, self::PAGE_DEFAULT));
		$resultsPerPage = new ResultsPerPage(self::RESULTS_PER_PAGE);

		return new Pagination($page, $resultsPerPage);
	}
}
