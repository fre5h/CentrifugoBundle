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

use Fresh\CentrifugoBundle\Model\StreamPosition;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * StreamPositionTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class StreamPositionTest extends TestCase
{
    #[Test]
    public function constructor(): void
    {
        $streamPosition = new StreamPosition(5, 'ABCD');
        $this->assertSame(5, $streamPosition->getOffset());
        $this->assertSame('ABCD', $streamPosition->getEpoch());
    }
}
