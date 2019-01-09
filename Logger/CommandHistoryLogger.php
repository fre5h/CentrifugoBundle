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

namespace Fresh\CentrifugoBundle\Logger;

use Fresh\CentrifugoBundle\Model\BatchRequest;
use Fresh\CentrifugoBundle\Model\CommandInterface;

/**
 * CommandHistoryLogger.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class CommandHistoryLogger
{
    /** @var CommandInterface[] */
    private $commandHistory = [];

    /**
     * Clear command history.
     */
    public function clearCommandHistory(): void
    {
        $this->commandHistory = [];
    }

    /**
     * @return CommandInterface[]
     */
    public function getCommandHistory(): array
    {
        return $this->commandHistory;
    }

    /**
     * @param CommandInterface $command
     */
    public function logCommand(CommandInterface $command): void
    {
        if ($command instanceof BatchRequest) {
            foreach ($command->getCommands() as $singleCommand) {
                $this->commandHistory[] = $singleCommand;
            }
        } else {
            $this->commandHistory[] = $command;
        }
    }
}
