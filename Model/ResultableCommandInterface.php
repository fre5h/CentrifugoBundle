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

/**
 * ResultableCommandInterface.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
interface ResultableCommandInterface extends CommandInterface
{
    /**
     * @param array $data
     *
     * @return array
     */
    public function processResponse(array $data): array;
}
