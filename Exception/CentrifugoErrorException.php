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

namespace Fresh\CentrifugoBundle\Exception;

use Fresh\CentrifugoBundle\Model\CommandInterface;

/**
 * CentrifugoErrorException.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class CentrifugoErrorException extends CentrifugoException
{
    public function __construct(
        private readonly CommandInterface $command,
        string $message = '',
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getCommand(): CommandInterface
    {
        return $this->command;
    }
}
