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

namespace Yceruto\MoneyBundle\Tests\Model;

use AssertionError;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Throwable;
use Yceruto\MoneyBundle\Model\MoneyDto;

class MoneyDtoTest extends TestCase
{
    public function testFromFactories(): void
    {
        self::assertEquals(new MoneyDto(1, 'CUP'), MoneyDto::fromMoney(Money::CUP(1)));
        self::assertEquals(new MoneyDto(0, 'USD'), MoneyDto::fromCurrency('USD'));
        self::assertEquals(new MoneyDto(2, 'EUR'), MoneyDto::fromAmount(2));
    }

    public function testToMoney(): void
    {
        self::assertEquals(Money::CUP(1), (new MoneyDto(1, 'CUP'))->toMoney());
    }

    public function testInvalidEmptyAmount(): void
    {
        $this->expectException(Throwable::class);
        $this->expectExceptionMessage('Empty number is invalid');

        (new MoneyDto('', 'CUP'))->toMoney();
    }

    public function testInvalidNonNumericAmount(): void
    {
        $this->expectException(Throwable::class);
        $this->expectExceptionMessage('Invalid digit a found');

        (new MoneyDto('abc', 'CUP'))->toMoney();
    }
}
