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
    private CentrifugoChecker $centrifugoChecker;
    private bool $profilerEnabled;
    private CommandHistoryLogger $commandHistoryLogger;
    private array $centrifugoError = [];

    /**
     * @param CentrifugoChecker    $centrifugoChecker
     * @param CommandHistoryLogger $commandHistoryLogger
     * @param Profiler|null        $profiler
     */
    public function __construct(CentrifugoChecker $centrifugoChecker, CommandHistoryLogger $commandHistoryLogger, ?Profiler $profiler)
    {
        $this->centrifugoChecker = $centrifugoChecker;
        $this->profilerEnabled = $profiler instanceof Profiler;
        $this->commandHistoryLogger = $commandHistoryLogger;
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

        $content = $response->getContent();

        if ($command instanceof BatchRequest) {
            $contents = \explode("\n", \rtrim($content, "\n"));
            $result = [];

            if (\count($contents) !== $command->getNumberOfCommands()) {
                throw new LogicException('Number of commands doesn\'t match number of responses');
            }

            $i = 0;
            foreach ($command->getCommands() as $innerCommand) {
                $result[] = $this->decodeAndProcessResponseResult($innerCommand, $contents[$i]);
                ++$i;
            }
        } else {
            $result = $this->decodeAndProcessResponseResult($command, $content);
        }

        if (isset($this->centrifugoError['message'], $this->centrifugoError['code'])) {
            throw new CentrifugoErrorException($this->centrifugoError['message'], $this->centrifugoError['code']);
        }

        return $result;
    }

    /**
     * @param CommandInterface $command
     * @param string           $content
     *
     * @throws CentrifugoException
     *
     * @return array|null
     */
    private function decodeAndProcessResponseResult(CommandInterface $command, string $content): ?array
    {
        try {
            $data = \json_decode($content, true, 512, \JSON_THROW_ON_ERROR);
        } catch (\Exception) {
            throw new CentrifugoException('Centrifugo response payload is not a valid JSON');
        }

        $successfulCommand = true;
        $result = null;

        if (isset($data['error'])) {
            if (empty($this->centrifugoError)) {
                $this->centrifugoError = [
                    'message' => $data['error']['message'],
                    'code' => $data['error']['code'],
                ];
            }

            $result = $data;
            $successfulCommand = false;
        } elseif ($command instanceof ResultableCommandInterface) {
            $result = $data['result'];
        }

        if ($this->profilerEnabled) {
            $this->commandHistoryLogger->logCommand($command, $successfulCommand, $result);
        }

        return $result;
    }
}
