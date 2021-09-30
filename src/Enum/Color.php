<?php
declare(strict_types=1);

namespace App\Enum;

/**
 * @method static self BLACK()
 * @method static self RED()
 * @method static self SILVER()
 * @method static self YELLOW()
 */
final class Color extends AbstractEnum
{
	private const BLACK = 'black';
	private const RED = 'red';
	private const SILVER = 'silver';
	private const YELLOW = 'yellow';
}
