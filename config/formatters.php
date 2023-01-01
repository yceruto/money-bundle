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
use Money\Formatter\AggregateMoneyFormatter;
use Money\Formatter\BitcoinMoneyFormatter;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Formatter\IntlLocalizedDecimalFormatter;
use Money\Formatter\IntlMoneyFormatter;
use Money\MoneyFormatter;
use NumberFormatter;
use Yceruto\MoneyBundle\DependencyInjection\Compiler\FormattersPass;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set(AggregateMoneyFormatter::class)
            ->args([abstract_arg('formatters')])

        ->set(DecimalMoneyFormatter::class)
            ->args([service(Currencies::class)])
            ->tag(FormattersPass::TAG, ['code' => '*'])

        ->set(IntlLocalizedDecimalFormatter::class)
            ->args([
                inline_service(NumberFormatter::class)
                    ->args([
                        param('.money_formatter_number_locale'),
                        param('.money_formatter_number_style'),
                        param('.money_formatter_number_pattern'),
                    ]),
                service(Currencies::class),
            ])
            ->tag(FormattersPass::TAG, ['code' => '*'])

        ->set(IntlMoneyFormatter::class)
            ->args([
                inline_service(NumberFormatter::class)
                    ->args([
                        param('.money_formatter_number_locale'),
                        param('.money_formatter_number_style'),
                        param('.money_formatter_number_pattern'),
                    ]),
                service(Currencies::class),
            ])
            ->tag(FormattersPass::TAG, ['code' => '*'])

        ->set(BitcoinMoneyFormatter::class)
            ->args([
                param('.money_formatter_fraction_digits'),
                service(Currencies::class),
            ])
            ->tag(FormattersPass::TAG, ['code' => BitcoinCurrencies::CODE])

        ->alias(MoneyFormatter::class, AggregateMoneyFormatter::class)
    ;
};
