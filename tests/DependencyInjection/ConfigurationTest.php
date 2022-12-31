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

    public function testDefaultConfig(): void
    {
        $configs = [[]];
        $config = (new Processor())->processConfiguration(new Configuration(), $configs);

        self::assertSame(['currencies' => []], $config);
    }

    public function testCustomConfig(): void
    {
        $configs = [
            [
                'currencies' => [
                    'EUR' => 3,
                    'USD' => 4,
                ],
            ],
        ];
        $config = (new Processor())->processConfiguration(new Configuration(), $configs);

        $expected = [
            'currencies' => [
                'EUR' => 3,
                'USD' => 4,
            ],
        ];
        self::assertSame($expected, $config);
    }
}
