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

namespace Fresh\CentrifugoBundle\Command;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;

/**
 * ArgumentDataTrait.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
trait ArgumentDataTrait
{
    /** @var mixed[] */
    protected array $data = [];

    /**
     * @param InputInterface $input
     *
     * @throws InvalidArgumentException
     */
    protected function initializeDataArgument(InputInterface $input): void
    {
        $data = $input->getArgument('data');

        if (!\is_string($data)) {
            throw new InvalidArgumentException('Argument "data" is not a string.');
        }

        try {
            $this->data = \json_decode($data, true, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new InvalidArgumentException('Argument "data" is not a valid JSON.');
        }
    }
}
