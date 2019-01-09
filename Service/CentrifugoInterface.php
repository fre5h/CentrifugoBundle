<?php
/*
 * This file is part of the FreshCentrifugoBundle
 *
 * (c) Artem Henvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Fresh\CentrifugoBundle\Service;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * CentrifugoInterface.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
interface CentrifugoInterface
{
    /**
     * @param UserInterface $user
     *
     * @return string
     */
    public function generateTokenForUser(UserInterface $user): string;

    /**
     * @return string
     */
    public function generateTokenForAnonymousUser(): string;
}
