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

use Symfony\Component\Console\Input\InputInterface;

/**
 * OptionSkipHistoryTrait.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
trait OptionSkipHistoryTrait
{
    protected bool $skipHistory = false;

    /**
     * @param InputInterface $input
     */
    protected function initializeSkipHistoryOption(InputInterface $input): void
    {
        $this->skipHistory = $input->hasParameterOption(['--skipHistory', '-s']);
    }
}
