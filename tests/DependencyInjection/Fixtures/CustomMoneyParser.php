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

use Money\Currency;
use Money\Exception\ParserException;
use Money\Money;
use Money\MoneyParser;

class CustomMoneyParser implements MoneyParser
{
    public function parse(string $money, ?Currency $fallbackCurrency = null): Money
    {
        if (!str_starts_with($money, CustomCurrencies::CODE)) {
            throw new ParserException();
        }

        $decimal = trim(str_replace(CustomCurrencies::CODE, '', $money));

        return new Money($decimal, new Currency(CustomCurrencies::CODE));
    }
}
