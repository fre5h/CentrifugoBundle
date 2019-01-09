<?php
/*
 * This file is part of the FreshCentrifugoBundle.
 *
 * (c) Artem Henvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Fresh\CentrifugoBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('fresh_centrifugo');

        /** @var ArrayNodeDefinition $root */
        $root = $treeBuilder->getRootNode();

        $root
            ->children()
                ->scalarNode('channel_max_length')
                    ->defaultValue(255)
                    ->info('Sets maximum length of channel name.')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
