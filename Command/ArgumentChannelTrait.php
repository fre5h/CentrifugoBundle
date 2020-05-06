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
 * ArgumentChannelTrait.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
trait ArgumentChannelTrait
{
    /** @var string */
    protected $channel;

    /**
     * @param InputInterface $input
     *
     * @throws InvalidArgumentException
     */
    protected function initializeChannelArgument(InputInterface $input): void
    {
        try {
            $channel = (string) $input->getArgument('channel');
            $this->centrifugoChecker->assertValidChannelName($channel);
            $this->channel = $channel;
        } catch (\Exception $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }
}
