<?php
declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

final class PaginationEventSubscriber implements EventSubscriberInterface
{
	private const FILTERS = 'filters';
	private const FIRST_PAGE = 1;
	private const PAGE = 'page';
	private const ROUTE_APP_SEARCH = 'app_search';

	public function __construct(private RouterInterface $router)
	{
	}

	public static function getSubscribedEvents(): array
	{
		return [
			KernelEvents::REQUEST => ['onKernelRequest']
		];
	}

	public function onKernelRequest(RequestEvent $event): void
	{
		if (false === $event->isMainRequest()) {
			return;
		}

		$request = $event->getRequest();
		$route = $request->attributes->get('_route');

		if (self::ROUTE_APP_SEARCH !== $route) {
			return;
		}

		$routeParams = $request->attributes->get('_route_params', []);
		$page = (int)($routeParams[self::PAGE] ?? 0);

		if (false === $page > self::FIRST_PAGE) {
			return;
		}

		$requestQueryParams = $request->query->all();
		$actualFilters = $requestQueryParams[self::FILTERS] ?? [];

		if (empty($actualFilters)) {
			return;
		}

		$referer = $request->headers->get('referer', '');
		$refererQueryString = parse_url($referer, PHP_URL_QUERY);

		if (empty($refererQueryString)) {
			return;
		}

		parse_str($refererQueryString, $previousQueryParams);
		$previousFilters = $previousQueryParams[self::FILTERS] ?? [];

		if (empty($previousFilters)) {
			return;
		}

		ksort($actualFilters);
		ksort($previousFilters);
		$actualFiltersSerialized = md5(serialize($actualFilters));
		$previousFiltersSerialized = md5(serialize($previousFilters));
		$filtersChanged = $actualFiltersSerialized !== $previousFiltersSerialized;

		if (false === $filtersChanged) {
			return;
		}

		$routeParams[self::PAGE] = self::FIRST_PAGE;
		$routeParams = array_merge($routeParams, $requestQueryParams);
		$url = $this->router->generate(self::ROUTE_APP_SEARCH, $routeParams);
		$response = new RedirectResponse($url);
		$event->setResponse($response);
	}
}
