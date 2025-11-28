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
 * OptionBase64DataTrait.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
trait OptionBase64DataTrait
{
    protected string $base64data = ''; // @codingStandardsIgnoreLine

    /**
     * @param InputInterface $input
     *
     * @throws InvalidOptionException
     */
    protected function initializeB64DataOption(InputInterface $input): void
    {
        $base64data = $input->getParameterOption('--base64data', null);

        if (\is_string($base64data) && !empty($base64data)) {
            $decodedData = base64_decode($base64data, true);
            if (false === $decodedData) {
                throw new InvalidOptionException('Option "--base64data" should be a valid base64 encoded string.');
            }

            $this->base64data = $base64data;
        }
    }
}
