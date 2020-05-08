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

/**
 * JwtPayload.
 *
 * @see https://centrifugal.github.io/centrifugo/server/authentication/#claims
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class JwtPayload extends AbstractJwtPayload
{
    /** @var string */
    private $subject;

    /** @var string[] */
    private $channels;

    /**
     * @param string      $subject
     * @param array       $info
     * @param int|null    $expirationTime
     * @param string|null $base64info
     * @param string[]    $channels
     */
    public function __construct(string $subject, array $info = [], ?int $expirationTime = null, ?string $base64info = null, array $channels = [])
    {
        $this->subject = $subject;
        $this->channels = $channels;

        parent::__construct($info, $expirationTime, $base64info);
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return array
     */
    public function getChannels(): array
    {
        return $this->channels;
    }

    /**
     * @return array
     */
    public function getPayloadData(): array
    {
        $data = [
            'sub' => $this->getSubject(),
        ];

        if (null !== $this->getExpirationTime()) {
            $data['exp'] = $this->getExpirationTime();
        }

        if (!empty($this->getInfo())) {
            $data['info'] = $this->getInfo();
        }

        if (null !== $this->getBase64Info()) {
            $data['b64info'] = $this->getBase64Info();
        }

        if (!empty($this->getChannels())) {
            $data['channels'] = $this->getChannels();
        }

        return $data;
    }
}
