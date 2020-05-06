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

use Fresh\CentrifugoBundle\Service\Centrifugo;
use Symfony\Component\Console\Command\Command;

/**
 * AbstractCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
abstract class AbstractCommand extends Command
{
    /** @var Centrifugo */
    protected $centrifugo;

    /**
     * @param Centrifugo $centrifugo
     */
    public function __construct(Centrifugo $centrifugo)
    {
        $this->centrifugo = $centrifugo;

        parent::__construct();
    }
}
