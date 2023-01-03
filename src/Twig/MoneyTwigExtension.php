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

namespace Yceruto\MoneyBundle\Twig;

use Money\MoneyFormatter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MoneyTwigExtension extends AbstractExtension
{
    public function __construct(
        private readonly MoneyFormatter $formatter,
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('money_format', $this->formatter->format(...))
        ];
    }
}
