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

use Fresh\CentrifugoBundle\Exception\CentrifugoException;
use Fresh\CentrifugoBundle\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * CentrifugoChecker.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class CentrifugoChecker
{
    /** @var int */
    private $channelMaxLength;

    /**
     * @param int $centrifugoChannelMaxLength
     */
    public function __construct(int $centrifugoChannelMaxLength)
    {
        $this->channelMaxLength = $centrifugoChannelMaxLength;
    }

    /**
     * @param string $channelName
     *
     * @throws InvalidArgumentException
     */
    public function assertValidChannelName(string $channelName): void
    {
        if (false === \mb_detect_encoding($channelName, 'ASCII', true)) {
            throw new InvalidArgumentException('Invalid channel name. Only ASCII symbols must be used in channel string.');
        }

        if (\strlen($channelName) > $this->channelMaxLength) {
            throw new InvalidArgumentException(\sprintf('Invalid channel name length. Maximum allowed length is %d.', $this->channelMaxLength));
        }
    }

    /**
     * @param ResponseInterface $response
     *
     * @throws CentrifugoException
     */
    public function assertValidResponseStatusCode(ResponseInterface $response): void
    {
        if (Response::HTTP_OK !== $response->getStatusCode()) {
            throw new CentrifugoException('Wrong status code for Centrifugo response');
        }
    }

    /**
     * @param ResponseInterface $response
     *
     * @throws CentrifugoException
     */
    public function assertResponseHeaders(ResponseInterface $response): void
    {
        $headers = $response->getHeaders(false);

        if (!isset($headers['content-type'])) {
            throw new CentrifugoException('Missing "content-type" header in Centrifugo response');
        }
    }

    /**
     * @param ResponseInterface $response
     *
     * @throws CentrifugoException
     */
    public function assertValidResponseContentType(ResponseInterface $response): void
    {
        $headers = $response->getHeaders(false);

        if (!\in_array('application/json', $headers['content-type'], true)) {
            throw new CentrifugoException('Unexpected content type for Centrifugo response');
        }
    }
}
