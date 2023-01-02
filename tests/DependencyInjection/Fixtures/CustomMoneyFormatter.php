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

use Money\Exception\FormatterException;
use Money\Money;
use Money\MoneyFormatter;

class CustomMoneyFormatter implements MoneyFormatter
{
    public function format(Money $money): string
    {
        if ($money->getCurrency()->getCode() !== CustomCurrencies::CODE) {
            throw new FormatterException();
        }

        return 'ZZZ '.$money->getAmount();
    }
}
