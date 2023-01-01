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

namespace Yceruto\MoneyBundle\Formatter;

use Symfony\Component\HttpFoundation\RequestStack;

class IntlNumberFormatterFactory
{
    public function __construct(
        private readonly ?RequestStack $requestStack,
        private readonly string $defaultLocale,
        private readonly int $defaultStyle,
        private readonly ?string $defaultPattern,
    ) {
    }

    public function __invoke(): \NumberFormatter
    {
        return new \NumberFormatter(
            $this->requestStack?->getCurrentRequest()?->getLocale() ?? $this->defaultLocale,
            $this->defaultStyle,
            $this->defaultPattern,
        );
    }
}
