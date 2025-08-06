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
 * OptionBase64InfoTrait.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
trait OptionBase64InfoTrait
{
    protected string $base64info = ''; // @codingStandardsIgnoreLine

    /**
     * @param InputInterface $input
     *
     * @throws InvalidOptionException
     */
    protected function initializeB64InfoOption(InputInterface $input): void
    {
        $base64info = $input->getParameterOption('--base64info', null);

        if (\is_string($base64info) && !empty($base64info)) {
            $decodedData = \base64_decode($base64info, true);
            if (false === $decodedData) {
                throw new InvalidOptionException('Option "--base64info" should be a valid base64 encoded string.');
            }

            $this->base64info = $base64info;
        }
    }
}
