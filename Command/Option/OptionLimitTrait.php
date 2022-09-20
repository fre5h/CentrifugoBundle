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

namespace Fresh\CentrifugoBundle\Command\Option;

use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputInterface;

/**
 * OptionLimitTrait.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
trait OptionLimitTrait
{
    /** @var int|null */
    protected ?int $limit = null;

    /**
     * @param InputInterface $input
     *
     * @throws InvalidOptionException
     */
    protected function initializeLimitOption(InputInterface $input): void
    {
        /** @var int|null $limit */
        $limit = $input->getParameterOption(['--limit', '-l'], null);

        if (null !== $limit) {
            $limit = (int) $limit;
            if ($limit <= 0) {
                throw new InvalidOptionException('Option "--limit, -l" should be a valid integer value greater than 0.');
            }

            $this->limit = $limit;
        }
    }
}
