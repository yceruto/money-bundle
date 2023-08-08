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

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\DoctrineExtension;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Money\Converter;
use Money\Currency;
use Money\Currencies\AggregateCurrencies;
use Money\Formatter\AggregateMoneyFormatter;
use Money\Money;
use Money\Parser\AggregateMoneyParser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Yceruto\MoneyBundle\DependencyInjection\Compiler\CurrenciesPass;
use Yceruto\MoneyBundle\DependencyInjection\Compiler\FormattersPass;
use Yceruto\MoneyBundle\DependencyInjection\Compiler\ParsersPass;
use Yceruto\MoneyBundle\DependencyInjection\MoneyExtension;
use Yceruto\MoneyBundle\Tests\DependencyInjection\Fixtures\CustomCurrencies;
use Yceruto\MoneyBundle\Tests\DependencyInjection\Fixtures\CustomMoneyFormatter;
use Yceruto\MoneyBundle\Tests\DependencyInjection\Fixtures\CustomMoneyParser;

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
        $container = $this->createContainer([AggregateCurrencies::class], $configs, static function (ContainerBuilder $container) {
            $container->register(CustomCurrencies::class)
                ->addTag('money.currencies');
        });

        self::assertTrue($container->hasParameter('money_currencies'));
        self::assertSame(['FOO' => 3], $container->getParameter('money_currencies'));

        $currencies = $container->get(AggregateCurrencies::class);

        // test currency list
        $currency = new Currency('FOO');
        self::assertTrue($currencies->contains($currency));
        self::assertSame(3, $currencies->subunitFor($currency));

        // test custom currencies
        $currency = new Currency('ZZZ');
        self::assertTrue($currencies->contains($currency));
        self::assertSame(0, $currencies->subunitFor($currency));

        // test ISO currencies
        $currency = new Currency('EUR');
        self::assertTrue($currencies->contains($currency));
        self::assertSame(2, $currencies->subunitFor($currency));
    }

    public function testFormatterServices(): void
    {
        $container = $this->createContainer([AggregateMoneyFormatter::class], callback: static function (ContainerBuilder $container) {
            $container->register(CustomMoneyFormatter::class)
                ->addTag('money.formatter', ['code' => 'ZZZ']);
        });

        $formatter = $container->get(AggregateMoneyFormatter::class);

        self::assertSame('€10.00', $formatter->format(Money::EUR('1000')));
        self::assertSame('Ƀ0.00000001', $formatter->format(Money::XBT('1')));
        self::assertSame('ZZZ 10', $formatter->format(new Money('10', new Currency('ZZZ'))));
    }

    public function testParsersServices(): void
    {
        $container = $this->createContainer([AggregateMoneyParser::class], callback: static function (ContainerBuilder $container) {
            $container->register(CustomMoneyParser::class)
                ->addTag('money.parser');
        });

        $parser = $container->get(AggregateMoneyParser::class);

        self::assertEquals(Money::EUR('1000'), $parser->parse('€10.00'));
        self::assertEquals(Money::EUR('1000'), $parser->parse('10.00', new Currency('EUR')));
        self::assertEquals(Money::XBT('1'), $parser->parse('Ƀ0.00000001'));
        self::assertEquals(new Money('10', new Currency('ZZZ')), $parser->parse('ZZZ 10'));
    }

    public function testExchangesServices(): void
    {
        $configs = [
            [
                'exchanges' => [
                    'fixed' => [
                        'EUR' => [
                            'USD' => '1.06',
                        ],
                    ],
                ],
            ],
        ];
        $container = $this->createContainer([
            Converter::class,
            Converter::class.' $reversedConverter',
        ], $configs);

        $converter = $container->get(Converter::class);
        $converted = $converter->convert(Money::EUR('200'), new Currency('USD'));
        self::assertEquals(Money::USD('212'), $converted);

        /** @var Converter $reversedConverter */
        $reversedConverter = $container->get(Converter::class.' $reversedConverter');
        $converted = $reversedConverter->convert(Money::USD('212'), new Currency('EUR'));
        self::assertEquals(Money::EUR('200'), $converted);
    }

    public function testMoneyDoctrineMapping(): void
    {
        $container = $this->createContainer([], [[]], static function (ContainerBuilder $container) {
            $container->registerExtension(new DoctrineExtension());
            $container->loadFromExtension('doctrine', [
                'dbal' => [],
            ]);
        });

        /** @var Registry $doctrine */
        $doctrine = $container->get('doctrine');
        /** @var EntityManager $manager */
        $manager = $doctrine->getManager();
        /** @var ClassMetadata $metadata */
        $metadata = $manager->getClassMetadata(Money::class);

        self::assertTrue($metadata->isEmbeddedClass);
        self::assertSame(Money::class, $metadata->getName());
        self::assertSame(['amount', 'currency.code'], $metadata->getFieldNames());
        self::assertSame('integer', $metadata->getTypeOfField('amount'));
        self::assertSame('string', $metadata->getTypeOfField('currency.code'));
        self::assertCount(1, $metadata->embeddedClasses);
        self::assertSame(Currency::class, $metadata->embeddedClasses['currency']['class']);
    }

    /**
     * @param array<class-string|string>                $publicServices
     * @param array<array-key, array<array-key, mixed>> $configs
     */
    private function createContainer(array $publicServices = [], array $configs = [[]], \Closure $callback = null): ContainerInterface
    {
        $parameters = [
            'kernel.debug' => true,
            'kernel.bundles' => [],
            'kernel.cache_dir' => sys_get_temp_dir(),
        ];
        $container = (new ContainerBuilder(new ParameterBag($parameters)))
            ->addCompilerPass(new CurrenciesPass())
            ->addCompilerPass(new FormattersPass())
            ->addCompilerPass(new ParsersPass())
        ;

        (new MoneyExtension())->load($configs, $container);

        if (null !== $callback) {
            $callback($container);
        }

        foreach ($publicServices as $serviceId) {
            try {
                $container->getDefinition($serviceId)
                    ->setPublic(true);
            } catch (ServiceNotFoundException) {
                $container->getAlias($serviceId)
                    ->setPublic(true);
            }
        }

        $container->compile();

        return $container;
    }
}
