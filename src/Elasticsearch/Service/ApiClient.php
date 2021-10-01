<?php
declare(strict_types=1);

namespace App\Elasticsearch\Service;

use App\Elasticsearch\ValueObject\Query;
use App\Elasticsearch\ValueObject\Response;
use App\Enum\Index;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

final class ApiClient implements ApiClientInterface
{
	private const ELASTICSEARCH_HOST = 'http://localhost:9200';

	private const ENDPOINT_SEARCH = '_search';

	public function __construct(private ClientInterface $client)
	{
	}

	public function search(Index $index, Query $query): Response
	{
		$url = $this->createEndpointUrl($index->value(), self::ENDPOINT_SEARCH);
		$response = $this->client->post($url, ['json' => $query->toArray()]);

		return new Response($this->convertResponseToArray($response));
	}

	private function createEndpointUrl(string $index, string $endpoint): string
	{
		return implode(DIRECTORY_SEPARATOR, [self::ELASTICSEARCH_HOST, $index, $endpoint]);
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
