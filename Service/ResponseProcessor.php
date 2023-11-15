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
    private array $centrifugoError = [];

    /**
     * @param CentrifugoChecker    $centrifugoChecker
     * @param CommandHistoryLogger $commandHistoryLogger
     * @param Profiler|null        $profiler
     */
    public function __construct(private readonly CentrifugoChecker $centrifugoChecker, private readonly CommandHistoryLogger $commandHistoryLogger, readonly ?Profiler $profiler)
    {
        $this->profilerEnabled = $profiler instanceof Profiler;
    }

    /**
     * @param CommandInterface  $command
     * @param ResponseInterface $response
     *
     * @throws LogicException
     * @throws CentrifugoErrorException
     *
     * @return array|null
     */
    public function processResponse(CommandInterface $command, ResponseInterface $response): ?array
    {
        $this->centrifugoChecker->assertValidResponseStatusCode($response);
        $this->centrifugoChecker->assertValidResponseHeaders($response);
        $this->centrifugoChecker->assertValidResponseContentType($response);

        $this->centrifugoError = [];

        $content = $response->getContent();

        try {
            /** @var array<string, array<string, mixed>> $data */
            $data = \json_decode($content, true, 512, \JSON_THROW_ON_ERROR);
        } catch (\Exception) {
            throw new CentrifugoException('Centrifugo response payload is not a valid JSON');
        }

        if ($command instanceof BatchRequest) {
            $replies = $data['replies'];
            $result = [];

            if (\count($replies) !== $command->getNumberOfCommands()) {
                throw new LogicException('Number of commands doesn\'t match number of responses');
            }

            $i = 0;
            foreach ($command->getCommands() as $innerCommand) {
                $result[] = $this->decodeAndProcessResponseResult($innerCommand, $replies[$i]);
                ++$i;
            }
        } else {
            $result = $this->decodeAndProcessResponseResult($command, $data);
        }

        if (\array_key_exists('command', $this->centrifugoError) && \array_key_exists('message', $this->centrifugoError)
            && \array_key_exists('code', $this->centrifugoError)
        ) {
            $exception = new CentrifugoErrorException(
                command: $this->centrifugoError['command'],
                message: $this->centrifugoError['message'],
                code: $this->centrifugoError['code'],
            );

            throw $exception;
        }

        return $result;
    }

    /**
     * @param CommandInterface $command
     * @param array            $data
     *
     * @throws CentrifugoException
     *
     * @return array|null
     */
    private function decodeAndProcessResponseResult(CommandInterface $command, array $data): ?array
    {
        $successfulCommand = true;
        $result = null;

        if (isset($data['error'])) {
            if (empty($this->centrifugoError)) {
                $this->centrifugoError = [
                    'command' => $command,
                    'message' => $data['error']['message'],
                    'code' => $data['error']['code'],
                ];
            }

            $result = $data;
            $successfulCommand = false;
        } elseif ($command instanceof ResultableCommandInterface) {
            $result = $data[$command->getMethod()->value];
        }

        if ($this->profilerEnabled) {
            $this->commandHistoryLogger->logCommand($command, $successfulCommand, $result);
        }

        return $result;
    }
}
