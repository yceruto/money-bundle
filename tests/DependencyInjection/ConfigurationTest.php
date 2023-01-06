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

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;
use Yceruto\MoneyBundle\DependencyInjection\Configuration;

class ConfigurationTest extends TestCase
{
    public function testInvalidConfig(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Invalid configuration for path "money.currencies.EUR": Invalid units value "2", it must be an integer.');

        $configs = [
            [
                'currencies' => [
                    'EUR' => '2',
                ],
            ],
        ];
        (new Processor())->processConfiguration(new Configuration(), $configs);
    }

    /**
     * @dataProvider configProvider
     */
    public function testConfig(array $input, array $output): void
    {
        $config = (new Processor())->processConfiguration(new Configuration(), $input);

        self::assertSame($output, $config);
    }

    /**
     * @return iterable<string, array{input: array<array-key, array<array-key, mixed>>, output: array<string, mixed>}>
     */
    public function configProvider(): iterable
    {
       yield 'empty config' => [
           'input' => [[]],
           'output' => [
               'form' => ['enabled' => true],
               'twig' => ['enabled' => true],
               'currencies' => [],
               'formatters' => [
                   'intl' => [
                       'number_locale' => 'en_US',
                       'number_style' => 2,
                       'number_pattern' => null,
                   ],
                   'bitcoin' => [
                       'fraction_digits' => 8,
                   ],
               ],
               'exchanges' => [
                   'fixed' => [],
               ],
           ],
       ];

       yield 'custom config' => [
           'input' => [
               [
                   'currencies' => [
                       'EUR' => 3,
                       'USD' => 4,
                   ],
                   'formatters' => [
                       'bitcoin' => [
                           'fraction_digits' => 7,
                       ],
                   ],
                   'exchanges' => [
                       'fixed' => [
                           'EUR' => [
                               'USD' => '1.08',
                           ]
                       ],
                   ],
               ],
           ],
           'output' => [
               'currencies' => [
                   'EUR' => 3,
                   'USD' => 4,
               ],
               'formatters' => [
                   'bitcoin' => [
                       'fraction_digits' => 7,
                   ],
                   'intl' => [
                       'number_locale' => 'en_US',
                       'number_style' => 2,
                       'number_pattern' => null,
                   ],
               ],
               'exchanges' => [
                   'fixed' => [
                       'EUR' => [
                           'USD' => '1.08',
                       ]
                   ],
               ],
               'form' => ['enabled' => true],
               'twig' => ['enabled' => true],
           ],
       ];

       yield 'override config' => [
           'input' => [
               [
                   'currencies' => [
                       'EUR' => 2,
                   ],
                   'exchanges' => [
                       'fixed' => [
                           'EUR' => [
                               'USD' => '1.08',
                           ]
                       ],
                   ],
               ],
               [
                   'currencies' => [
                       'EUR' => 3,
                   ],
                   'exchanges' => [
                       'fixed' => [
                           'EUR' => [
                               'USD' => '1.10',
                           ]
                       ],
                   ],
               ],
           ],
           'output' => [
               'currencies' => [
                   'EUR' => 3,
               ],
               'exchanges' => [
                   'fixed' => [
                       'EUR' => [
                           'USD' => '1.10',
                       ]
                   ],
               ],
               'form' => ['enabled' => true],
               'twig' => ['enabled' => true],
               'formatters' => [
                   'intl' => [
                       'number_locale' => 'en_US',
                       'number_style' => 2,
                       'number_pattern' => null,
                   ],
                   'bitcoin' => [
                       'fraction_digits' => 8,
                   ],
               ],
           ],
       ];
    }
}
