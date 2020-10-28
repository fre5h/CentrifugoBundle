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

use Fresh\CentrifugoBundle\Model;
use Fresh\CentrifugoBundle\Service\Centrifugo;
use Fresh\CentrifugoBundle\Service\FakeCentrifugo;
use PHPUnit\Framework\TestCase;

/**
 * FakeCentrifugoTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class FakeCentrifugoTest extends TestCase
{
    /** @var Centrifugo */
    private $centrifugo;

    protected function setUp(): void
    {
        $this->centrifugo = new FakeCentrifugo();
    }

    protected function tearDown(): void
    {
        unset(
            $this->centrifugo,
        );
    }

    public function testAllMethods(): void
    {
        $this->centrifugo->publish(['foo' => 'bar'], 'channelA');
        $this->centrifugo->broadcast(['foo' => 'bar'], ['channelA', 'channelB']);
        $this->centrifugo->unsubscribe('user123', 'channelA');
        $this->centrifugo->disconnect('user123');
        self::assertSame([], $this->centrifugo->presence('channelA'));
        self::assertSame([], $this->centrifugo->presenceStats('channelA'));
        self::assertSame([], $this->centrifugo->history('channelA'));
        $this->centrifugo->historyRemove('channelA');
        self::assertSame([], $this->centrifugo->channels());
        self::assertSame([], $this->centrifugo->info());
        self::assertSame(
            [],
            $this->centrifugo->batchRequest(
                [
                    new Model\PublishCommand([], 'channelA'),
                    new Model\PublishCommand([], 'channelB'),
                ]
            )
        );
    }
}
