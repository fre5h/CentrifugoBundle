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

use Fresh\CentrifugoBundle\Service\CentrifugoInterface;
use Symfony\Component\Console\Command\Command;

/**
 * AbstractCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
abstract class AbstractCommand extends Command
{
    /** @var CentrifugoInterface */
    protected $centrifugo;

    /**
     * @param CentrifugoInterface $centrifugo
     */
    public function __construct(CentrifugoInterface $centrifugo)
    {
        $this->centrifugo = $centrifugo;

        parent::__construct();
    }
}
