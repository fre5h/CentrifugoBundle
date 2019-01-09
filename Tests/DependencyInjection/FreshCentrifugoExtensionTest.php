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
        self::assertArrayNotHasKey(Centrifugo::class, $this->container->getDefinitions());

        $this->expectException(ServiceNotFoundException::class);

        $this->container->get(Centrifugo::class);
        $this->container->hasParameter('fresh_centrifugo.channel_max_length');
    }
}
