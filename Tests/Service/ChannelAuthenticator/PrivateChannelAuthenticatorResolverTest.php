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

namespace Fresh\CentrifugoBundle\Tests\Service\ChannelAuthenticator;

use Fresh\CentrifugoBundle\Service\ChannelAuthenticator\ChannelAuthenticatorInterface;
use Fresh\CentrifugoBundle\Service\ChannelAuthenticator\PrivateChannelAuthenticatorResolver;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * PrivateChannelAuthenticatorResolverTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class PrivateChannelAuthenticatorResolverTest extends TestCase
{
    private Request $request;

    protected function setUp(): void
    {
        $this->request = new Request();
    }

    protected function tearDown(): void
    {
        unset($this->request);
    }

    #[Test]
    public function returnsNullWhenNoAuthenticators(): void
    {
        $resolver = new PrivateChannelAuthenticatorResolver([]);

        $this->assertNull($resolver->findAppropriateChannelAuthenticator('channelA', $this->request));
    }

    #[Test]
    public function returnsNullWhenNoAuthenticatorSupportsChannel(): void
    {
        $authenticatorA = $this->createAuthenticator(false, 'channelA');
        $authenticatorB = $this->createAuthenticator(false, 'channelA');

        $resolver = new PrivateChannelAuthenticatorResolver([$authenticatorA, $authenticatorB]);

        $this->assertNull($resolver->findAppropriateChannelAuthenticator('channelA', $this->request));
    }

    #[Test]
    public function returnsSingleSupportingAuthenticator(): void
    {
        $authenticator = $this->createAuthenticator(true, 'channelA');

        $resolver = new PrivateChannelAuthenticatorResolver([$authenticator]);

        $this->assertSame($authenticator, $resolver->findAppropriateChannelAuthenticator('channelA', $this->request));
    }

    #[Test]
    public function skipsNonSupportingAuthenticators(): void
    {
        $authenticatorA = $this->createAuthenticator(false, 'channelA');
        $authenticatorB = $this->createAuthenticator(true, 'channelA');

        $resolver = new PrivateChannelAuthenticatorResolver([$authenticatorA, $authenticatorB]);

        $this->assertSame($authenticatorB, $resolver->findAppropriateChannelAuthenticator('channelA', $this->request));
    }

    #[Test]
    public function returnsFirstSupportingAuthenticatorAndStopsSearching(): void
    {
        $authenticatorA = $this->createAuthenticator(true, 'channelA');
        $authenticatorB = $this->createMock(ChannelAuthenticatorInterface::class);
        $authenticatorB
            ->expects($this->never())
            ->method('supports')
        ;

        $resolver = new PrivateChannelAuthenticatorResolver([$authenticatorA, $authenticatorB]);

        $this->assertSame($authenticatorA, $resolver->findAppropriateChannelAuthenticator('channelA', $this->request));
    }

    #[Test]
    public function acceptsAnyIterable(): void
    {
        $authenticator = $this->createAuthenticator(true, 'channelA');

        $resolver = new PrivateChannelAuthenticatorResolver((static function () use ($authenticator): \Generator {
            yield $authenticator;
        })());

        $this->assertSame($authenticator, $resolver->findAppropriateChannelAuthenticator('channelA', $this->request));
    }

    /**
     * @param bool   $supports
     * @param string $channel
     *
     * @return ChannelAuthenticatorInterface&MockObject
     */
    private function createAuthenticator(bool $supports, string $channel): ChannelAuthenticatorInterface&MockObject
    {
        $authenticator = $this->createMock(ChannelAuthenticatorInterface::class);
        $authenticator
            ->expects($this->once())
            ->method('supports')
            ->with($channel, $this->request)
            ->willReturn($supports)
        ;

        return $authenticator;
    }
}
