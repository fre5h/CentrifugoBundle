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
 * OptionOffsetTrait.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
trait OptionOffsetTrait
{
    /** @var int|null */
    protected ?int $offset = null;

    /**
     * @param InputInterface $input
     *
     * @throws InvalidOptionException
     */
    protected function initializeOffsetOption(InputInterface $input): void
    {
        /** @var int|null $offset */
        $offset = $input->getParameterOption('--offset', null);

        if (null !== $offset) {
            $offset = (int) $offset;
            if ($offset <= 0) {
                throw new InvalidOptionException('Option "--offset" should be a valid integer value greater than 0.');
            }

            $this->offset = $offset;
        }
    }
}
