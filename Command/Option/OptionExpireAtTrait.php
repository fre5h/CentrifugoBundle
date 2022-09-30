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

namespace Fresh\CentrifugoBundle\Command\Option;

use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputInterface;

/**
 * OptionExpireAtTrait.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
trait OptionExpireAtTrait
{
    /** @var int|null */
    protected ?int $expireAt = null;

    /**
     * @param InputInterface $input
     *
     * @throws InvalidOptionException
     */
    protected function initializeExpireAtOption(InputInterface $input): void
    {
        /** @var int|null $expireAt */
        $expireAt = $input->getParameterOption('--expireAt', null);

        if (null !== $expireAt) {
            $expireAt = (int) $expireAt;
            if ($expireAt <= 0) {
                throw new InvalidOptionException('Option "--expireAt" should be a valid integer value greater than 0.');
            }

            $this->expireAt = $expireAt;
        }
    }
}
