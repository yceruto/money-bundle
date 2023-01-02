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
            ->fixXmlConfig('formatter')
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

                ->arrayNode('formatters')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('intl')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('number_locale')->defaultValue('en_US')->end()
                                ->integerNode('number_style')->defaultValue(2)->end()
                                ->scalarNode('number_pattern')->defaultNull()->end()
                            ->end()
                        ->end()
                        ->arrayNode('bitcoin')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->integerNode('fraction_digits')->defaultValue(8)->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('exchanges')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('fixed')
                            ->variablePrototype()
                                ->defaultValue([])
                                ->validate()
                                    ->ifTrue(static function ($array): bool {
                                        if (!is_array($array)) {
                                            return true;
                                        }

                                        foreach ($array as $k => $v) {
                                            if (!is_string($k)) {
                                                return true;
                                            }

                                            if (!is_string($v)) {
                                                return true;
                                            }
                                        }

                                        return false;
                                    })
                                    ->thenInvalid('Expected an array of string.')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
