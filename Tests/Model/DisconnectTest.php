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

namespace Fresh\CentrifugoBundle\Tests\Model;

use Fresh\CentrifugoBundle\Model\Disconnect;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * DisconnectTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class DisconnectTest extends TestCase
{
    #[Test]
    public function constructor(): void
    {
        $disconnectObject = new Disconnect(999, 'some reason');
        self::assertSame(999, $disconnectObject->getCode());
        self::assertSame('some reason', $disconnectObject->getReason());
    }
}
