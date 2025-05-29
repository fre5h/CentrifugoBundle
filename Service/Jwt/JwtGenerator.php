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

namespace Fresh\CentrifugoBundle\Service\Jwt;

use Fresh\CentrifugoBundle\Token\JwtPayloadInterface;

/**
 * JwtGenerator.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class JwtGenerator
{
    private const HMAC_ALGORITHM = 'sha256';

    /**
     * @param string $centrifugoSecret
     */
    public function __construct(private readonly string $centrifugoSecret)
    {
    }

    /**
     * @param JwtPayloadInterface $payload
     *
     * @return string
     */
    public function generateToken(JwtPayloadInterface $payload): string
    {
        $headerPart = $this->buildHeaderPart();
        $payloadPart = $this->buildPayloadPart($payload);

        $headerPartEncoded = $this->base64EncodeUrlSafe($headerPart);
        $payloadPartEncoded = $this->base64EncodeUrlSafe($payloadPart);

        return \implode('.', [
            $headerPartEncoded,
            $payloadPartEncoded,
            $this->buildSignaturePart($headerPartEncoded, $payloadPartEncoded),
        ]);
    }

    /**
     * @return string
     */
    private function buildHeaderPart(): string
    {
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256',
        ];

        return $this->convertArrayToJsonString($header);
    }

    /**
     * @param JwtPayloadInterface $payload
     *
     * @return string
     */
    private function buildPayloadPart(JwtPayloadInterface $payload): string
    {
        return $this->convertArrayToJsonString($payload->getPayloadData());
    }

    /**
     * @param string $headerPartDecoded
     * @param string $payloadPartDecoded
     *
     * @return string
     */
    private function buildSignaturePart(string $headerPartDecoded, string $payloadPartDecoded): string
    {
        $data = $headerPartDecoded.'.'.$payloadPartDecoded;
        $hash = \hash_hmac(self::HMAC_ALGORITHM, $data, $this->centrifugoSecret, true);

        return $this->base64EncodeUrlSafe($hash);
    }

    /**
     * @param array $array
     *
     * @return string
     */
    private function convertArrayToJsonString(array $array): string
    {
        return \json_encode($array, \JSON_THROW_ON_ERROR);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private function base64EncodeUrlSafe(string $string): string
    {
        return \str_replace(['+', '/', '='], ['-', '_', ''], \base64_encode($string));
    }
}
