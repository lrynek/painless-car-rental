<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DefaultController extends AbstractController
{
	/**
	 * @Route("/")
	 */
	public function search(): Response
	{
		return $this->render('base.html.twig', ['companyName' => 'Painless Car Rental']);
	}
}
