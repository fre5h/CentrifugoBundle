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
 * ArgumentChannelsTrait.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
trait ArgumentChannelsTrait
{
    /** @var string[] */
    private array $channels;

    protected readonly CentrifugoChecker $centrifugoChecker;

    /**
     * @param InputInterface $input
     *
     * @throws InvalidArgumentException
     */
    protected function initializeChannelsArgument(InputInterface $input): void
    {
        try {
            /** @var string[] $channels */
            $channels = (array) $input->getArgument('channels');
            foreach ($channels as $channel) {
                $this->centrifugoChecker->assertValidChannelName($channel);
            }
            $this->channels = $channels;
        } catch (\Throwable $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }
}
