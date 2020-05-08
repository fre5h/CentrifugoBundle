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
    /** @var CommandHistoryLogger */
    private $centrifugoLogger;

    /**
     * @param CommandHistoryLogger $commandHistoryLogger
     */
    public function __construct(CommandHistoryLogger $commandHistoryLogger)
    {
        $this->centrifugoLogger = $commandHistoryLogger;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Throwable $exception = null): void
    {
        $this->data = $this->centrifugoLogger->getCommandHistory();
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
        $this->data = [];
        $this->centrifugoLogger->clearCommandHistory();
    }

    /**
     * @return int
     */
    public function getCommandCount(): int
    {
        return \count($this->data);
    }

    /**
     * @return int
     */
    public function getRequestCount(): int
    {
        return \count($this->data);
    }

    /**
     * @return CommandInterface[]
     */
    public function getCommands(): array
    {
        return $this->data;
    }
}
