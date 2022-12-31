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

namespace Yceruto\MoneyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('money');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->fixXmlConfig('currency')
            ->children()
                ->arrayNode('currencies')
                    ->example(['EUR' => 2, 'USD' => 2])
                    ->scalarPrototype()
                        ->validate()
                            ->ifTrue(static fn ($v): bool => !\is_int($v))
                            ->thenInvalid('Invalid units value %s, it must be an integer.')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
