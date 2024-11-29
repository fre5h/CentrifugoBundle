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

namespace Fresh\CentrifugoBundle\Tests;

use Fresh\CentrifugoBundle\DependencyInjection\Compiler\RegisterCentrifugoPass;
use Fresh\CentrifugoBundle\FreshCentrifugoBundle;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * FreshCentrifugoBundleTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class FreshCentrifugoBundleTest extends TestCase
{
    #[Test]
    public function build(): void
    {
        $containerBuilder = $this->createMock(ContainerBuilder::class);
        $containerBuilder
            ->expects($this->once())
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(RegisterCentrifugoPass::class))
        ;

        $bundle = new FreshCentrifugoBundle();
        $bundle->build($containerBuilder);
    }
}
