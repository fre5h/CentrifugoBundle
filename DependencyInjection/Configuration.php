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

use Fresh\CentrifugoBundle\Token\JwtAlgorithm;
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
                ->arrayNode('jwt')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->enumNode('algorithm')
                            ->values([JwtAlgorithm::HS256, JwtAlgorithm::RSA])
                            ->defaultValue(JwtAlgorithm::HS256)
                            ->info('JWT algorithm. At moment the only supported JWT algorithms are HMAC and RSA.')
                        ->end()
                        ->integerNode('ttl')
                            ->min(0)
                            ->defaultNull()
                            ->info('TTL for JWT tokens in seconds.')
                        ->end()
                    ->end()
                ->end()
                ->integerNode('channel_max_length')
                    ->defaultValue(255)
                    ->info('Maximum length of channel name.')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
