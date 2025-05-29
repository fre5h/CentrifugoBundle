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

namespace Fresh\CentrifugoBundle\Command\Argument;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;

/**
 * ArgumentUserTrait.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
trait ArgumentUserTrait
{
    protected string $user;

    /**
     * @param InputInterface $input
     *
     * @throws InvalidArgumentException
     */
    protected function initializeUserArgument(InputInterface $input): void
    {
        $user = $input->getArgument('user');

        if (!\is_string($user)) {
            throw new InvalidArgumentException('Argument "user" is not a string.');
        }

        $this->user = $user;
    }
}
