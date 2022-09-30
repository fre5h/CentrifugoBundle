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

use Fresh\CentrifugoBundle\Service\CentrifugoChecker;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;

/**
 * ArgumentChannelTrait.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
trait ArgumentChannelTrait
{
    protected string $channel;
    protected readonly CentrifugoChecker $centrifugoChecker;

    /**
     * @param InputInterface $input
     *
     * @throws InvalidArgumentException
     */
    protected function initializeChannelArgument(InputInterface $input): void
    {
        $channel = $input->getArgument('channel');

        if (null === $channel) {
            throw new InvalidArgumentException('Argument "channel" is required.');
        }

        if (!\is_string($channel)) {
            throw new InvalidArgumentException('Argument "channel" is not a string.');
        }

        try {
            $this->centrifugoChecker->assertValidChannelName($channel);
            $this->channel = $channel;
        } catch (\Throwable $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }
}
