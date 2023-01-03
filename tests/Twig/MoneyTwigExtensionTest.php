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

namespace Yceruto\MoneyBundle\Tests\Twig;

use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Yceruto\MoneyBundle\Twig\MoneyTwigExtension;

class MoneyTwigExtensionTest extends TestCase
{
    public function testMoneyFormatFilter(): void
    {
        $extension = new MoneyTwigExtension(new IntlMoneyFormatter(
            new \NumberFormatter('en_US', \NumberFormatter::CURRENCY),
            new ISOCurrencies(),
        ));

        $twig = new Environment(new ArrayLoader([
            'money.twig' => '{{ money|money_format }}',
        ]));
        $twig->addExtension($extension);

        self::assertSame('€100.00', $twig->render('money.twig', [
            'money' => Money::EUR(10000),
        ]));
    }
}
