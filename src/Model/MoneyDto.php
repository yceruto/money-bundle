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

    public static function fromCurrency(string $currency): self
    {
        return new self(0, $currency);
    }

    public static function fromAmount(int|string $amount): self
    {
        return new self($amount, 'EUR');
    }

    public function __construct(
        public int|string|null $amount = null,
        public ?string $currency = null,
    ) {
    }

    public function toMoney(): Money
    {
        assert(null !== $this->amount && '' !== $this->amount, 'Empty number is invalid');
        assert(is_numeric($this->amount), 'Invalid digit a found');
        assert(null !== $this->currency && '' !== $this->currency, 'Currency must be a non-empty-string value');

        return new Money($this->amount, new Currency($this->currency));
    }
}
