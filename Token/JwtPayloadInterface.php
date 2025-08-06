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
 * JwtPayloadInterface.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
interface JwtPayloadInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getPayloadData(): array;
}
