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

namespace Yceruto\MoneyBundle\Currencies;

use AppendIterator;
use Money\Currencies;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;
use Traversable;

/**
 * Aggregates several currency repositories.
 *
 * This is a clone of Money\AggregateCurrencies class with iterable argument.
 */
class AggregateCurrencies implements Currencies
{
    /**
     * @param iterable<Currencies> $currencies
     */
    public function __construct(
        private readonly iterable $currencies,
    ) {
    }

    public function contains(Currency $currency): bool
    {
        foreach ($this->currencies as $currencies) {
            if ($currencies->contains($currency)) {
                return true;
            }
        }

        return false;
    }

    public function subunitFor(Currency $currency): int
    {
        foreach ($this->currencies as $currencies) {
            if ($currencies->contains($currency)) {
                return $currencies->subunitFor($currency);
            }
        }

        throw new UnknownCurrencyException('Cannot find currency ' . $currency->getCode());
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): Traversable
    {
        /** @psalm-var AppendIterator&Traversable<int|string, Currency> $iterator */
        $iterator = new AppendIterator();

        foreach ($this->currencies as $currencies) {
            $currencyIterator = $currencies->getIterator();
            /** @psalm-var AppendIterator&Traversable<int|string, Currency> $currencyIterator */
            $iterator->append($currencyIterator);
        }

        return $iterator;
    }
}
