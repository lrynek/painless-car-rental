<?php
declare(strict_types=1);

namespace App\Controller\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;

abstract class AbstractParamConverter implements ParamConverterInterface
{
	public function supports(ParamConverter $configuration): bool
	{
		return static::class === $configuration->getClass();
	}
}
