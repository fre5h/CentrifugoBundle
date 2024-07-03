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

namespace Fresh\CentrifugoBundle\User;

/**
 * CentrifugoUserMetaInterface.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
interface CentrifugoUserMetaInterface
{
    /**
     * @return mixed[]
     */
    public function getCentrifugoUserMeta(): array;
}
