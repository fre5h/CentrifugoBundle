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

use Fresh\CentrifugoBundle\Logger\CommandHistoryLogger;
use Fresh\CentrifugoBundle\Model\PublishCommand;
use Fresh\CentrifugoBundle\Service\Centrifugo;
use Fresh\CentrifugoBundle\Service\CentrifugoChecker;
use Fresh\CentrifugoBundle\Service\ResponseProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * CentrifugoTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class CentrifugoTest extends TestCase
{
    /** @var HttpClientInterface|MockObject */
    private $httpClient;

    /** @var ResponseInterface|MockObject */
    private $response;

    /** @var ResponseProcessor|MockObject */
    private $responseProcessor;

    /** @var CommandHistoryLogger|MockObject */
    private $commandHistoryLogger;

    /** @var CentrifugoChecker|MockObject */
    private $centrifugoChecker;

    /** @var Profiler|MockObject */
    private $profiler;

    /** @var Centrifugo */
    private $centrifugo;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);
        $this->responseProcessor = $this->createMock(ResponseProcessor::class);
        $this->commandHistoryLogger = $this->createMock(CommandHistoryLogger::class);
        $this->centrifugoChecker = $this->createMock(CentrifugoChecker::class);
        $this->profiler = $this->createMock(Profiler::class);
        $this->centrifugo = new Centrifugo(
            'http://test.com',
            'qwerty',
            'secret',
            $this->httpClient,
            $this->responseProcessor,
            $this->commandHistoryLogger,
            $this->centrifugoChecker,
            $this->profiler
        );
    }

    protected function tearDown(): void
    {
        unset(
            $this->response,
            $this->httpClient,
            $this->responseProcessor,
            $this->commandHistoryLogger,
            $this->centrifugoChecker,
            $this->profiler,
            $this->centrifugo,
        );
    }

    public function testPublish(): void
    {
        $this->centrifugoChecker
            ->expects(self::once())
            ->method('assertValidChannelName')
            ->with('channelA')
        ;

        $this->httpClient
            ->expects(self::once())
            ->method('request')
            ->willReturn($this->response)
        ;

        $this->commandHistoryLogger
            ->expects(self::once())
            ->method('logCommand')
            ->with($this->isInstanceOf(PublishCommand::class))
        ;

        $this->responseProcessor
            ->expects(self::once())
            ->method('processResponse')
            ->with($this->isInstanceOf(PublishCommand::class), $this->response)
            ->willReturn(null)
        ;

        $this->centrifugo->publish(['foo' => 'bar'], 'channelA');
    }
}
