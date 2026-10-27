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
use Fresh\CentrifugoBundle\Service\ChannelAuthenticator\PrivateChannelAuthenticatorResolver;
use Fresh\CentrifugoBundle\Service\Credentials\CredentialsGenerator;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SEEC\PhpUnit\Helper\ConsecutiveParams;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * PrivateChannelAuthenticatorTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class PrivateChannelAuthenticatorTest extends TestCase
{
    use ConsecutiveParams;

    private Request&MockObject $request;
    private ChannelAuthenticatorInterface&MockObject $customerChannelAuthenticator;
    private CredentialsGenerator&MockObject $credentialsGenerator;
    private PrivateChannelAuthenticator $privateChannelAuthenticator;

    protected function setUp(): void
    {
        $this->request = $this->createMock(Request::class);
        $this->credentialsGenerator = $this->createMock(CredentialsGenerator::class);
        $this->customerChannelAuthenticator = $this->createMock(ChannelAuthenticatorInterface::class);
        $this->privateChannelAuthenticator = new PrivateChannelAuthenticator(
            $this->credentialsGenerator,
            new PrivateChannelAuthenticatorResolver([$this->customerChannelAuthenticator]),
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

    #[Test]
    #[AllowMockObjectsWithoutExpectations]
    public function invalidJsonRequest(): void
    {
        $this->request
            ->expects($this->once())
            ->method('getContent')
            ->willReturn('bla bla')
        ;

        $this->customerChannelAuthenticator
            ->expects($this->never())
            ->method('supports')
        ;

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessageIsOrContains('Invalid JSON.');

        $this->assertEquals(['channels' => []], $this->privateChannelAuthenticator->authChannelsForClientFromRequest($this->request));
    }

    #[Test]
    #[DataProvider('dataProviderForTestInvalidClientInRequest')]
    #[AllowMockObjectsWithoutExpectations]
    public function testInvalidClientInRequest(string $content): void
    {
        $this->request
            ->expects($this->once())
            ->method('getContent')
            ->willReturn($content)
        ;

        $this->customerChannelAuthenticator
            ->expects($this->never())
            ->method('supports')
        ;

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessageIsOrContains('Client must be set in request.');

        $this->assertEquals(['channels' => []], $this->privateChannelAuthenticator->authChannelsForClientFromRequest($this->request));
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

    #[DataProvider('dataProviderForTestInvalidChannelsInRequest')]
    #[AllowMockObjectsWithoutExpectations]
    public function testInvalidChannelsInRequest(string $content): void
    {
        $this->request
            ->expects($this->once())
            ->method('getContent')
            ->willReturn($content)
        ;

        $this->customerChannelAuthenticator
            ->expects($this->never())
            ->method('supports')
        ;

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessageIsOrContains('Channels must be set in request.');

        $this->assertEquals(['channels' => []], $this->privateChannelAuthenticator->authChannelsForClientFromRequest($this->request));
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

    #[Test]
    #[AllowMockObjectsWithoutExpectations]
    public function nonStringChannelInRequest(): void
    {
        $this->request
            ->expects($this->once())
            ->method('getContent')
            ->willReturn(<<<'JSON'
                {
                    "client": "spiderman",
                    "channels": ["channelA", 123]
                }
            JSON)
        ;

        $this->customerChannelAuthenticator
            ->expects($this->never())
            ->method('supports')
        ;

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessageIsOrContains('Channel must be a string.');

        $this->assertEquals(['channels' => []], $this->privateChannelAuthenticator->authChannelsForClientFromRequest($this->request));
    }

    #[Test]
    #[AllowMockObjectsWithoutExpectations]
    public function exceptionOnGetContent(): void
    {
        $this->request
            ->expects($this->once())
            ->method('getContent')
            ->willThrowException(new \Exception('test'))
        ;

        $this->customerChannelAuthenticator
            ->expects($this->never())
            ->method('supports')
        ;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageIsOrContains('test');

        $this->assertEquals(['channels' => []], $this->privateChannelAuthenticator->authChannelsForClientFromRequest($this->request));
    }

    #[Test]
    #[AllowMockObjectsWithoutExpectations]
    public function noChannelAuthenticator(): void
    {
        $this->request
            ->expects($this->once())
            ->method('getContent')
            ->willReturn(<<<'JSON'
                {
                    "client": "spiderman",
                    "channels": ["avengers"]
                }
            JSON)
        ;

        $this->customerChannelAuthenticator
            ->expects($this->once())
            ->method('supports')
            ->with('avengers')
            ->willReturn(false)
        ;

        $this->assertEquals(['channels' => []], $this->privateChannelAuthenticator->authChannelsForClientFromRequest($this->request));
    }

    #[Test]
    public function successChannelAuthenticator(): void
    {
        $this->request
            ->expects($this->once())
            ->method('getContent')
            ->willReturn(<<<'JSON'
                {
                    "client": "spiderman",
                    "channels": ["avengers", "marvel"]
                }
            JSON)
        ;

        $this->customerChannelAuthenticator
            ->expects($this->exactly(2))
            ->method('supports')
            ->with(
                ...self::withConsecutive(
                    ['avengers'],
                    ['marvel'],
                )
            )
            ->willReturnOnConsecutiveCalls(true, true)
        ;

        $this->customerChannelAuthenticator
            ->expects($this->exactly(2))
            ->method('hasAccessToChannel')
            ->with(
                ...self::withConsecutive(
                    ['avengers'],
                    ['marvel'],
                )
            )
            ->willReturnOnConsecutiveCalls(true, true)
        ;

        $this->credentialsGenerator
            ->expects($this->exactly(2))
            ->method('generateJwtTokenForPrivateChannel')
            ->with(
                ...self::withConsecutive(
                    ['spiderman', 'avengers'],
                    ['spiderman', 'marvel'],
                )
            )
            ->willReturnOnConsecutiveCalls('test1', 'test2')
        ;

        $this->assertEquals(
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

    #[Test]
    public function accessDeniedForChannel(): void
    {
        $this->request
            ->expects($this->once())
            ->method('getContent')
            ->willReturn(<<<'JSON'
                {
                    "client": "spiderman",
                    "channels": ["avengers"]
                }
            JSON)
        ;

        $this->customerChannelAuthenticator
            ->expects($this->once())
            ->method('supports')
            ->with('avengers')
            ->willReturn(true)
        ;

        $this->customerChannelAuthenticator
            ->expects($this->once())
            ->method('hasAccessToChannel')
            ->with('avengers')
            ->willReturn(false)
        ;

        $this->credentialsGenerator
            ->expects($this->never())
            ->method('generateJwtTokenForPrivateChannel')
        ;

        $this->assertEquals(['channels' => []], $this->privateChannelAuthenticator->authChannelsForClientFromRequest($this->request));
    }

    #[Test]
    public function onlyChannelsWithAccessGetTokens(): void
    {
        $this->request
            ->expects($this->once())
            ->method('getContent')
            ->willReturn(<<<'JSON'
                {
                    "client": "spiderman",
                    "channels": ["avengers", "marvel"]
                }
            JSON)
        ;

        $this->customerChannelAuthenticator
            ->expects($this->exactly(2))
            ->method('supports')
            ->with(
                ...self::withConsecutive(
                    ['avengers'],
                    ['marvel'],
                )
            )
            ->willReturn(true)
        ;

        $this->customerChannelAuthenticator
            ->expects($this->exactly(2))
            ->method('hasAccessToChannel')
            ->with(
                ...self::withConsecutive(
                    ['avengers'],
                    ['marvel'],
                )
            )
            ->willReturnOnConsecutiveCalls(false, true)
        ;

        $this->credentialsGenerator
            ->expects($this->once())
            ->method('generateJwtTokenForPrivateChannel')
            ->with('spiderman', 'marvel')
            ->willReturn('test-marvel')
        ;

        $this->assertEquals(
            [
                'channels' => [
                    [
                        'channel' => 'marvel',
                        'token' => 'test-marvel',
                    ],
                ],
            ],
            $this->privateChannelAuthenticator->authChannelsForClientFromRequest($this->request)
        );
    }

    #[Test]
    #[AllowMockObjectsWithoutExpectations]
    public function noAuthenticatorRegistered(): void
    {
        $privateChannelAuthenticator = new PrivateChannelAuthenticator(
            $this->credentialsGenerator,
            new PrivateChannelAuthenticatorResolver([]),
        );

        $this->request
            ->expects($this->once())
            ->method('getContent')
            ->willReturn(<<<'JSON'
                {
                    "client": "spiderman",
                    "channels": ["avengers"]
                }
            JSON)
        ;

        $this->credentialsGenerator
            ->expects($this->never())
            ->method('generateJwtTokenForPrivateChannel')
        ;

        $this->assertEquals(['channels' => []], $privateChannelAuthenticator->authChannelsForClientFromRequest($this->request));
    }
}
