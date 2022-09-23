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
 * OptionExpiredTrait.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
trait OptionExpiredTrait
{
    protected bool $expired = false;

    /**
     * @param InputInterface $input
     */
    protected function initializeExpiredOption(InputInterface $input): void
    {
        $this->expired = $input->hasParameterOption('--expired');
    }
}
