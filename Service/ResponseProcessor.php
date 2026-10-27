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

namespace Fresh\CentrifugoBundle\Service;

use Fresh\CentrifugoBundle\Exception\CentrifugoErrorException;
use Fresh\CentrifugoBundle\Exception\CentrifugoException;
use Fresh\CentrifugoBundle\Exception\LogicException;
use Fresh\CentrifugoBundle\Logger\CommandHistoryLogger;
use Fresh\CentrifugoBundle\Model\BatchRequest;
use Fresh\CentrifugoBundle\Model\CommandInterface;
use Fresh\CentrifugoBundle\Model\ResultableCommandInterface;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * ResponseProcessor.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class ResponseProcessor
{
    private bool $profilerEnabled;
    /**
     * @var array{command?: CommandInterface, response?: ResponseInterface, message?: string, code?: int}
     */
    private array $centrifugoError = [];

    /**
     * @param CentrifugoChecker    $centrifugoChecker
     * @param CommandHistoryLogger $commandHistoryLogger
     * @param Profiler|null        $profiler
     */
    public function __construct(private readonly CentrifugoChecker $centrifugoChecker, private readonly CommandHistoryLogger $commandHistoryLogger, ?Profiler $profiler)
    {
        $this->profilerEnabled = $profiler instanceof Profiler;
    }

    /**
     * @param CommandInterface  $command
     * @param ResponseInterface $response
     *
     * @return array<array-key, mixed>|null
     *
     * @throws LogicException
     * @throws CentrifugoErrorException
     */
    public function processResponse(CommandInterface $command, ResponseInterface $response): ?array
    {
        $this->centrifugoChecker->assertValidResponseStatusCode($response);
        $this->centrifugoChecker->assertValidResponseHeaders($response);
        $this->centrifugoChecker->assertValidResponseContentType($response);

        $this->resetCentrifugoError();

        $content = $response->getContent();

        try {
            /** @var array<string, array<string, mixed>> $data */
            $data = json_decode($content, true, 512, \JSON_THROW_ON_ERROR);
        } catch (\Exception $parsingException) {
            throw new CentrifugoException(response: $response, message: 'Centrifugo response payload is not a valid JSON', previous: $parsingException);
        }

        if ($command instanceof BatchRequest) {
            /** @var array<int, array<string, mixed>> $replies */
            $replies = $data['replies'];
            $result = [];

            if (\count($replies) !== $command->getNumberOfCommands()) {
                throw new LogicException('Number of commands doesn\'t match number of responses');
            }

            $i = 0;
            foreach ($command->getCommands() as $innerCommand) {
                $result[] = $this->decodeAndProcessResponseResult($innerCommand, $response, $replies[$i]);
                ++$i;
            }
        } else {
            $result = $this->decodeAndProcessResponseResult($command, $response, $data);
        }

        $centrifugoError = $this->centrifugoError;

        if (\array_key_exists('command', $centrifugoError)
            && \array_key_exists('response', $centrifugoError)
            && \array_key_exists('message', $centrifugoError)
            && \array_key_exists('code', $centrifugoError)
        ) {
            $exception = new CentrifugoErrorException(
                command: $centrifugoError['command'],
                response: $centrifugoError['response'],
                message: $centrifugoError['message'],
                code: $centrifugoError['code'],
            );

            throw $exception;
        }

        return $result;
    }

    private function resetCentrifugoError(): void
    {
        $this->centrifugoError = [];
    }

    /**
     * @param CommandInterface     $command
     * @param ResponseInterface    $response
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>|null
     */
    private function decodeAndProcessResponseResult(CommandInterface $command, ResponseInterface $response, array $data): ?array
    {
        $successfulCommand = true;
        $result = null;

        if (isset($data['error'])) {
            /** @var array{message: string, code: int} $error */
            $error = $data['error'];

            if (empty($this->centrifugoError)) {
                $this->centrifugoError = [
                    'command' => $command,
                    'response' => $response,
                    'message' => $error['message'],
                    'code' => $error['code'],
                ];
            }

            $result = $data;
            $successfulCommand = false;
        } elseif ($command instanceof ResultableCommandInterface) {
            $result = $command->processResponse($data);
        }

        if ($this->profilerEnabled) {
            $this->commandHistoryLogger->logCommand($command, $successfulCommand, $result);
        }

        return $result;
    }
}
