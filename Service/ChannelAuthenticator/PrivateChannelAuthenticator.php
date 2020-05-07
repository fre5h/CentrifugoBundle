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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * PrivateChannelAuthenticator.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class PrivateChannelAuthenticator
{
    /** @var CredentialsGenerator */
    private $credentialsGenerator;

    /** @var ChannelAuthenticatorInterface[]|iterable */
    private $channelAuthenticators;

    /**
     * @param CredentialsGenerator                     $credentialsGenerator
     * @param ChannelAuthenticatorInterface[]|iterable $channelAuthenticators
     */
    public function __construct(CredentialsGenerator $credentialsGenerator, iterable $channelAuthenticators)
    {
        $this->credentialsGenerator = $credentialsGenerator;
        $this->channelAuthenticators = $channelAuthenticators;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function authChannelsForUserFromRequest(Request $request): array
    {
        $channelsAuth = [];

        [$client, $channels] = $this->processRequest($request);

        foreach ($channels as $channel) {
            if (($token = $this->authChannelForUser($client, $channel)) && \is_string($token)) {
                $channelsAuth[] = [
                    'channel' => $channel,
                    'token' => $token,
                ];
            }
        }

        return ['channels' => $channelsAuth];
    }

    /**
     * @param string $client
     * @param string $channel
     *
     * @return string|null
     */
    private function authChannelForUser(string $client, string $channel): ?string
    {
        $token = null;

        $channelAuthenticator = $this->findAppropriateChannelAuthenticator($channel);
        if ($channelAuthenticator instanceof ChannelAuthenticatorInterface && $channelAuthenticator->hasAccessToChannel($channel)) {
            $token = $this->credentialsGenerator->generateJwtTokenForPrivateChannel($client, $channel);
        }

        return $token;
    }

    /**
     * @param string $channel
     *
     * @return ChannelAuthenticatorInterface|null
     */
    private function findAppropriateChannelAuthenticator(string $channel): ?ChannelAuthenticatorInterface
    {
        foreach ($this->channelAuthenticators as $channelAuthenticator) {
            if ($channelAuthenticator->supports($channel)) {
                return $channelAuthenticator;
            }
        }

        return null;
    }

    /**
     * @param Request $request
     *
     * @throws BadRequestHttpException
     *
     * @return array
     */
    private function processRequest(Request $request): array
    {
        $content = \json_decode((string) $request->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        if (!isset($content['client']) || !\is_string($content['client'])) {
            throw new BadRequestHttpException('Client must be set in request');
        }
        $result[] = $content['client'];

        if (!isset($content['channels']) || empty($content['channels'] || !\is_array($content['channels']))) {
            throw new BadRequestHttpException('Channels must be set in request');
        }
        $result[] = $content['channels'];

        return $result;
    }
}
