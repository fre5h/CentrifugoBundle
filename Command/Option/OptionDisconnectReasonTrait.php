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

use Symfony\Component\Console\Input\InputInterface;

/**
 * OptionDisconnectReasonTrait.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
trait OptionDisconnectReasonTrait
{
    protected string $disconnectReason = '';

    /**
     * @param InputInterface $input
     */
    protected function initializeDisconnectReasonOption(InputInterface $input): void
    {
        $disconnectReason = $input->getParameterOption('--disconnectReason', null);

        if (\is_string($disconnectReason) && !empty($disconnectReason)) {
            $this->disconnectReason = $disconnectReason;
        }
    }
}
