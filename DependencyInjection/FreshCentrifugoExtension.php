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

use Fresh\CentrifugoBundle\Service\ChannelAuthenticator\ChannelAuthenticatorInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * FreshCentrifugoExtension.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class FreshCentrifugoExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.php');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('centrifugo.channel_max_length', (int) $config['channel_max_length']);
        $container->setParameter('centrifugo.jwt.ttl', $config['jwt']['ttl']);
        $container->setParameter('centrifugo.fake_mode', $config['fake_mode']);
        $container->setParameter('centrifugo.api_key', $config['api_key']);
        $container->setParameter('centrifugo.api_endpoint', $config['api_endpoint']);
        $container->setParameter('centrifugo.secret', $config['secret']);
        $container->registerForAutoconfiguration(ChannelAuthenticatorInterface::class)->addTag('centrifugo.channel_authenticator');
    }
}
