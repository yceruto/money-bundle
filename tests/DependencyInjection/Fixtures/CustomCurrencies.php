<?php

declare(strict_types=1);

/*
 * This file is part of the MoneyBundle package.
 *
 * (c) Yonel Ceruto Gonzalez <yonelceruto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yceruto\MoneyBundle\Tests\DependencyInjection\Fixtures;

use ArrayIterator;
use Money\Currencies;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;
use Traversable;

class CustomCurrencies implements Currencies
{
    public const CODE = 'ZZZ';

    public function contains(Currency $currency): bool
    {
        return $currency->getCode() === self::CODE;
    }

    public function subunitFor(Currency $currency): int
    {
        if ($currency->getCode() !== self::CODE) {
            throw new UnknownCurrencyException();
        }

        return 0;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator([new Currency(self::CODE)]);
    }
}
