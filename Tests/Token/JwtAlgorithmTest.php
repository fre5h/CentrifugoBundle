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

namespace Fresh\CentrifugoBundle\Tests\Token;

use Fresh\CentrifugoBundle\Exception\InvalidArgumentException;
use Fresh\CentrifugoBundle\Token\JwtAlgorithm;
use PHPUnit\Framework\TestCase;

/**
 * JwtAlgorithmTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class JwtAlgorithmTest extends TestCase
{
    public function testInvalidAlgorithm(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(('Unsupported JWT algorithm: fake'));

        JwtAlgorithm::assertValidAlgorithm('fake');
    }

    /**
     * @param string $algorithm
     *
     * @doesNotPerformAssertions
     *
     * @testWith ["HS256"]
     *           ["RSA"]
     */
    public function testValidAlgorithm(string $algorithm): void
    {
        JwtAlgorithm::assertValidAlgorithm($algorithm);
    }
}
