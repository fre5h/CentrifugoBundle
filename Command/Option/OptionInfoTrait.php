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
 * OptionInfoTrait.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
trait OptionInfoTrait
{
    /** @var array<string, mixed> */
    protected array $info = [];

    /**
     * @param InputInterface $input
     *
     * @throws InvalidOptionException
     */
    protected function initializeInfoOption(InputInterface $input): void
    {
        $info = $input->getParameterOption('--info', null);

        if (\is_string($info)) {
            try {
                /** @var array $decodedData */
                $decodedData = json_decode($info, true, 512, \JSON_THROW_ON_ERROR);
                $this->info = $decodedData;
            } catch (\JsonException) {
                throw new InvalidOptionException('Option "--info" is not a valid JSON.');
            }
        }
    }
}
