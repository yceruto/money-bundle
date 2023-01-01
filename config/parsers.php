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
use Money\MoneyParser;
use Money\Parser\AggregateMoneyParser;
use Money\Parser\BitcoinMoneyParser;
use Money\Parser\DecimalMoneyParser;
use Money\Parser\IntlLocalizedDecimalParser;
use Money\Parser\IntlMoneyParser;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set(AggregateMoneyParser::class)
            ->args([abstract_arg('parsers')])

        ->set(IntlMoneyParser::class)
            ->args([
                service('money.intl.number_formatter'),
                service(Currencies::class),
            ])
            ->tag('money.parser')

        ->set(IntlLocalizedDecimalParser::class)
            ->args([
                service('money.intl.number_formatter'),
                service(Currencies::class),
            ])
            ->tag('money.parser')

        ->set(DecimalMoneyParser::class)
            ->args([service(Currencies::class)])
            ->tag('money.parser')

        ->set(BitcoinMoneyParser::class)
            ->args([param('.money_formatter_fraction_digits')])
            ->tag('money.parser')

        ->alias(MoneyParser::class, AggregateMoneyParser::class)
    ;
};
