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
use PHPUnit\Framework\TestCase;

/**
 * StreamPositionTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class StreamPositionTest extends TestCase
{
    public function testConstructor(): void
    {
        $streamPosition = new StreamPosition(5, 'ABCD');
        self::assertSame(5, $streamPosition->getOffset());
        self::assertSame('ABCD', $streamPosition->getEpoch());
    }
}
