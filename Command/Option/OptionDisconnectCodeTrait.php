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
 * OptionDisconnectCodeTrait.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
trait OptionDisconnectCodeTrait
{
    /** @var int|null */
    protected ?int $disconnectCode = null;

    /**
     * @param InputInterface $input
     */
    protected function initializeDisconnectCodeOption(InputInterface $input): void
    {
        /** @var int|null $disconnectCode */
        $disconnectCode = $input->getParameterOption('--disconnectCode', null);

        if (null !== $disconnectCode) {
            $disconnectCode = (int) $disconnectCode;
            if ($disconnectCode <= 0) {
                throw new InvalidOptionException('Option "--disconnectCode" should be a valid integer value greater than 0.');
            }

            $this->disconnectCode = $disconnectCode;
        }
    }
}
