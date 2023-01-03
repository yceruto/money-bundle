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

use Money\Converter;
use Money\Currencies;
use Money\Exchange;
use Money\Exchange\FixedExchange;
use Money\Exchange\IndirectExchange;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set(Converter::class)
            ->args([
                service(Currencies::class),
                service(Exchange::class),
            ])

        ->set(FixedExchange::class)
            ->args([param('.money_exchange_fixed')])

        ->set(IndirectExchange::class)
            ->args([
                service(Exchange::class),
                service(Currencies::class),
            ])

        ->alias(Exchange::class, FixedExchange::class)
    ;
};
