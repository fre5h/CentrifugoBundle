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

namespace Fresh\CentrifugoBundle\Token;

use JetBrains\PhpStorm\ArrayShape;

/**
 * JwtSubscriptionPayload.
 *
 * @see https://centrifugal.dev/docs/server/channel_token_auth
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class JwtSubscriptionPayload extends AbstractJwtPayload
{
    /**
     * @param string      $subject
     * @param string      $channel
     * @param array       $info
     * @param array       $meta
     * @param int|null    $expirationTime
     * @param string|null $base64info
     */
    public function __construct(private readonly string $subject, private readonly string $channel, array $info = [], array $meta = [], ?int $expirationTime = null, ?string $base64info = null)
    {
        parent::__construct($info, $meta, $expirationTime, $base64info);
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getChannel(): string
    {
        return $this->channel;
    }

    /**
     * @return array
     */
    #[ArrayShape([
        'sub' => 'string',
        'channel' => 'string',
        'b64info' => 'null|string',
        'info' => 'array',
        'meta' => 'array',
        'exp' => 'int|null',
    ])]
    public function getPayloadData(): array
    {
        $data = [
            'sub' => $this->getSubject(),
            'channel' => $this->getChannel(),
        ];

        if (null !== $this->getExpirationTime()) {
            $data['exp'] = $this->getExpirationTime();
        }

        if (!empty($this->getInfo())) {
            $data['info'] = $this->getInfo();
        }

        if (!empty($this->getMeta())) {
            $data['meta'] = $this->getMeta();
        }

        if (null !== $this->getBase64Info()) {
            $data['b64info'] = $this->getBase64Info();
        }

        return $data;
    }
}
