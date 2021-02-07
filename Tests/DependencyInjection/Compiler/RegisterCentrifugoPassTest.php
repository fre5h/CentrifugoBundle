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

namespace Fresh\CentrifugoBundle\Tests\DependencyInjection\Compiler;

use Fresh\CentrifugoBundle\DependencyInjection\Compiler\RegisterCentrifugoPass;
use Fresh\CentrifugoBundle\Service\Centrifugo;
use Fresh\CentrifugoBundle\Service\CentrifugoInterface;
use Fresh\CentrifugoBundle\Service\FakeCentrifugo;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * RegisterCentrifugoPassTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class RegisterCentrifugoPassTest extends TestCase
{
    /** @var ContainerBuilder|MockObject */
    private $containerBuilder;

    /** @var RegisterCentrifugoPass */
    private $registerCentrifugoPass;

    protected function setUp(): void
    {
        $this->containerBuilder = $this->createMock(ContainerBuilder::class);
        $this->registerCentrifugoPass = new RegisterCentrifugoPass();
    }

    protected function tearDown(): void
    {
        unset(
            $this->containerBuilder,
            $this->registerCentrifugoPass,
        );
    }

    public function testProcessFakeCentrifugo(): void
    {
        $this->containerBuilder
            ->expects(self::once())
            ->method('getParameter')
            ->with('centrifugo.fake_mode')
            ->willReturn(true)
        ;

        $this->containerBuilder
            ->expects(self::once())
            ->method('setDefinition')
            ->with(CentrifugoInterface::class, self::callback(static function (Definition $definition) {
                return FakeCentrifugo::class === $definition->getClass();
            }))
        ;

        $this->registerCentrifugoPass->process($this->containerBuilder);
    }

    public function testProcessCentrifugo(): void
    {
        $this->containerBuilder
            ->expects(self::once())
            ->method('getParameter')
            ->withConsecutive(
                ['centrifugo.fake_mode'],
                ['centrifugo.api_endpoint'],
                ['centrifugo.api_key'],
            )
            ->willReturnOnConsecutiveCalls(
                false,
                '%env(CENTRIFUGO_API_ENDPOINT)%',
                '%env(CENTRIFUGO_API_KEY)%'
            )
        ;

        $this->containerBuilder
            ->expects(self::once())
            ->method('setDefinition')
            ->with(CentrifugoInterface::class, self::callback(static function (Definition $definition) {
                return Centrifugo::class === $definition->getClass();
            }))
        ;

        $this->registerCentrifugoPass->process($this->containerBuilder);
    }
}
