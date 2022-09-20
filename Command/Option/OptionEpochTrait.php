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
 * OptionEpochTrait.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
trait OptionEpochTrait
{
    protected string $epoch = '';

    /**
     * @param InputInterface $input
     */
    protected function initializeEpochOption(InputInterface $input): void
    {
        $epoch = $input->getParameterOption(['--epoch', '-ep'], null);

        if (\is_string($epoch) && !empty($epoch)) {
            $this->epoch = $epoch;
        }
    }
}
