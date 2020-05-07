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

use Fresh\CentrifugoBundle\Exception\InvalidArgumentException;

/**
 * JwtAlgorithm.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class JwtAlgorithm
{
    public const HS256 = 'HS256';

    public const RSA = 'RSA';

    /**
     * @param string $algorithm
     *
     * @throws InvalidArgumentException
     */
    public static function assertValidAlgorithm(string $algorithm): void
    {
        $reflectionClass = new \ReflectionClass(self::class);
        $availableAlgorithms = $reflectionClass->getConstants();

        if (!\in_array($algorithm, $availableAlgorithms, true)) {
            throw new InvalidArgumentException(\sprintf('Unsupported JWT algorithm: %s', $algorithm));
        }
    }
}
