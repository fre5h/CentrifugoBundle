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

namespace Fresh\CentrifugoBundle\Tests\Service\Jwt;

use Fresh\CentrifugoBundle\Service\ChannelAuthenticator\ChannelAuthenticatorInterface;
use Fresh\CentrifugoBundle\Service\ChannelAuthenticator\PrivateChannelAuthenticator;
use Fresh\CentrifugoBundle\Service\Credentials\CredentialsGenerator;
use Fresh\CentrifugoBundle\Tests\ConsecutiveParamsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * PrivateChannelAuthenticatorTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class PrivateChannelAuthenticatorTest extends TestCase
{
    use ConsecutiveParamsTrait;

    /** @var Request|MockObject */
    private Request|MockObject $request;

    /** @var ChannelAuthenticatorInterface|MockObject */
    private ChannelAuthenticatorInterface|MockObject $customerChannelAuthenticator;

    /** @var CredentialsGenerator|MockObject */
    private CredentialsGenerator|MockObject $credentialsGenerator;

    private PrivateChannelAuthenticator $privateChannelAuthenticator;

    protected function setUp(): void
    {
        $this->request = $this->createMock(Request::class);
        $this->credentialsGenerator = $this->createMock(CredentialsGenerator::class);
        $this->customerChannelAuthenticator = $this->createMock(ChannelAuthenticatorInterface::class);
        $this->privateChannelAuthenticator = new PrivateChannelAuthenticator(
            $this->credentialsGenerator,
            [$this->customerChannelAuthenticator]
        );
    }

    protected function tearDown(): void
    {
        unset(
            $this->request,
            $this->credentialsGenerator,
            $this->customerChannelAuthenticator,
            $this->privateChannelAuthenticator,
        );
    }

    public function testInvalidJsonRequest(): void
    {
        $this->request
            ->expects(self::once())
            ->method('getContent')
            ->willReturn('bla bla')
        ;

        $this->customerChannelAuthenticator
            ->expects(self::never())
            ->method('supports')
        ;

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Invalid JSON.');

        self::assertEquals(['channels' => []], $this->privateChannelAuthenticator->authChannelsForClientFromRequest($this->request));
    }

    /**
     * @param string $content
     *
     * @dataProvider dataProviderForTestInvalidClientInRequest
     */
    public function testInvalidClientInRequest(string $content): void
    {
        $this->request
            ->expects(self::once())
            ->method('getContent')
            ->willReturn($content)
        ;

        $this->customerChannelAuthenticator
            ->expects(self::never())
            ->method('supports')
        ;

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Client must be set in request.');

        self::assertEquals(['channels' => []], $this->privateChannelAuthenticator->authChannelsForClientFromRequest($this->request));
    }

    public static function dataProviderForTestInvalidClientInRequest(): iterable
    {
        yield 'null client' => [
            <<<'JSON'
                {
                    "client": null,
                    "channels": ["avengers"]
                }
            JSON,
        ];

        yield 'integer client' => [
            <<<'JSON'
                {
                    "client": 123,
                    "channels": ["avengers"]
                }
            JSON,
        ];

        yield 'missing channels' => [
            <<<'JSON'
                {
                    "client": 123
                }
            JSON,
        ];

        yield 'missing client' => [
            <<<'JSON'
                {
                    "wrongClient": "spiderman",
                    "channels": ["avengers"]
                }
            JSON,
        ];
    }

    /**
     * @param string $content
     *
     * @dataProvider dataProviderForTestInvalidChannelsInRequest
     */
    public function testInvalidChannelsInRequest(string $content): void
    {
        $this->request
            ->expects(self::once())
            ->method('getContent')
            ->willReturn($content)
        ;

        $this->customerChannelAuthenticator
            ->expects(self::never())
            ->method('supports')
        ;

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Channels must be set in request.');

        self::assertEquals(['channels' => []], $this->privateChannelAuthenticator->authChannelsForClientFromRequest($this->request));
    }

    public static function dataProviderForTestInvalidChannelsInRequest(): iterable
    {
        yield 'empty array' => [
            <<<'JSON'
                {
                    "client": "spiderman",
                    "channels": []
                }
            JSON,
        ];

        yield 'missing channels' => [
            <<<'JSON'
                {
                    "client": "spiderman"
                }
            JSON,
        ];

        yield 'integer channels' => [
            <<<'JSON'
                {
                    "client": "spiderman",
                    "channels": 123
                }
            JSON,
        ];
    }

    public function testNonStringChannelInRequest(): void
    {
        $this->request
            ->expects(self::once())
            ->method('getContent')
            ->willReturn(<<<'JSON'
                {
                    "client": "spiderman",
                    "channels": ["channelA", 123]
                }
            JSON)
        ;

        $this->customerChannelAuthenticator
            ->expects(self::never())
            ->method('supports')
        ;

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Channel must be a string.');

        self::assertEquals(['channels' => []], $this->privateChannelAuthenticator->authChannelsForClientFromRequest($this->request));
    }

    public function testExceptionOnGetContent(): void
    {
        $this->request
            ->expects(self::once())
            ->method('getContent')
            ->willThrowException(new \Exception('test'))
        ;

        $this->customerChannelAuthenticator
            ->expects(self::never())
            ->method('supports')
        ;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('test');

        self::assertEquals(['channels' => []], $this->privateChannelAuthenticator->authChannelsForClientFromRequest($this->request));
    }

    public function testNoChannelAuthenticator(): void
    {
        $this->request
            ->expects(self::once())
            ->method('getContent')
            ->willReturn(<<<'JSON'
                {
                    "client": "spiderman",
                    "channels": ["avengers"]
                }
            JSON)
        ;

        $this->customerChannelAuthenticator
            ->expects(self::once())
            ->method('supports')
            ->with('avengers')
            ->willReturn(false)
        ;

        self::assertEquals(['channels' => []], $this->privateChannelAuthenticator->authChannelsForClientFromRequest($this->request));
    }

    public function testSuccessChannelAuthenticator(): void
    {
        $this->request
            ->expects(self::once())
            ->method('getContent')
            ->willReturn(<<<'JSON'
                {
                    "client": "spiderman",
                    "channels": ["avengers", "marvel"]
                }
            JSON)
        ;

        $this->customerChannelAuthenticator
            ->expects(self::exactly(2))
            ->method('supports')
            ->with(
                ...$this->consecutiveParams(
                    ['avengers'],
                    ['marvel'],
                )
            )
            ->willReturnOnConsecutiveCalls(true, true)
        ;

        $this->customerChannelAuthenticator
            ->expects(self::exactly(2))
            ->method('hasAccessToChannel')
            ->with(
                ...$this->consecutiveParams(
                    ['avengers'],
                    ['marvel'],
                )
            )
            ->willReturnOnConsecutiveCalls(true, true)
        ;

        $this->credentialsGenerator
            ->expects(self::exactly(2))
            ->method('generateJwtTokenForPrivateChannel')
            ->with(
                ...$this->consecutiveParams(
                    ['spiderman', 'avengers'],
                    ['spiderman', 'marvel'],
                )
            )
            ->willReturnOnConsecutiveCalls('test1', 'test2')
        ;

        self::assertEquals(
            [
                'channels' => [
                    [
                        'channel' => 'avengers',
                        'token' => 'test1',
                    ],
                    [
                        'channel' => 'marvel',
                        'token' => 'test2',
                    ],
                ],
            ],
            $this->privateChannelAuthenticator->authChannelsForClientFromRequest($this->request)
        );
    }
}
