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

namespace Yceruto\MoneyBundle\DependencyInjection\Compiler;

use Money\Formatter\AggregateMoneyFormatter;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FormattersPass implements CompilerPassInterface
{
    public const TAG = 'money.formatter';

    use PriorityTaggedServiceTrait;

    public function process(ContainerBuilder $container): void
    {
        $tag = new TaggedIteratorArgument(self::TAG, 'code');
        $container->getDefinition(AggregateMoneyFormatter::class)
            ->setArgument(0, $this->findAndSortTaggedServices($tag, $container));
    }
}
