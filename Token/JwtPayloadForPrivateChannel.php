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
 * JwtPayloadForPrivateChannel.
 *
 * @todo Set correct link
 *
 * @see https://centrifugal.github.io/centrifugo/server/private_channels/#claims
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class JwtPayloadForPrivateChannel extends AbstractJwtPayload
{
    /**
     * @param string      $client
     * @param string      $channel
     * @param array       $info
     * @param array       $meta
     * @param int|null    $expirationTime
     * @param string|null $base64info
     * @param bool|null   $eto
     */
    public function __construct(private readonly string $client, private readonly string $channel, array $info = [], array $meta = [], ?int $expirationTime = null, ?string $base64info = null, private readonly ?bool $eto = null)
    {
        parent::__construct($info, $meta, $expirationTime, $base64info);
    }

    /**
     * @return string
     */
    public function getClient(): string
    {
        return $this->client;
    }

    /**
     * @return string
     */
    public function getChannel(): string
    {
        return $this->channel;
    }

    /**
     * @return bool|null
     */
    public function isEto(): ?bool
    {
        return $this->eto;
    }

    /**
     * {@inheritdoc}
     */
    #[ArrayShape([
        'client' => 'string',
        'channel' => 'string',
        'eto' => 'bool|null',
        'b64info' => 'null|string',
        'info' => 'mixed',
        'meta' => 'mixed',
        'exp' => 'int|null',
    ])]
    public function getPayloadData(): array
    {
        $data = [
            'client' => $this->getClient(),
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

        if (null !== $this->isEto()) {
            $data['eto'] = $this->isEto();
        }

        return $data;
    }
}
