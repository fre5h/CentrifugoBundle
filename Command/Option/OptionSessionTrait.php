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
 * OptionSessionTrait.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
trait OptionSessionTrait
{
    protected string $session = '';

    /**
     * @param InputInterface $input
     */
    protected function initializeSessionOption(InputInterface $input): void
    {
        $session = $input->getParameterOption('--session', null);

        if (\is_string($session) && !empty($session)) {
            $this->session = $session;
        }
    }
}
