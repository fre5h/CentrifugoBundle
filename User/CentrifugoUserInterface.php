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

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * CentrifugoUserInterface.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
interface CentrifugoUserInterface extends UserInterface
{
    /**
     * @return string
     */
    public function getSubject(): string; // @todo Rethink methods

    /**
     * @return mixed[]
     */
    public function getInfo(): array;
}
