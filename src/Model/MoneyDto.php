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

namespace Yceruto\MoneyBundle\Model;

use Money\Currency;
use Money\Money;

class MoneyDto
{
    public static function fromMoney(Money $money): self
    {
        return new self($money->getAmount(), $money->getCurrency()->getCode());
    }

    public function toMoney(): Money
    {
        return new Money($this->amount, new Currency($this->currency));
    }

    public function __construct(
        public string $amount = '',
        public string $currency = '',
    ) {
    }
}
