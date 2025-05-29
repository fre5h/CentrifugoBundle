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

namespace Fresh\CentrifugoBundle\Tests\DependencyInjection;

use Fresh\CentrifugoBundle\DependencyInjection\FreshCentrifugoExtension;
use Fresh\CentrifugoBundle\Service\CentrifugoInterface;
use Fresh\CentrifugoBundle\Service\ResponseProcessor;
use Fresh\DateTime\DateTimeHelper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * FreshCentrifugoExtensionTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class FreshCentrifugoExtensionTest extends TestCase
{
    private FreshCentrifugoExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new FreshCentrifugoExtension();
        $this->container = new ContainerBuilder();
        $this->container->registerExtension($this->extension);
    }

    protected function tearDown(): void
    {
        unset(
            $this->extension,
            $this->container,
        );
    }

    #[Test]
    public function loadExtension(): void
    {
        $this->container->loadFromExtension($this->extension->getAlias());
        $this->container->compile();

        $this->assertArrayHasKey(CentrifugoInterface::class, $this->container->getRemovedIds());
        $this->assertArrayHasKey(ResponseProcessor::class, $this->container->getRemovedIds());
        $this->assertArrayHasKey(DateTimeHelper::class, $this->container->getRemovedIds());
        $this->assertArrayNotHasKey(CentrifugoInterface::class, $this->container->getDefinitions());
        $this->assertArrayNotHasKey(ResponseProcessor::class, $this->container->getDefinitions());
        $this->assertArrayNotHasKey(DateTimeHelper::class, $this->container->getDefinitions());

        $this->assertTrue($this->container->hasParameter('centrifugo.channel_max_length'));
        $this->assertSame(255, $this->container->getParameter('centrifugo.channel_max_length'));
        $this->assertTrue($this->container->hasParameter('centrifugo.jwt.ttl'));
        $this->assertNull($this->container->getParameter('centrifugo.jwt.ttl'));
        $this->assertFalse($this->container->getParameter('centrifugo.fake_mode'));
        $this->assertNotEmpty($this->container->getParameter('centrifugo.api_key'));
        $this->assertNotEmpty($this->container->getParameter('centrifugo.api_endpoint'));
        $this->assertNotEmpty($this->container->getParameter('centrifugo.secret'));

        $childDefinitions = $this->container->getAutoconfiguredInstanceof();
        foreach ($childDefinitions as $childDefinition) {
           $this->assertTrue($childDefinition->hasTag('centrifugo.channel_authenticator'));
        }
    }

    #[Test]
    public function exceptionOnGettingPrivateService(): void
    {
        $this->container->loadFromExtension($this->extension->getAlias());
        $this->container->compile();

        $this->expectException(ServiceNotFoundException::class);
        $this->container->get(CentrifugoInterface::class);
    }
}
