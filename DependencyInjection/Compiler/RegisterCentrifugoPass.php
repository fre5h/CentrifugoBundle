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

namespace Fresh\CentrifugoBundle\DependencyInjection\Compiler;

use Fresh\CentrifugoBundle\Logger\CommandHistoryLogger;
use Fresh\CentrifugoBundle\Service\Centrifugo;
use Fresh\CentrifugoBundle\Service\CentrifugoChecker;
use Fresh\CentrifugoBundle\Service\CentrifugoInterface;
use Fresh\CentrifugoBundle\Service\FakeCentrifugo;
use Fresh\CentrifugoBundle\Service\ResponseProcessor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * RegisterCentrifugoPass.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class RegisterCentrifugoPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container): void
    {
        if (true === $container->getParameter('centrifugo.fake_mode')) {
            $definition = new Definition(FakeCentrifugo::class, []);
        } else {
            $definition = new Definition(
                Centrifugo::class,
                [
                    $container->resolveEnvPlaceholders('%env(CENTRIFUGO_API_ENDPOINT)%'),
                    $container->resolveEnvPlaceholders('%env(CENTRIFUGO_API_KEY)%'),
                    $container->findDefinition('http_client'),
                    $container->findDefinition(ResponseProcessor::class),
                    $container->findDefinition(CommandHistoryLogger::class),
                    $container->findDefinition(CentrifugoChecker::class),
                    $container->hasDefinition('profiler') ? $container->getDefinition('profiler') : null,
                ]
            );
        }

        $container->setDefinition(CentrifugoInterface::class, $definition);
    }
}
