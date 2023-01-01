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

use Money\Currency;
use Money\Currencies\AggregateCurrencies;
use Money\Formatter\AggregateMoneyFormatter;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Yceruto\MoneyBundle\DependencyInjection\Compiler\CurrenciesPass;
use Yceruto\MoneyBundle\DependencyInjection\Compiler\FormattersPass;
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
        $container = $this->createContainer([AggregateCurrencies::class], $configs);

        self::assertTrue($container->hasParameter('.money_currencies'));
        self::assertSame(['FOO' => 3], $container->getParameter('.money_currencies'));

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

    public function testFormatterServices(): void
    {
        $formatters = $this->createContainer([AggregateMoneyFormatter::class])
            ->get(AggregateMoneyFormatter::class);

        self::assertSame('€10.00', $formatters->format(Money::EUR('1000')));
        self::assertSame('Ƀ0.00000001', $formatters->format(Money::XBT('1')));
    }

    private function createContainer(array $publicServices = [], array $configs = [[]]): ContainerInterface
    {
        $container = (new ContainerBuilder(new ParameterBag()))
            ->addCompilerPass(new CurrenciesPass())
            ->addCompilerPass(new FormattersPass())
        ;

        (new MoneyExtension())->load($configs, $container);

        foreach ($publicServices as $serviceId) {
            $container->getDefinition($serviceId)
                ->setPublic(true);
        }

        $container->compile();

        return $container;
    }
}
