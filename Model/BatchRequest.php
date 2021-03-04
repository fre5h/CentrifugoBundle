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

namespace Fresh\CentrifugoBundle\Model;

use Fresh\CentrifugoBundle\Exception\UnexpectedValueException;

/**
 * BatchRequest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class BatchRequest implements CommandInterface
{
    /** @var string[] */
    private array $channels = [];

    /** @var CommandInterface[] */
    private array $commands = [];

    /**
     * @param CommandInterface[] $commands
     *
     * @throws UnexpectedValueException
     */
    public function __construct(array $commands = [])
    {
        foreach ($commands as $command) {
            if (!$command instanceof CommandInterface) {
                throw new UnexpectedValueException(\sprintf('Invalid command for batch request. Only instances of %s are allowed.', CommandInterface::class));
            }

            $this->addCommand($command);
        }
    }

    /**
     * @param CommandInterface $command
     */
    public function addCommand(CommandInterface $command): void
    {
        $this->commands[] = $command;
        $this->channels = \array_merge($this->channels, (array) $command->getChannels());
    }

    /**
     * @return CommandInterface[]
     */
    public function getCommands(): iterable
    {
        foreach ($this->commands as $command) {
            yield $command;
        }
    }

    /**
     * @return int
     */
    public function getNumberOfCommands(): int
    {
        return \count($this->commands);
    }

    /**
     * {@inheritdoc}
     */
    public function getChannels(): iterable
    {
        foreach ($this->channels as $channelName) {
            yield $channelName;
        }
    }

    /**
     * @return string
     */
    public function prepareLineDelimitedJson(): string
    {
        $serializedCommands = [];

        foreach ($this->getCommands() as $command) {
            $serializedCommands[] = \json_encode($command, \JSON_THROW_ON_ERROR);
        }

        if (!empty($serializedCommands)) {
            $json = \implode("\n", $serializedCommands);
        } else {
            $json = '{}';
        }

        return $json;
    }
}
