<?php
/*
 * This file is part of the FreshCentrifugoBundle
 *
 * (c) Artem Henvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Fresh\CentrifugoBundle\Token;

/**
 * JsonWebTokenGenerator.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class JsonWebTokenGenerator
{
    private const HMAC_ALGORITHM = 'sha256';

    private $secret;

    /**
     * @param string $centrifugoSecret
     */
    public function __construct(string $centrifugoSecret)
    {
        $this->secret = $centrifugoSecret;
    }

    /**
     * @param JsonWebTokenPayloadInterface $payload
     *
     * @return string
     */
    public function generateToken(JsonWebTokenPayloadInterface $payload): string
    {
        $headerPart = $this->buildHeaderPart();
        $payloadPart = $this->buildPayloadPart($payload);

        $headerPartEncoded = \base64_encode($headerPart);
        $payloadPartEncoded = \base64_encode($payloadPart);

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
            'alg' => JsonWebTokenHeader::ALGORITHM,
            'typ' => JsonWebTokenHeader::TYPE,
        ];

        return $this->convertArrayToJsonString($header);
    }

    /**
     * @param JsonWebTokenPayloadInterface $payload
     *
     * @return string
     */
    private function buildPayloadPart(JsonWebTokenPayloadInterface $payload): string
    {
        $data = [
            'sub' => $payload->getSubject(),
        ];

        if (null !== $payload->getExpirationTime()) {
            $data['exp'] = $payload->getExpirationTime();
        }

        if (!empty($payload->getInfo())) {
            $data['info'] = $payload->getInfo();
        }

        if (null !== $payload->getBase64Info()) {
            $data['b64info'] = $payload->getBase64Info();
        }

        return $this->convertArrayToJsonString($data);
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

        return \hash_hmac(self::HMAC_ALGORITHM, $data, $this->secret, true);
    }

    /**
     * @param array $array
     *
     * @return string
     */
    private function convertArrayToJsonString(array $array): string
    {
        $result = \json_encode($array);

        if (false === $result) {
            throw new \Exception(); // @todo Custom bundle exception
        }

        return $result;
    }
}
