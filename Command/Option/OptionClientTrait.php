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
 * OptionClientTrait.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
trait OptionClientTrait
{
    protected string $client = '';

    /**
     * @param InputInterface $input
     */
    protected function initializeClientOption(InputInterface $input): void
    {
        $client = $input->getParameterOption(['--client', '-c'], null);

        if (\is_string($client) && !empty($client)) {
            $this->client = $client;
        }
    }
}
