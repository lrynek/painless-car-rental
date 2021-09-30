<?php
declare(strict_types=1);

namespace App\Service;

use App\Repository\Elasticsearch\ValueObject\Query;
use App\Repository\Elasticsearch\ValueObject\Response;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

final class ElasticsearchClient
{
	private const ELASTICSEARCH_HOST = 'http://localhost:9200';
	private const ELASTICSEARCH_SEARCH = '_search';
	private const INDEX = 'cars';

	public function __construct(private ClientInterface $client)
	{
	}

	public function search(Query $query): Response
	{
		$url = $this->createEndpointUrl(self::ELASTICSEARCH_SEARCH);
		$response = $this->client->post($url, ['json' => $query->toArray()]);

		return new Response($this->convertResponseToArray($response));
	}

	private function createEndpointUrl(string $endpoint): string
	{
		return implode(DIRECTORY_SEPARATOR, [self::ELASTICSEARCH_HOST, self::INDEX, $endpoint]);
	}

	private function convertResponseToArray(ResponseInterface $response): array
	{
		$contentType = $response->getHeader('content-type')[0] ?? '';
		$stream = $response->getBody();

		if (str_contains($contentType, 'text/plain')) {
			return \explode("\n", (string)$stream) ?? [];
		}

		return \json_decode(json: (string)$stream, associative: true, flags: JSON_THROW_ON_ERROR) ?? [];
	}
}
