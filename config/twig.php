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
use Money\MoneyFormatter;
use Yceruto\MoneyBundle\DependencyInjection\Compiler\CurrenciesPass;
use Yceruto\MoneyBundle\Twig\MoneyTwigExtension;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set(MoneyTwigExtension::class)
            ->args([service(MoneyFormatter::class)])
            ->tag('twig.extension')
    ;
};
