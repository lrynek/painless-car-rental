<?php
declare(strict_types=1);

namespace App\Elasticsearch\ValueObject\Criteria;

use App\ValueObject\Phrase;

final class PhraseFilter implements Criterion
{
	public function __construct(private Phrase $phrase)
	{
	}

	public function definition(): array
	{
		return [
			'query_string' => [
				'query' => $this->phrase->value(),
				'fields' => [
					'producer',
					'model',
				]
			]
		];
	}
}
