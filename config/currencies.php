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
use Money\Currencies\AggregateCurrencies;
use Money\Currencies\BitcoinCurrencies;
use Money\Currencies\CryptoCurrencies;
use Money\Currencies\CurrencyList;
use Money\Currencies\ISOCurrencies;
use Yceruto\MoneyBundle\DependencyInjection\Compiler\CurrenciesPass;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set(AggregateCurrencies::class)
            ->args([abstract_arg('currencies')])

        ->set(CurrencyList::class)
            ->args([param('.money_currencies')])
            ->tag(CurrenciesPass::TAG)

        ->set(ISOCurrencies::class)
            ->tag(CurrenciesPass::TAG)

        ->set(BitcoinCurrencies::class)
            ->tag(CurrenciesPass::TAG)

        ->set(CryptoCurrencies::class)
            ->tag(CurrenciesPass::TAG)

        ->alias(Currencies::class, AggregateCurrencies::class)
    ;
};
