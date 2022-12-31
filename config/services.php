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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Money\Currencies;
use Money\Currencies\BitcoinCurrencies;
use Money\Currencies\CryptoCurrencies;
use Money\Currencies\CurrencyList;
use Money\Currencies\ISOCurrencies;
use Yceruto\MoneyBundle\Currencies\AggregateCurrencies;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set(AggregateCurrencies::class)
            ->args([tagged_iterator('money.currencies')])

        ->set(CurrencyList::class)
            ->args([param('.money_currencies')])
            ->tag('money.currencies')

        ->set(ISOCurrencies::class)
            ->tag('money.currencies')

        ->set(BitcoinCurrencies::class)
            ->tag('money.currencies')

        ->set(CryptoCurrencies::class)
            ->tag('money.currencies')

        ->alias(Currencies::class, AggregateCurrencies::class)
    ;
};
