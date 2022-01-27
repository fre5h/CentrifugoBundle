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

use Fresh\CentrifugoBundle\Logger\CommandHistoryLogger;
use Fresh\CentrifugoBundle\Model;
use Fresh\CentrifugoBundle\Model\CommandInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Centrifugo.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class Centrifugo implements CentrifugoInterface
{
    private readonly string $endpoint;
    private readonly string $apiKey;
    private readonly HttpClientInterface $httpClient;
    private readonly ResponseProcessor $responseProcessor;
    private readonly CommandHistoryLogger $commandHistoryLogger;
    private readonly CentrifugoChecker $centrifugoChecker;
    private readonly bool $profilerEnabled;

    /**
     * @param string               $endpoint
     * @param string               $apiKey
     * @param HttpClientInterface  $httpClient
     * @param ResponseProcessor    $responseProcessor
     * @param CommandHistoryLogger $commandHistoryLogger
     * @param CentrifugoChecker    $centrifugoChecker
     * @param Profiler|null        $profiler
     */
    public function __construct(string $endpoint, string $apiKey, HttpClientInterface $httpClient, ResponseProcessor $responseProcessor, CommandHistoryLogger $commandHistoryLogger, CentrifugoChecker $centrifugoChecker, ?Profiler $profiler)
    {
        $this->endpoint = $endpoint;
        $this->apiKey = $apiKey;
        $this->httpClient = $httpClient;
        $this->responseProcessor = $responseProcessor;
        $this->commandHistoryLogger = $commandHistoryLogger;
        $this->centrifugoChecker = $centrifugoChecker;
        $this->profilerEnabled = $profiler instanceof Profiler;
    }

    /**
     * {@inheritdoc}
     */
    public function publish(array $data, string $channel): void
    {
        $this->doSendCommand(new Model\PublishCommand($data, $channel));
    }

    /**
     * {@inheritdoc}
     */
    public function broadcast(array $data, array $channels): void
    {
        $this->doSendCommand(new Model\BroadcastCommand($data, $channels));
    }

    /**
     * {@inheritdoc}
     */
    public function unsubscribe(string $user, string $channel): void
    {
        $this->doSendCommand(new Model\UnsubscribeCommand($user, $channel));
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect(string $user): void
    {
        $this->doSendCommand(new Model\DisconnectCommand($user));
    }

    /**
     * {@inheritdoc}
     */
    public function presence(string $channel): array
    {
        return (array) $this->doSendCommand(new Model\PresenceCommand($channel));
    }

    /**
     * {@inheritdoc}
     */
    public function presenceStats(string $channel): array
    {
        return (array) $this->doSendCommand(new Model\PresenceStatsCommand($channel));
    }

    /**
     * {@inheritdoc}
     */
    public function history(string $channel): array
    {
        return (array) $this->doSendCommand(new Model\HistoryCommand($channel));
    }

    /**
     * {@inheritdoc}
     */
    public function historyRemove(string $channel): void
    {
        $this->doSendCommand(new Model\HistoryRemoveCommand($channel));
    }

    /**
     * {@inheritdoc}
     */
    public function channels(?string $pattern = null): array
    {
        return (array) $this->doSendCommand(new Model\ChannelsCommand($pattern));
    }

    /**
     * {@inheritdoc}
     */
    public function info(): array
    {
        return (array) $this->doSendCommand(new Model\InfoCommand());
    }

    /**
     * {@inheritdoc}
     */
    public function batchRequest(array $commands): array
    {
        return (array) $this->doSendCommand(new Model\BatchRequest($commands));
    }

    /**
     * @param CommandInterface $command
     *
     * @return array|null
     */
    private function doSendCommand(CommandInterface $command): ?array
    {
        foreach ($command->getChannels() as $channel) {
            $this->centrifugoChecker->assertValidChannelName($channel);
        }

        if ($command instanceof Model\BatchRequest) {
            $json = $command->prepareLineDelimitedJson();
        } else {
            $json = \json_encode($command, \JSON_THROW_ON_ERROR);
        }

        if ($this->profilerEnabled) {
            $this->commandHistoryLogger->increaseRequestsCount();
        }

        $response = $this->httpClient->request(
            Request::METHOD_POST,
            $this->endpoint,
            [
                'headers' => [
                    'Authorization' => 'apikey '.$this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'body' => $json,
            ]
        );

        return $this->responseProcessor->processResponse($command, $response);
    }
}
