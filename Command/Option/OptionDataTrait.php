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
 * OptionDataTrait.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
trait OptionDataTrait
{
    /** @var array<string, mixed> */
    protected array $data = [];

    /**
     * @param InputInterface $input
     *
     * @throws InvalidOptionException
     */
    protected function initializeDataOption(InputInterface $input): void
    {
        $data = $input->getParameterOption('--data', null);

        if (\is_string($data)) {
            try {
                /** @var array $decodedData */
                $decodedData = \json_decode($data, true, 512, \JSON_THROW_ON_ERROR);
                $this->data = $decodedData;
            } catch (\JsonException) {
                throw new InvalidOptionException('Option "--data" is not a valid JSON.');
            }
        }
    }
}
