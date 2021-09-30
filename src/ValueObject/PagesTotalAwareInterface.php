<?php
declare(strict_types=1);

namespace App\ValueObject;

interface PagesTotalAwareInterface
{
	public function total(): int;
}
