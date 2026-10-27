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

namespace Fresh\CentrifugoBundle\Service\ChannelAuthenticator;

use Fresh\CentrifugoBundle\Service\Credentials\CredentialsGenerator;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * PrivateChannelAuthenticator.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class PrivateChannelAuthenticator
{
    /**
     * @param CredentialsGenerator                $credentialsGenerator
     * @param PrivateChannelAuthenticatorResolver $privateChannelAuthenticatorResolver
     */
    public function __construct(
        private readonly CredentialsGenerator $credentialsGenerator,
        private readonly PrivateChannelAuthenticatorResolver $privateChannelAuthenticatorResolver,
    ) {
    }

    /**
     * @param Request $request
     *
     * @return array{channels: list<array{channel: string, token: string}>}
     */
    #[ArrayShape(['channels' => 'array'])]
    public function authChannelsForClientFromRequest(Request $request): array
    {
        $authData = [];

        [$client, $channels] = $this->processRequest($request);

        foreach ($channels as $channel) {
            $token = $this->authChannelForClient($client, $channel, $request);

            if (\is_string($token)) {
                $authData[] = [
                    'channel' => $channel,
                    'token' => $token,
                ];
            }
        }

        return ['channels' => $authData];
    }

    /**
     * @param string  $client
     * @param string  $channel
     * @param Request $request
     *
     * @return string|null
     */
    private function authChannelForClient(string $client, string $channel, Request $request): ?string
    {
        $token = null;

        $channelAuthenticator = $this->privateChannelAuthenticatorResolver->findAppropriateChannelAuthenticator($channel, $request);
        if ($channelAuthenticator instanceof ChannelAuthenticatorInterface && $channelAuthenticator->hasAccessToChannel($channel, $request)) {
            $token = $this->credentialsGenerator->generateJwtTokenForPrivateChannel($client, $channel);
        }

        return $token;
    }

    /**
     * @param Request $request
     *
     * @return array{string, list<string>}
     *
     * @throws BadRequestHttpException
     * @throws \Exception
     */
    private function processRequest(Request $request): array
    {
        try {
            /** @var array<string, mixed> $content */
            $content = json_decode((string) $request->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new BadRequestHttpException('Invalid JSON.');
        }

        if (!isset($content['client']) || !\is_string($content['client'])) {
            throw new BadRequestHttpException('Client must be set in request.');
        }

        if (!isset($content['channels']) || !\is_array($content['channels']) || empty($content['channels'])) {
            throw new BadRequestHttpException('Channels must be set in request.');
        }

        $channels = [];

        foreach ($content['channels'] as $channel) {
            if (!\is_string($channel)) {
                throw new BadRequestHttpException('Channel must be a string.');
            }

            $channels[] = $channel;
        }

        return [$content['client'], $channels];
    }
}
