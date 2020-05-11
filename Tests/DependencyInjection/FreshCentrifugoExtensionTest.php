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
use Fresh\CentrifugoBundle\Service\Centrifugo;
use Fresh\CentrifugoBundle\Service\ResponseProcessor;
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
    /** @var FreshCentrifugoExtension */
    private $extension;

    /** @var ContainerBuilder */
    private $container;

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

    public function testLoadExtension(): void
    {
        $this->container->loadFromExtension($this->extension->getAlias());
        $this->container->compile();

        self::assertArrayHasKey(Centrifugo::class, $this->container->getRemovedIds());
        self::assertArrayHasKey(ResponseProcessor::class, $this->container->getRemovedIds());
        self::assertArrayNotHasKey(Centrifugo::class, $this->container->getDefinitions());
        self::assertArrayNotHasKey(ResponseProcessor::class, $this->container->getDefinitions());

        self::assertTrue($this->container->hasParameter('centrifugo.channel_max_length'));
        self::assertSame(255, $this->container->getParameter('centrifugo.channel_max_length'));
        self::assertTrue($this->container->hasParameter('centrifugo.jwt.ttl'));
        self::assertNull($this->container->getParameter('centrifugo.jwt.ttl'));

        $childDefinitions = $this->container->getAutoconfiguredInstanceof();
        foreach ($childDefinitions as $childDefinition) {
           self::assertTrue($childDefinition->hasTag('centrifugo.channel_authenticator'));
        }
    }

    public function testExceptionOnGettingPrivateService(): void
    {
        $this->container->loadFromExtension($this->extension->getAlias());
        $this->container->compile();

        $this->expectException(ServiceNotFoundException::class);
        $this->container->get(Centrifugo::class);
    }
}
