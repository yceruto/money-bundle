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

namespace Yceruto\MoneyBundle\Tests\DependencyInjection;

use Money\Currencies;
use Money\Currency;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Yceruto\MoneyBundle\Currencies\AggregateCurrencies;
use Yceruto\MoneyBundle\DependencyInjection\MoneyExtension;

class MoneyExtensionTest extends TestCase
{
    public function testCurrencyServices(): void
    {
        $configs = [
            [
                'currencies' => [
                    'FOO' => 3,
                ],
            ],
        ];
        $container = new ContainerBuilder(new ParameterBag());

        $extension = new MoneyExtension();
        $extension->load($configs, $container);

        self::assertTrue($container->hasParameter('.money_currencies'));
        self::assertSame(['FOO' => 3], $container->getParameter('.money_currencies'));

        // test definition
        self::assertTrue($container->hasAlias(Currencies::class));
        self::assertTrue($container->hasDefinition(AggregateCurrencies::class));

        $container->getDefinition(AggregateCurrencies::class)
            ->setPublic(true);

        $container->compile();
        $currencies = $container->get(AggregateCurrencies::class);

        // test custom currency list
        $currency = new Currency('FOO');
        self::assertTrue($currencies->contains($currency));
        self::assertSame(3, $currencies->subunitFor($currency));

        // test ISO currencies
        $currency = new Currency('EUR');
        self::assertTrue($currencies->contains($currency));
        self::assertSame(2, $currencies->subunitFor($currency));
    }
}
