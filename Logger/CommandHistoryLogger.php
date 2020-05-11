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

    /** @var int */
    private $requestsCount = 0;

    /** @var int */
    private $commandsCount = 0;

    /** @var int */
    private $successfulCommandsCount = 0;

    /** @var int */
    private $failedCommandsCount = 0;

    /**
     * @param CommandInterface $command
     * @param bool             $success
     * @param array|null       $result
     */
    public function logCommand(CommandInterface $command, bool $success, ?array $result): void
    {
        $this->commandHistory[] = [
            'command' => $command,
            'result' => $result,
            'success' => $success,
        ];

        ++$this->commandsCount;

        if ($success) {
            ++$this->successfulCommandsCount;
        } else {
            ++$this->failedCommandsCount;
        }
    }

    /**
     * Clear command history.
     */
    public function clearCommandHistory(): void
    {
        $this->commandHistory = [];
        $this->requestsCount = 0;
        $this->commandsCount = 0;
        $this->successfulCommandsCount = 0;
        $this->failedCommandsCount = 0;
    }

    /**
     * @return CommandInterface[]
     */
    public function getCommandHistory(): array
    {
        return $this->commandHistory;
    }

    /**
     * Increase requests count.
     */
    public function increaseRequestsCount(): void
    {
        ++$this->requestsCount;
    }

    /**
     * @return int
     */
    public function getRequestsCount(): int
    {
        return $this->requestsCount;
    }

    /**
     * @return int
     */
    public function getCommandsCount(): int
    {
        return $this->commandsCount;
    }

    /**
     * @return int
     */
    public function getSuccessfulCommandsCount(): int
    {
        return $this->successfulCommandsCount;
    }

    /**
     * @return int
     */
    public function getFailedCommandsCount(): int
    {
        return $this->failedCommandsCount;
    }
}
