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

namespace Fresh\CentrifugoBundle\Tests\Service;

use Fresh\CentrifugoBundle\Exception\InvalidArgumentException;
use Fresh\CentrifugoBundle\Service\CentrifugoChecker;
use PHPUnit\Framework\TestCase;

/**
 * CentrifugoCheckerTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class CentrifugoCheckerTest extends TestCase
{
    /** @var CentrifugoChecker */
    private $centrifugoChecker;

    protected function setUp(): void
    {
        $this->centrifugoChecker = new CentrifugoChecker(10);
    }

    protected function tearDown(): void
    {
        unset($this->centrifugoChecker);
    }

    public function testInvalidChannelName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid channel name. Only ASCII symbols must be used in channel string.');

        $this->centrifugoChecker->assertValidChannelName('Hallöchen');
    }

    public function testInvalidChannelNameLength(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid channel name length. Maximum allowed length is 10.');

        $this->centrifugoChecker->assertValidChannelName('ABCDEFGHIJK');
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testValidChannelName(): void
    {
        $this->centrifugoChecker->assertValidChannelName('1234567890');
    }
}
