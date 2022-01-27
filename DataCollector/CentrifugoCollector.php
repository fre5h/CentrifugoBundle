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

namespace Fresh\CentrifugoBundle\DataCollector;

use Fresh\CentrifugoBundle\Logger\CommandHistoryLogger;
use Fresh\CentrifugoBundle\Model\CommandInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * CentrifugoCollector.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class CentrifugoCollector extends DataCollector
{
    private readonly CommandHistoryLogger $commandHistoryLogger;

    /**
     * @param CommandHistoryLogger $commandHistoryLogger
     */
    public function __construct(CommandHistoryLogger $commandHistoryLogger)
    {
        $this->commandHistoryLogger = $commandHistoryLogger;
        $this->reset();
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Throwable $exception = null): void
    {
        $this->data = [
            'command_history' => $this->commandHistoryLogger->getCommandHistory(),
            'requests_count' => $this->commandHistoryLogger->getRequestsCount(),
            'commands_count' => $this->commandHistoryLogger->getCommandsCount(),
            'successful_commands_count' => $this->commandHistoryLogger->getSuccessfulCommandsCount(),
            'failed_commands_count' => $this->commandHistoryLogger->getFailedCommandsCount(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'centrifugo';
    }

    /**
     * {@inheritdoc}
     */
    public function reset(): void
    {
        $this->data = [
            'command_history' => [],
            'requests_count' => 0,
            'commands_count' => 0,
            'successful_commands_count' => 0,
            'failed_commands_count' => 0,
        ];
        $this->commandHistoryLogger->clearCommandHistory();
    }

    /**
     * @return int
     */
    public function getCommandsCount(): int
    {
        return $this->data['commands_count'];
    }

    /**
     * @return int
     */
    public function getSuccessfulCommandsCount(): int
    {
        return $this->data['successful_commands_count'];
    }

    /**
     * @return int
     */
    public function getFailedCommandsCount(): int
    {
        return $this->data['failed_commands_count'];
    }

    /**
     * @return int
     */
    public function getRequestsCount(): int
    {
        return $this->data['requests_count'];
    }

    /**
     * @return CommandInterface[]
     */
    public function getCommandHistory(): array
    {
        return $this->data['command_history'];
    }
}
