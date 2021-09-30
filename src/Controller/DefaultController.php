<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\CarRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DefaultController extends AbstractController
{
	#[Route('/')]
	public function list(CarRepositoryInterface $carRepository): Response
	{
		$cars = $carRepository->findAll();

		return $this->render('search/list.html.twig', [
			'cars' => $cars,
		]);
	}
}
