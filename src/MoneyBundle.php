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

namespace Yceruto\MoneyBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MoneyBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
